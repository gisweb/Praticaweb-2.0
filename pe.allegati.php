<?php
include_once("login.php");
include_once("./lib/tabella_h.class.php");
include_once("./lib/tabella_v.class.php");
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$id=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('list');
$titolo=$_SESSION["TITOLO_$idpratica"];
$prot=$_REQUEST["protocollo"];
$data_prot = $_REQUEST["data_protocollo"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$config_file = sprintf("%s/%s",$tabpath,"allegati");
$dbh = utils::getDb();
$sql = "SELECT id as value, nome as option FROM pe.e_iter order by ordine,nome" ;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
<title>Allegati - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array('/form/pe.allegati'));
	utils::loadCss();
?>
<style>
    .ui-state-hover,.ui-state-active,.ui-state-focus{
        background:url("css/start/images/ui-bg_glass_45_0078ae_1x400.png");
        color:white;
    }
</style>
<script>
	var item = <?php echo json_encode($items);?>;
</script>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">

<?php
if (in_array($modo,Array("edit","new"))){
    include "./inc/inc.page_header.php";
    $tabella=new tabella_v($config_file,$modo);
    if ($modo=="new"){
        unset($_SESSION["ADD_NEW"]);
        $prot = $_REQUEST["protocollo"];
        $data_prot = $_REQUEST["data_protocollo"];
        $richiesta = $_REQUEST["prot_richiesta"];
        $data_richiesta = $_REQUEST["data_richiesta"];
        $tabella->set_dati(Array("prot_allegato"=>$prot,"data_prot_allegato"=>$data_prot,"data_richiesta"=>$data_richiesta,"prot_richiesta"=>$richiesta));
        //ob_start();
        
        //$tab = ob_get_contents();
        //ob_end_clean();
    }
    else{
        $tabella->set_dati("id=$id");
    }
    $tab = $tabella->edita(0);
    $page=<<<EOT
	<FORM id="frm_allegati" name="frm_allegati" method="post" action="praticaweb.php" enctype="multipart/form-data">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			<tr> 
				<td> 
			<!-- intestazione-->
					<H2 class="blueBanner">Scheda documento allegato</H2>
			<!-- fine intestazione-->
				</td>
			</tr>
			<tr> 
				<td> 
				<!-- contenuto-->
                    $tab
				<!-- fine contenuto-->
				</td>
			</tr>		  	 	
        </TABLE>	
        <input name="active_form" type="hidden" value="pe.allegati.php">	
        <input name="id" type="hidden" value="$id">
        <input name="mode" type="hidden" id="mode" value="$modo">				
        <input name="pratica" type="hidden" value="$idpratica">
</FORM>            
EOT;
    print $page; 
}
elseif ($modo=="view") {
    $tabella=new tabella_v($config_file,$modo);
    $tabella->set_dati("id=$id");
    $tabella->set_titolo("Scheda del documento","modifica",array());
    $tabella->elenco();
}    
else{
    $sql = "SELECT id,nome as opzione FROM pe.e_stati_allegato ORDER BY ordine";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute()){
        $options = $stmt->fetchAll();
        $opts[] = sprintf("<input type=\"radio\" name=\"allegati_state\" class=\"\" id=\"\" data-plugins=\"input-download-allegati\" value=\"%s\">%s</input><br/>","all","Tutti gli stati");
        for($i=0;$i<count($options);$i++){
            $optVal = $options[$i]["id"];
            $optLabel = $options[$i]["opzione"];
            $opts[] = sprintf("<input type=\"radio\" class=\"\" name=\"allegati_state\" data-plugins=\"input-download-allegati\" value=\"%s\">%s</input><br/>",$optVal,$optLabel);         
        }
        $radioHtml = implode("\n",$opts);
    }
    $sql="SELECT DISTINCT pratica,protocollo,data_protocollo,titolo,tipo FROM pe.elenco_allegati_pratica WHERE pratica=? ORDER BY 3";
    $stmt = $dbh->prepare($sql);
    $res = Array();
    if($stmt->execute(Array($idpratica))){
        $res = $stmt->fetchAll();
    }
    else{
        print_r($stmt->errorInfo());
    }
    
    for($i=0;$i<count($res);$i++){
        $r = $res[$i];
        list($prat,$prot,$data_prot,$titolo,$tipoRich)=$r;
        
        $tabella=new Tabella_h($config_file,"list");
        $num_allegati = $tabella->set_dati("coalesce(id,0)<>0 AND pratica=$idpratica AND protocollo='$prot' AND data_protocollo='$data_prot'::date");
        //print_array($tabella);
        $arrayData = ($tipoRich=="richiesta_integrazione")?(Array("prot_richiesta"=>$prot,"data_richiesta"=>$data_prot)):(Array("protocollo"=>$prot,"data_protocollo"=>$data_prot));
        $tabella->set_titolo($titolo,"nuovo",$arrayData);
        $tabella->get_titolo();
        if ($num_allegati) 
            $tabella->elenco();
        else
			 print "<p><b>Nessun Documento Allegato</b></p>";
    }
    $btn_download = <<<EOT
    <div id="div_download_allegati" style="margin-top:20px;margin-bottom:20px;style:display:none;" class="hidden">
        <button id = "btn_dialog_allegati" class="" data-plugins="dialog-allegati" data-pratica="$pratica" data-stato_allegato=""/>
    
        <div id="dialog-download" title="Seleziona quali allegati scaricare">
            $radioHtml
            <input type="hidden" id="pratica-download" value="$idpratica"/>
        </div>
    </div>        
    <script>
        var dialog = $("#dialog-download").dialog({
            autoOpen: false,
            height: 280,
            width: 350,
            modal: true,
            buttons: {
                "Scarica il file compresso": function(){
                    var pr = $('#pratica-download').val();
                    goToPratica('services/downloadAllegati.php',{'pratica':pr});

                },
                Cancel: function() {
                   dialog.dialog( "close" );
                }
            }
        });
        $("#btn_dialog_allegati").button({
           icon: "ui-icon-disk",
           iconPosition: "end",
           label : "Seleziona i file da scaricare"
        }).on('click',function() {
           dialog.dialog( "open" );
       });

    </script>
    
EOT;
if (in_array($_SESSION["USER_ID"],Array(1,100000,100001)))     echo $btn_download;
    $tabella_integrazione=new tabella_h("$tabpath/integrazioni.tab");
	$tabella_integrazione->set_titolo("Aggiungi nuova Integrazione","nuovo");
	$tabella_integrazione->get_titolo("pe.integrazioni.php");
}
?>
    
</body>
</html>    
    
    
    
    
    
    


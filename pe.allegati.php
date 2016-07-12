<?php
include_once("login.php");
include "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Allegati - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<style>
    .ui-state-hover,.ui-state-active,.ui-state-focus{
        background:url("css/start/images/ui-bg_glass_45_0078ae_1x400.png");
        color:white;
    }
</style>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php
    if ($modo=="edit"){
        $_SESSION["ADD_NEW"]=0;
        $iter=$_POST["iter"];
        $nomeiter=$_POST["nomeiter"];	
        include "./inc/inc.page_header.php";
        $tabella_allegati=new tabella_h("$tabpath/doc_allegati",$modo);
        $tabella_elenco=new tabella_h("$tabpath/doc_elenco",$modo);
        $dummytable=new tabella_h("buttons",'new');
        $num_allegati=$tabella_allegati->set_dati("pratica=$idpratica and iter=$iter and (allegato=1 or mancante=1)");
        $num_elenco=$tabella_elenco->set_dati("iter=$iter and id not in (select 'doc_'||documento::varchar as id from pe.allegati inner join pe.e_documenti on(e_documenti.id=documento) where iter=$iter and (allegato=1 or integrato=1 or mancante=1) and pratica=$idpratica)");

        $tabella_allegati->set_titolo($nomeiter);
        $tabella_elenco->set_titolo($nomeiter);
        $tabella_allegati->set_color("#728bb8","#FFFFFF",0,0);
                print <<<EOT
        <H2 class=blueBanner>Gestione allegati:&nbsp;$nomeiter</H2>
        <FORM method="POST" action="praticaweb.php">
            <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
                <TR> 
                    <TD>             
EOT;
        if (!$num_allegati && !$num_elenco){
            print <<<EOT
                        <h2>l'elenco dei documenti per la fase $nomeiter è vuoto. Inserire un elenco di documenti</h2><br>
                        <input name="azione" type="submit" class="hexfield" tabindex="14" value="Chiudi">
                        <input name="active_form" type="hidden" value="pe.allegati.php">
                        <input name="pratica" type="hidden" value="$idpratica">			
                    </TD>
                </TR>
            </TABLE>
EOT;
	}
        else{
            $jsActivate='';
            if ($num_allegati){
                $jsActivate="$('#accordion').accordion('activate',0); ";
                $tabella_allegati->elenco();
            }
            else
                print ("<p><b>Nessun Documento Allegato</b></p>");
            if ($num_elenco){
                print <<<EOT
                <div id="accordion" style="">
                    <h3><a href="#">Elenco Allegati</a></h3>
                    <div>
EOT;
                $tabella_elenco->elenco();
                print <<<EOT
                    </div>
                </div>
                <script>
                    $("#accordion").accordion({
                        collapsible: true,
                        disabled:false
                    });
                    $jsActivate
                </script>
EOT;
         }

                print <<<EOT
                    </TD>
                </TR>
            </TABLE>
            <input name="active_form" type="hidden" value="pe.allegati.php">
            <input name="pratica" type="hidden" value="$idpratica">  
EOT;
        print $dummytable->set_buttons();
        print "</FORM>";
    }
}    
else{// VISTA DATI

$tabella_allegati=new Tabella_h("$tabpath/doc_allegati");
$tabella_mancanti=new Tabella_h("$tabpath/doc_mancanti");
$tabella_mancanti->set_color("#FFFFFF","#FF0000",0,0);
$db=$tabella_allegati->get_db();
$sql = "SELECT DISTINCT protocollo,data_protocollo FROM pe.files WHERE pratica=$idpratica and coalesce(protocollo,'')<>''";
$db->sql_query($sql);
$res = $db->sql_fetchrowset();
?>
    <H2 class="blueBanner">Elenco documenti allegati alla pratica</H2>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		

<?php	
for($i=0;$i<count($res);$i++){
	$prot = $res[$i]["protocollo"];
	$data = $res[$i]["data_protocollo"];
        //Visualizzare solo quelli inerenti il tipo di pratica e opzioni (es se richiesta voltura)
        $num_allegati=$tabella_allegati->set_dati("protocollo='$prot' and pratica=$idpratica and (allegato=1 or integrato=1)");
        $num_mancanti=$tabella_mancanti->set_dati("pratica=$idpratica and mancante=1");
        $tabella_allegati->set_titolo("Elenco dei Documenti presentati il $data con Protocollo $prot","modifica");
        $tabella_allegati->set_tag($idpratica);
        //$tabella_mancanti->set_tag($idpratica);
        print <<<EOT
        <TR>
            <TD>
EOT;
        $tabella_allegati->get_titolo();
        if ($num_allegati) 
            $tabella_allegati->elenco();
        else
		// print "<p><b>Nessun Documento Allegato</b></p>";
		//if ($num_mancanti) 
        //    $tabella_mancanti->elenco();
        print <<<EOT
            <BR>
            </TD>
        </TR>
EOT;
}
	
// end for
    if ($tabella_allegati->editable) 
        print $tabella_allegati->elenco_stampe();

print "\t</TABLE>";

}
?>
</body>
</html>

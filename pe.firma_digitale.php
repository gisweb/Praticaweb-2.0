<?php
include_once("login.php");
include "./lib/tabella_h.class.php";
include "./lib/tabella_v.class.php";
$tabpath="pe";
//print_array($_REQUEST);

$dbh = utils::getDb();

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$today=date('j-m-y'); 
$titolo="Firma Digitale dei documenti";
$pr=new pratica($idpratica);

if (isset($_POST["azione"]) && $_POST["azione"]){
	//$id=$_POST["idrow"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		include "db/db.pe.firma_digitale.php";
	}
	$_SESSION["ADD_NEW"]=$_POST;				
}
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

//Imposto i permessi di default per il modulo
$_SESSION["PERMESSI"]=$_SESSION["PERMESSI_$idpratica"];

/**/
$banner = "Documenti da firmare / firmati digitalmente";
$formaction = "pe.firma_digitale.php";
?>

<html>
<head>
<title>Iter - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
	utils::loadJS("jquery.multi-select",'form/pe.firma_digitale');
	utils::loadCss(Array("multi-select",'firma_digitale'));
?>
</head>
<style>
.ms-container .ms-selectable li.ms-elem-selectable,
.ms-container .ms-selection li.ms-elem-selection{
  border-bottom: 1px #eee solid;
  padding: 2px 10px;
  color: rgb(65, 85, 120);
  font-family:Verdana, Geneva, Arial, sans-serif; 
  font-size: 10px;
}div.ms-container {width:800px;}
</style>
<body>
<?php
    if (($modo=="edit") or ($modo=="new") ){
        include "./inc/inc.page_header.php";
        $tabella=new tabella_v("$tabpath/firma_digitale",$modo);        
        $tabella->printTable = 0;
        if($Errors){
            $tabella->set_errors($Errors);
            $tabella->set_dati($_POST);
        }
        elseif($modo=="edit"){
            $id = $_REQUEST["id"];
            $sql = "SELECT iddocumento,iddocumento as id,idpratica as pratica,datainvio,idutentesrc,idutentedst,oggetto,lettura,firma,1 as protocolla,raggruppamentoprotocollo FROM firma_digitale.documenti WHERE iddocumento = ?;";
			$dbh = utils::getDb();
            $stmt = $dbh->prepare($sql);
            $stmt->execute(Array($id));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $tabella->set_dati($res);
            //print_r($tabella->array_dati);
        }
        
        $htmlTabella = $tabella->edita(0);
        $pagina = <<<EOT
    <H2 class='blueBanner'>$banner</H2>
    <FORM id="documenti" name="utenti" method="post" action="praticaweb.php">
            <!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">		
            <TR> 
                <TD> 
                <!-- contenuto-->
                    $htmlTabella
                <!-- fine contenuto-->
                </TD>
            </TR> 
        </TABLE>
        <input name="active_form" type="hidden" value="$formaction">
        <input name="mode" type="hidden" value="$modo"/>
        <input name="id" type="hidden" value="$id">
    </FORM>                
EOT;
        print $pagina;
?>
<script>
//console.log($("data-plugins='multi-select'"));
$("[data-plugins='multi-select']").multiSelect({});
</script>	    
<?php    
    }	
    else{
    //-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA DOCUMENTI DA FIRMARE >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
        $tabella=new tabella_h("$tabpath/firma_digitale",$modo);
        
        $sql = "SELECT iddocumento,iddocumento as id,idpratica as pratica,datainvio,idutentesrc,idutentedst,oggetto,lettura,firma,1 as protocolla,destinatarimail as destinatari,raggruppamentoprotocollo,pathdocumento FROM firma_digitale.documenti WHERE idpratica = ? AND pathdocumento ilike '{%}';";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(Array($idpratica));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
//		utils::debugAdmin($res);
        if (file_exists(LOCAL_LIB."wsclient.mail.class.php")){
            require_once LOCAL_LIB."wsclient.mail.class.php";
            for($i=0;$i<count($res);$i++){
                //DA FARE CHIAMATA A WS SIMONE PER ACCETTAZIONE E CONSEGNA PEC IN BASE A PRATICA_RAGGRUPPAMENTOPROTOCOLLO
                $objId = sprintf("%s_%s",$idpratica,$res[$i]["raggruppamentoprotocollo"]);
                $rr = wsClientMail::getInfoPEC($objId);
                if (count($rr)){
                    $acc = ($rr[0]["Accettazione"])?($rr[0]["Accettazione"]):(" --- ");
                    $cons = ($rr[0]["Consegna"])?($rr[0]["Consegna"]):(" --- ");
                }
                else{
                    $acc = " --- ";
                    $cons = " --- ";
                }    


                $res[$i]["consegna"] = $cons;
                $res[$i]["accettazione"] = $acc;
                $dd = json_decode($res[$i]["pathdocumento"],TRUE);
                $res[$i]["nomedocumento"] = $dd["nomedocumento"];
                $res[$i]["object"] = $dd["object"];

            }
        }
        else{
            for($i=0;$i<count($res);$i++){
                $dd = json_decode($res[$i]["pathdocumento"],TRUE);
                $res[$i]["nomedocumento"] = $dd["nomedocumento"];
                $res[$i]["object"] = $dd["object"];
            }
        }
        $tabella->set_dati($res,'list');	
        $nrec = count($res);
        $tabella->set_titolo($titolo,"nuovo");
        echo "\t\t<H2 class='blueBanner'>$banner</H2>";

?>			

        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
            <TR> 
                <TD> 
                <!-- contenuto-->
<?php

        $tabella->get_titolo();
        if ($nrec)	
            $tabella->elenco();
        else
            print ("<p><b>Nessun Documento nella coda di firma</b></p>");			
?>
                <!-- fine contenuto-->
                 </TD>
            </TR>
        </TABLE>
	
<?php
}
?>		

</body>
</html>

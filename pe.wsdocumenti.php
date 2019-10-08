<?php
include_once("login.php");
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";

$includeFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."init.pe.wsdocumenti.php";
$idpratica=$_REQUEST["pratica"];
$modo="list";
$titolo=$_SESSION["TITOLO_$idpratica"];

$tabpath="pe";
$dbh = utils::getDb();
$sql = "SELECT trim(format('%s%s%s',fascicolo,'.'||sub_fascicolo,'.'||sub_sub_fascicolo)) as fascicolo,coalesce(anno_fascicolo,anno::varchar) as anno,numero,fascicolo as nfasc,sub_fascicolo  FROM pe.avvioproc where pratica=?";
$stmt = $dbh->prepare($sql);
if($stmt->execute(Array($idpratica))){
        $d = $stmt->fetch();
        $fascicolo = $d[0];
        $anno = $d[1];
        $numero = $d[2];
        $nfasc = $d[3];
        $sub = $d[4];
//	$fascicolo = $stmt->fetchColumn(0);
//	$anno = $stmt->fetchColumn(1);
//	$numero = $stmt->fetchColumn(2);
} 

$sql = "SELECT username FROM admin.users_ads WHERE userid=?";
$stmt = $dbh->prepare($sql);
if($stmt->execute(Array($user))){
	$userAds = $stmt->fetchColumn(0);
}
//appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$wsData = Array();
//error_reporting E_ALL;



?>
<html>
<head>
<title>Ubicazione - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
    utils::loadJS(Array('init'));
    utils::loadCss();

?>
<script>

</script>
</head>
<body>
<?php
    include "./inc/inc.window.php";
	$tabella=new tabella_h("$tabpath/wsdocumenti","list");
	if (file_exists($includeFile) && $nfasc && $sub) require_once $includeFile;
    
    if (!$nfasc){
        $message = "Nessun fascicolo assegnato alla pratica.";
    }
    elseif (!$sub){
        $message = "Impossibile recuperare i dati senza sub fascicolo";
    }    
    
    else{
        $message = "Nessuna Documento registrato su Prisma";
    }
    //$numrec=$tabella->set_dati(Array($wsData));
    for($k=0;$k<count($wsData);$k++){
        $wsData[$k]["data"] = str_replace("/","-",$wsData[$k]["data"]);
        $wsData[$k]["tms"] = strtotime($wsData[$k]["data"]);
        //print_array($wsData[$k]);
    }

    $tabella->array_dati = $wsData;
    $tabella->num_record = count($wsData);
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <H2 class="blueBanner">Documenti registrati sul Protocollo Informatico</H2>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
      <TR> 
            <TD> 
			<!-- tabella nuovo inserimento-->
<?php
        $tabella->set_titolo("Elenco dei documenti registrati sul Protocollo informatico per la pratica con fascicolo $fascicolo dell'anno $anno");
        $tabella->get_titolo();
        if (count($wsData)) 
            $tabella->elenco();
        else
            print ("<p><b>$message</b></p>");
        print "<BR>";
	//print_array($tabella);
?>
<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
		
</body>


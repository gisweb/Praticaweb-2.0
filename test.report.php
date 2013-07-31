<?php
session_start();
$_SESSION['USER_ID']=1;
require_once "login.php";

/*require_once APPS_DIR."/lib/hiweb.report.class.php";

$db=gitReport::setDB();
$sql="SELECT DISTINCT pratica from pe.avvioproc WHERE not protocollo is null and tipo < 4000 and data_presentazione >='01/01/2012'::date and split_part(numero,'/',2) ~ '^([0-9]+)$'";
$ris=$db->fetchAll($sql);
foreach($ris as $val){
	$r=gitReport::recordA($val["pratica"]);
	print_array($r);
}
*/
require_once "./lib/stampe.word.class.php";
$doc=new wordDoc(109,33692);
$doc->createDoc();

?>
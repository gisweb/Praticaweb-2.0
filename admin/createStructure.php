<?php
session_start();
$_SESSION['USER_ID']=1;

require_once '../login.php';
//error_reporting(E_ALL);
require_once '../lib/pratica.class.php';

$dbconn->sql_query("SELECT DISTINCT pratica FROM pe.avvioproc where numero ilike '0%' limit 100");
$ris=$dbconn->sql_fetchlist('pratica');
for($i=0;$i<count($ris);$i++){
	$p=new pratica($ris[$i]);
	echo ($i+1).") Processata Pratica $p->numero\n";
}
?>

<?php
include_once("login.php");
//print_r($_REQUEST);
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$object=$_POST["obj"];
$id=$_POST["id"]; 
$azione=$_POST["action"];

$sql="select distinct nome_tavola as id from vincoli.zona where nome_vincolo= '$id'";
$db->sql_query($sql); 
$ris=$db->sql_fetchrowset();
for ($i=0;$i<count($ris);$i++) $out[]="{id:'".$ris[$i]["id"]."',name:'".$ris[$i]["id"]."'}";
header("Content-Type: text/plain; Charset=UTF-8");
 $debug="{id:'$object',values:[".implode(',',$out)."]}";
 print_debug($debug);
 echo $debug;
?>
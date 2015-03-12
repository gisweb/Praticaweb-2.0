<?php
include_once("login.php");
//print_r($_REQUEST);
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$object=$_POST["obj"];
$id=$_POST["id"]; 
$azione=$_POST["action"];

$sql="select distinct zona.nome_tavola as id from vincoli.zona left join vincoli.tavola on(zona.nome_tavola=tavola.nome_tavola) where zona.nome_vincolo= '$id' and tavola.cdu=1;";
$db->sql_query($sql); 
print_debug($sql);
$ris=$db->sql_fetchrowset();
for ($i=0;$i<count($ris);$i++) $out[]="{id:'".$ris[$i]["id"]."',name:'".$ris[$i]["id"]."'}";
header("Content-Type: text/plain; Charset=UTF-8");
 $debug="{id:'$object',values:[".implode(',',$out)."]}";
 print_debug($debug);
 echo $debug;
?>
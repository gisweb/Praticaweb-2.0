<?php
include "login.php";
function hexstr($hexstr) {
  $hexstr = str_replace(' ', '', $hexstr);
  $hexstr = str_replace('\x', '', $hexstr);
  $retstr = pack('H*', $hexstr);
  return $retstr;
}


$db = new sql_db('192.168.1.100','postgres','postgres',DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql="select oledoc from attdoc where not oledoc is null limit  10;";
$db->sql_query($sql);
$r=$db->sql_fetchrowset();
header("Content-Type: application/vnd.ms-word"); 
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("content-disposition: inline;filename=test.doc");
echo $r[0]['oledoc'];
//foreach($r as $v) print( "<p>".hexstr($v['oledoc'])."</p>");


?>
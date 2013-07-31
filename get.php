<?php
define("comune",true);
//require ("setting.php");
//require ("common.php");
require_once 'login.php';
$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
//$z="A";
$z = $_GET['zona'];
if (strlen($z)==2){	$z=substr($z,0,1); }

$cond="%Modifica di destinazione%";
$query_1 = "SELECT coefficiente FROM tabella_a WHERE tipo_intervento LIKE '".$cond."' && zona LIKE '%".$z."%'";
//$query_1 = "SELECT coefficiente FROM tabella_a WHERE tipo_intervento='";
//echo $query_1.'<br>';
$result = $db->sql_query($query_1);
$row_1 = $db->sql_fetchrow($result);
//echo "culo<br>";		
$coeff = $row_1["coefficiente"];

//echo $coeff;
echo $coeff;


?>		

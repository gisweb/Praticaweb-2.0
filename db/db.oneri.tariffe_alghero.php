<?
$azione=$_POST["azione"];
if (($azione=="Salva") || ($azione=="Elimina") ){
	include_once "./db/db.savedata.php";
}

$modo="list";

?>
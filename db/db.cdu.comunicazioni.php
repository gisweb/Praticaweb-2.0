<?php
$idpratica=$_REQUEST["pratica"];
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
	
}
	
$active_form="cdu.comunicazioni.php?pratica=$idpratica";
	
?>
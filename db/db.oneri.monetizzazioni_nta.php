<?php

if (in_array($_POST["azione"],Array("Salva","Elimina"))){
	include_once "./db/db.savedata.php";
	
}
	
$active_form="oneri.monetizzazioni_nta.php?pratica=$idpratica";
?>

<?php
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
}

$active_form="pe.conformita_ptcp.php?pratica=$idpratica";
?>
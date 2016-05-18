<?php

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") )
	include_once "./db/db.savedata.php";
if ($_REQUEST["mode"]=="new")
    $idpratica=$_SESSION["ADD_NEW"];	
	
$active_form="storage.invio.php?pratica=$idpratica";
	
?>
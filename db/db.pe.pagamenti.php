<?php

if (($_REQUEST["azione"]=="Salva") || ($_REQUEST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php"; 
}

$active_form="pe.pagamenti.php?pratica=$idpratica";

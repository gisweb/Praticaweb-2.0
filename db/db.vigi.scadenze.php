<?php

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
    include_once "./db/db.savedata.php";
    $modo="list";
}
	
$active_form="vigi.scadenze.php?pratica=$idpratica";
	
?>

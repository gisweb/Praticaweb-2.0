<?php

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
    include_once "./db/db.savedata.php";
}
	
$active_form="vigi.infrazioni.php?pratica=$idpratica&vigi=1";
	
?>

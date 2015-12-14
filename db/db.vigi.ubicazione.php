<?php

if ($_POST["azione"]=="Annulla"){
	$active_form.="?pratica=$idpratica";
}
else{
    require_once APPS_DIR."db/db.savedata.php";
    $active_form.="?pratica=$idpratica";
}
?>
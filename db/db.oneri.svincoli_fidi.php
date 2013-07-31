<?php
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
    if($_POST["azione"]=="Salva"){
        $idfido=$_REQUEST["fido"];
        $id=($_REQUEST["id"])?($_REQUEST["id"]):($_SESSION["ADD_NEW"]);
        $db=appUtils::getDB();
        $db->update("oneri.svincolo_fidi",Array("fido"=>$idfido),Array("id"=>$id));
    }
}
$active_form="oneri.fidi.php?pratica=$idpratica";
?>

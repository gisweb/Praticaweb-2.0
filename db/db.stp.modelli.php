<?php

$azione=  strtolower($_REQUEST["azione"]);
$modo=($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
if (in_array($azione, Array("salva","elimina"))){
    require_once 'db.savedata.php';
    $modo=($azione=='elimina')?("list"):("view");
    $id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
}
elseif($azione=="annulla"){
    $modo=($modo=='new')?("list"):("view");
}
?>

<?php

$azione=  strtolower($_REQUEST["azione"]);
$modo=($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
if (in_array($azione, Array("salva","elimina"))){
    require_once 'db.savedata.php';
    $modo=($azione=='elimina')?("list"):("view");
    $id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
    if ($_FILES['file']['tmp_name']){
        $fName=$_REQUEST['nome'];
        if (file_exists(DOCUMENTI. $fName)) $r=unlink (DOCUMENTI. $fName);
        if (!$r) echo "<p>Impossibile rimuovere il file ".MODELLI."$fName</p>";
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], DOCUMENTI. $fName)) { 
          print("***ERROR: Non è possibile copiare il file.<br />\n". MODELLI. $fName); 
	} 
    }
     $modo='list';
}
elseif($azione=="annulla"){
    $modo='list';
}
?>

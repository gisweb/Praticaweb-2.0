<?php

$azione=  strtolower($_REQUEST["azione"]);
$modo=($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
if (in_array($azione, Array("salva"))){
    if(!$_POST["nome"]){
        $_POST["nome"]=$_FILES['file']["name"];
        $_REQUEST["nome"]=$_FILES['file']["name"];
    }
 
    require_once 'db.savedata.php';
    $modo=($azione=='elimina')?("list"):("view");
    $id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
    if ($_FILES['file']['tmp_name']){
        $fName=$_REQUEST['nome'];
        if (file_exists(MODELLI. $fName)) $r=unlink (MODELLI. $fName);
        if (!$r) echo "<p>Impossibile rimuovere il file ".MODELLI."$fName</p>";
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], MODELLI. $fName)) { 
          print("***ERROR: Non è possibile copiare il file.<br />\n". MODELLI. $fName); 
	} 
    }
}
elseif($azione=="elimina"){
    require_once 'db.savedata.php';
    $modo="list";
    
}
elseif($azione=="annulla"){
    $modo=($modo=='new')?("list"):("view");
}
?>

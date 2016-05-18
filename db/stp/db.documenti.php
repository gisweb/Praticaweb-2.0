<?php

$azione=  strtolower($_REQUEST["azione"]);
$modo=($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$pr=new pratica($idpratica,$app);

if (in_array($azione, Array("salva","elimina"))){
    
    $modo=($azione=='elimina')?("list"):("view");
    $id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
    if ($azione=='elimina'){
        require_once 'db.savedata.php';
        $fName=$_REQUEST['file_doc'];
        $r=unlink ($pr->documenti. $fName);
        
    }
    elseif ($_FILES['file']['tmp_name']){
        $fName=($_REQUEST['file_doc'])?($_REQUEST['file_doc']):($_FILES['file']['name']);
        $_POST['file_doc']=$fName;
        if(!pathinfo($fName,PATHINFO_EXTENSION)){
            $ext=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
            $fName.=".$ext";
            $_POST['file_doc']=$fName;
        }
        require_once 'db.savedata.php';
        if (file_exists($pr->documenti. $fName)){
            $r=unlink ($pr->documenti. $fName);
            if (!$r) echo "<p>Impossibile rimuovere il file ".$pr->documenti."$fName</p>";
        }
        
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $pr->documenti. $fName)) { 
          print("***ERROR: Non è possibile copiare il file.<br />\n". $pr->documenti. $fName); 
	} 
    }
     $modo='list';
}
elseif($azione=="annulla"){
    $modo='list';
}
if ($is_cdu) $active_form="cdu.iter.php?pratica=$idpratica";
elseif($is_ce) $active_form="ce.iter.php?pratica=$idpratica";
elseif($is_vigi) $active_form="vigi.iter.php?pratica=$idpratica";
else
$active_form="stp.documenti.php?mode=$modo&pratica=$idpratica";
?>

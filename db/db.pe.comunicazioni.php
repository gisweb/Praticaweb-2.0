<?php

$localSave=DATA_DIR."praticaweb/db/db.pe.comunicazioni.before.php";
$idpratica=$_REQUEST["pratica"];
$action = $_REQUEST["azione"];


if (in_array($action,Array("Salva","Elimina","Invia"))){
    if ($_REQUEST["azione"]== "Invia") $_REQUEST["azione"]="Salva";
	include_once "./db/db.savedata.php";
	if (file_exists($localSave)){
		
	}
	if ($action == "Invia"){
                $id = ($_REQUEST["mode"]=="new")?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
		$protocollata = ($_REQUEST["protocollo"])?(1):(0);
		$inviata = ($_REQUEST["data_invio"])?(1):(0);
		require_once $localSave;
		
		
	}

	

}	
$active_form="pe.comunicazioni.php?pratica=$idpratica&mode=list";
	
?>

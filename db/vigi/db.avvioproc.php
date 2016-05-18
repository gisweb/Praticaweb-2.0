<?php
////creare un trigger sul db per la numerazione automatica
	//per ora calcolo qui il nuovo numero pratica senza controlli
	//CREARE UN TRIGGER CHE AGGIORNA PRATICA A ID NELLA TABELLA AVVIOPROC 
	//UTILIZZARE UNA TRANSAZIONE PER L'EREDITARIETA DEI DATI DELLA NUOVA PRATICA
	//se ho annullato esco
	if ($_POST["azione"]=="Annulla"){
		$active_form.="?pratica=$idpratica";
		return;
	}
	//se ho già inserito il record recupero l'IDpratica ed esco
	if (($_POST["mode"]=="new") && ($_SESSION["ADD_NEW"])){
		$idpratica=$_SESSION["ADD_NEW"];
		$ERRMSG= "Il record " . $_SESSION["ADD_NEW"] . " è già stato inserito! ";
		return;
	}
	
	//Modulo condiviso per la gestione dei dati
	include_once "./db/db.savedata.php";
	if ($_REQUEST["mode"]=="new"){
            $idpratica=$_SESSION["ADD_NEW"];
        }
	
	
	//aggiorno una pratica esistente
	$active_form.="?pratica=$idpratica";  
	//IN TUTTI I db.mioform.php risetto i parametri per active_form da passare all'iframe
	//$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
	
	if (file_exists(DATA_DIR."praticaweb/db/db.vigi.avvioproc.php")){
            require_once DATA_DIR."praticaweb/db/db.vigi.avvioproc.php";
       }
	
?>

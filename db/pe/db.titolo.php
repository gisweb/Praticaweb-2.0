<?php
//Inserimento automatico delle date di scadenza lavori quando viene inserita la data di notifica.
$azione=$_POST["azione"];
if (($azione=="Salva") || ($azione=="Elimina") ){
	include_once "./db/db.savedata.php";
	$tabella=$_POST["tabella"];
	$data=$_POST["data_rilascio"];
	
	//se non ho la data di notifica non salvo nulla
    
	$pr=new pratica($idpratica);
	$info=appUtils::getInfoPratica($idpratica);
	$resp=$info['resp_proc'];
	$dirigente=$info['dirigente'];
	
	if ($azione=='Elimina'){
		appUtils::delTransition($idpratica,null,'rt');
		appUtils::delTransition($idpratica,null,'art');
		appUtils::delTransition($idpratica,null,'com');
		$pr->removeTitolo();
	}
	if ($azione=='Salva' && $data){
		if ($_REQUEST['mode']=='new') {	//SALVATAGGIO NUOVO RECORD
			$pr->nuovoTitolo($data);								//SETTO NUOVO NUMERO DI TITOLO
			//appUtils::addTransition($idpratica,Array("codice"=>"rt","data"=>$data,"utente_in"=>$resp,"utente_fi"=>$dirigente));   // TITOLO RILASCIATO
			//appUtils::addTransition($idpratica,Array("codice"=>"art","data"=>$data,"utente_in"=>$resp,"utente_fi"=>$dirigente));  // IN ATTESA DI RITIRO TITOLO
			//$pr->setDateLavori($data);								//SETTO DATE LAVORI
			//$pr->setDateRateCM($data);								//SETTO SCADENZE RATE CORRISPETTIVO MONETARIO
			$pr->setDateRateOC($data);								//SETTO SCADENZE RATE ONERI CONCESSORI

		}
	}
}

$active_form="pe.titolo.php?pratica=$idpratica";
?>

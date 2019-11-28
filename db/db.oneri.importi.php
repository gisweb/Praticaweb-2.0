<?php
$menu_rate=120;
$menu_fidi=130;

if ($_POST["azione"]=="Salva"){
	include_once "./db/db.savedata.php";
	$pr=new pratica($idpratica);
	if ($_POST["config_file"]=="oneri/corrispettivo_monetario.tab"){
		$pr->setCM();
		$pr->setRateCM();
		//$db->sql_query($sql);
		if(!(int)$_REQUEST['fideiussione']) $pr->setFidiCM();
	}
	elseif($_POST["config_file"]=="oneri/totali.tab"){
		$pr->setOC();
		$rate=(isset($_POST["rateizzato"]) && $_POST["rateizzato"])?(1):(-1);
		$pr->setRateOC($rate);
        if (defined('MODELLO_RATEIZZAZIONE_ONERI') && MODELLO_RATEIZZAZIONE_ONERI){
            require_once LIB."stampe.word.class.php";
            $modello = MODELLO_RATEIZZAZIONE_ONERI;
            $form = "oneri.importi";
            
            ob_start();
            $doc=new wordDoc($modello,$idpratica);
            $a = $doc->createDoc();
            ob_end_clean();
            $data = Array(
                "pratica"=>$idpratica,
                "modello"=>$modello,
                "form"=>$form,
                'file_doc'=>$doc->docName,
                'file_pdf'=>$doc->docName
            );
            $r = appUtils::addDocumentoStampa($data);

        }
		//if(!$_REQUEST['fideiussione']) $pr->setFidiOC();
		//if (isset($_POST["fideiussione"])){
		//	$menu->add_menu($idpratica,$menu_fidi);		
		//}
		//else{
		//	$db->sql_query("delete from oneri.fidi where pratica=$idpratica;");
		//	$menu->remove_menu($idpratica,$menu_fidi);
		//}
	}
}

if ($_POST["azione"]=="Elimina"){
	include_once "./db/db.savedata.php";
	if ($_POST["config_file"]=="oneri/corrispettivo_monetario.tab"){
		$db->sql_query("delete from pe.iter where pratica=$idpratica and nota ilike 'Creato il documento <img src=\"images/pdf.png\" border=0 onclick=\"window.open%\">&nbsp;&nbsp;<a href=%>Calcolo Corrispettivo Monetario</a>'");
		
	}
	elseif($_POST["config_file"]=="oneri/oneri_concessori.tab"){
//VEDERE SE ELIMINARE TUTTI I CALCOLI, LE RATE I FIDI E I MENU
		$db->sql_query("delete from oneri.rate where pratica=$idpratica");
		//$db->sql_query("delete from oneri.fidi where pratica=$idpratica");
		//$menu->remove_menu($idpratica,$menu_rate);
		//$menu->remove_menu($idpratica,$menu_fidi);
		$db->sql_query("delete from pe.iter where pratica=$idpratica and nota ilike 'Creato il documento <img src=\"images/pdf.png\" border=0 onclick=\"window.open%\">&nbsp;&nbsp;<a href=%>Calcolo Oneri</a>'");
	}
	//SE NON CI SONO PIU' RATE ELIMINO IL MENU DELLE RATEIZZAZIONI
	$db->sql_query("SELECT count(*) as tot from oneri.rate WHERE pratica=$pr->pratica;");
	if(!$db->sql_fetchfield('tot')){
		$menu->remove_menu($idpratica,$menu_rate);
		$menu->remove_menu($idpratica,$menu_fidi);
	}
}

	
	
$active_form="oneri.importi.php?pratica=$idpratica";
?>

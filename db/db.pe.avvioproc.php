<?php
//creare un trigger sul db per la numerazione automatica
	//per ora calcolo qui il nuovo numero pratica senza controlli
	//CREARE UN TRIGGER CHE AGGIORNA PRATICA A ID NELLA TABELLA AVVIOPROC 
	//UTILIZZARE UNA TRANSAZIONE PER L'EREDITARIETA DEI DATI DELLA NUOVA PRATICA
	
	//se ho annullato esco
	if ($_POST["azione"]=="Annulla" || !isset($_POST["azione"])){
		$active_form.="?pratica=$idpratica";
		return;
	}
	//se ho già inserito il record recupero l'IDpratica ed esco
	if (($_POST["mode"]=="new") && ($_SESSION["ADD_NEW"])){
		$idpratica=$_SESSION["ADD_NEW"];
		$ERRMSG= "Il record " . $_SESSION["ADD_NEW"] . " è già stato inserito! ";
		return;
	}
	if($_REQUEST['mode']=='edit'){
		$prPrec=new pratica($idpratica);
		$db=$prPrec->db1;
		$pratPrec=$db->fetchAssoc("SELECT * FROM pe.avvioproc WHERE pratica=?",Array($idpratica));
        }
        
        if (file_exists(DATA_DIR."praticaweb/db/db.pe.avvioproc.before.php")){
            $dataprot = $_REQUEST["data_prot"];
            $prot = $_REQUEST["protocollo"];
            require_once DATA_DIR."praticaweb/db/db.pe.avvioproc.before.php";
            $_REQUEST["data_prot"]=$dataprot;
            $_REQUEST["protocollo"]=$prot;
        }
	//Modulo condiviso per la gestione dei dati
	include_once "./db/db.savedata.php";
	
	$d_resp=($_REQUEST['data_resp'])?($_REQUEST['data_resp']):("now");
	$d_respIT=($_REQUEST['data_resp_it'])?($_REQUEST['data_resp_it']):("now");
	$d_respIA=($_REQUEST['data_resp_ia'])?($_REQUEST['data_resp_ia']):("now");
	
	if ($_REQUEST["mode"]=="new"){
            $idpratica=$_SESSION["ADD_NEW"];

            $pr=new pratica($idpratica);
            $pr->addRecenti();
            $pr->nuovaPratica(Array("data_resp"=>$d_resp,"data_resp_it"=>$d_respIT,"data_resp_ia"=>$d_respIA));
            //numerazione automatica
            $data_presentazione=$_POST["data_presentazione"];
            $tipo_pratica=$_POST["tipo"];
            $resp_proc=$_POST['resp_proc'];	
            $menu->list_menu($idpratica,$_POST["tipo"]);
		
		
		
	}//fine sezione nuova pratica
	
	//aggiorno una pratica esistente
	elseif($_POST["mode"]=="edit"){
            $pr=new pratica($idpratica);
            //devo solo controllare se è stato cambiato il tipo di pratica: in questo caso aggiorno il menu
            $tipo=$_POST["tipo"];
            $oldtipo=$_POST["oldtipo"];
            if ($tipo!=$oldtipo)
                    $menu->change_menu($idpratica,$oldtipo,$tipo);
            $menu->add_menu($idpratica,60);

            if($_POST["oldnumero"] && $_POST["numero"] && $_POST["oldnumero"]!=$_POST["numero"]){
                //$mex = sprintf("<p>MOVE %s TO %s</p>",$prPrec->documenti,$pr->documenti);
                //echo $mex;
                rename($prPrec->documenti,$pr->documenti);
            }
            
		
            if($pratPrec['resp_proc']!=$pr->info["resp_proc"]) 
                    $pr->addTransition(Array('codice'=>'rardp',"utente_fi"=>$pr->info["resp_proc"],"data"=>$d_resp));	
            if($pratPrec['resp_it']!=$_REQUEST['resp_it'])
                    $pr->addTransition(Array('codice'=>'raitec',"utente_fi"=>$pr->info["resp_it"],"data"=>$d_respIT));	
            if($pratPrec['resp_ia']!=$_REQUEST['resp_ia']) 
                    $pr->addTransition(Array('codice'=>'raiamm',"utente_fi"=>$pr->info["resp_ia"],"data"=>$d_respIA));	
	}
	$db->sql_close();
	$active_form.="?pratica=$idpratica";  
	//IN TUTTI I db.mioform.php risetto i parametri per active_form da passare all'iframe
	//$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
	
	if (file_exists(DATA_DIR."praticaweb/db/db.pe.after.avvioproc.php")){
            require_once DATA_DIR."praticaweb/db/db.pe.after.avvioproc.php";
        }
	
?>

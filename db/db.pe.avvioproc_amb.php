<?//creare un trigger sul db per la numerazione automatica
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
		//echo $ERRMSG;
		return;
	}
		
	//Modulo condiviso per la gestione dei dati
	include_once "./db/db.savedata.php";
		
	if ($_POST["mode"]=="new"){
		$idpratica= $_SESSION["ADD_NEW"];
		$db->sql_query ("update pe.avvioproc set pratica=id where id=$idpratica");	
		
		//numerazione automatica per savona
		//$db->sql_query ("update pe.avvioproc set numero=protocollo || '/' || substr(date_part('year',data_presentazione),3,2) where id=$idpratica");				 
		
		//numerazione automatica
		$data_presentazione=$_POST["data_presentazione"];
		$tipo_pratica=$_POST["tipo"];
		if(!$_POST["numero"]){
			$sql="update pe.avvioproc set numero= pe.nuovo_numero('$data_presentazione'::date,$tipo_pratica) where pratica=$idpratica;";
			if (DEBUG) echo $sql;
			$db->sql_query ($sql);	
		}

		//Aggiungo il menù della nuova pratica alla tabella menu	
		$menu->list_menu($idpratica,$_POST["tipo"]);
		
		//inserisco i dati relativi ai riferimenti: 
		if ($_POST["refpratica"]){
			//esiste la pratica di riferimento importo tutti i dati della pratica di riferimento
			include ("db.pe.importa_pratica.php");
		}
		elseif ($_POST["riferimento"]){
			$newref=$_POST["riferimento"];
			$newref=addslashes($newref);
			//aggiungo il riferimento e lo assegno alla pratica
			$db->sql_query ("insert into pe.riferimenti (descrizione) values ('$newref')");
			$idref=$db->sql_nextid();
			$db->sql_query ("update pe.avvioproc set riferimento=$idref where pratica=$idpratica");
			//aggiungo i riferimenti territoriali inseriti: via e civico
			if($_POST["via"]){
				$sqlcampi="via";
				$sqlvalori="'".htmlentities(addslashes($_POST["via"]))."'";
				if($_POST["civico"]){
					$sqlcampi.=",civico";
					$sqlvalori.=",'".htmlentities(addslashes($_POST["civico"]))."'";
				}
				$sql="insert into pe.indirizzi (pratica,$sqlcampi,uidins,tmsins) values ($idpratica,$sqlvalori,".$_SESSION["USER_ID"].",".time().")"; 
				if(DEBUG) echo $sql;
				$db->sql_query($sql);
			}
			//aggiungo i riferimenti territoriali inseriti: catasto terreni
			if($_POST["ctfoglio"]){
				$sqlcampi="foglio";
				$sqlvalori="'".htmlentities(addslashes($_POST["ctfoglio"]))."'";
				if($_POST["ctsezione"]){
					$sqlcampi.=",sezione";
					$sqlvalori.=",'".htmlentities(addslashes($_POST["ctsezione"]))."'";
				}
				if($_POST["ctmappale"]){
					$sqlcampi.=",mappale";
					$sqlvalori.=",'".htmlentities(addslashes($_POST["ctmappale"]))."'";					
				}
				$sql="insert into pe.cterreni (pratica,$sqlcampi,uidins,tmsins) values ($idpratica,$sqlvalori,".$_SESSION["USER_ID"].",".time().")"; 
				if(DEBUG) echo $sql;
				$db->sql_query($sql);
			}
		}
	}//fine sezione nuova pratica
	
	//aggiorno una pratica esistente
	elseif($_POST["mode"]=="edit"){
		//devo solo controllare se è stato cambiato il tipo di pratica: in questo caso aggiorno il menu
		$tipo=$_POST["tipo"];
		$oldtipo=$_POST["oldtipo"];
		if ($tipo!=$oldtipo)
			$menu->change_menu($idpratica,$oldtipo,$tipo);
		$menu->add_menu($idpratica,60);
	}
	$db->sql_close();
	//IN TUTTI I db.mioform.php risetto i parametri per active_form da passare all'iframe
	//$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
	$active_form.="?pratica=$idpratica";
	
	
?>

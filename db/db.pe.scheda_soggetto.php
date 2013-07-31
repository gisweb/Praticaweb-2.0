<?
//la variabile ruolo contiene il ruolo corrente per il nominativo


	$id=$_POST["id"];
	$ruolo=(isset($_POST["ruolo"]))?($_POST["ruolo"]):(null);//lo utilizzo anche per sapere quale ruolo spostare tra le volture nel caso
	$modo=$_POST["mode"];
	if(substr($ruolo,0,1)=='v') $ruolo=substr($ruolo,1);
	
	if ($_POST["azione"]=="Salva"){
		include_once "./db/db.savedata.php";
		if($modo=="new") $id=$_SESSION["ADD_NEW"];
		$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
	}
	
	elseif ($_POST["azione"]=="Annulla"){
		//se annullo da nuovo soggetto torno ad elenco
		if ($modo=="edit")
			$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
		else
			$active_form="pe.elenco_soggetti.php?pratica=$idpratica&id=$id";
	}
		
	elseif (ereg("Sposta",$_POST["azione"])){
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
		$sql="update pe.soggetti set $ruolo=-1 where id=$id;";		
		if (DEBUG) echo $sql;
		$db->sql_query ($sql);
		$active_form="pe.elenco_soggetti.php?pratica=$idpratica";
	}

	elseif ($_POST["azione"]=="Elimina"){
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
		$sql="update pe.soggetti set $ruolo=0 where id=$id;
				  delete from pe.soggetti where proprietario=0 and richiedente=0 and concessionario=0 
				  and progettista=0 and direttore=0 and esecutore=0 and id=$id;";
		$db->sql_query ($sql);
		$active_form="pe.elenco_soggetti.php?pratica=$idpratica";
	}

?>

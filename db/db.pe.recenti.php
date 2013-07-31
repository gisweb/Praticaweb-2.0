<?
//Gestione dell'elenco delle pratiche recenti 
//quando apro una nuova pratica aggiorno la tabella
//già che ci sono metto in sessione l'informazione legata a titolo della pagina (numero pratica .. del ...)

	$userid=$_SESSION["USER_ID"];
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");
	$sql="select pratica from pe.recenti where utente=$userid and pratica=$idpratica";
	$db->sql_query ($sql);
	$in_recenti=$db->sql_numrows();
	if($in_recenti==0){// se sono minori di 10 aggiungo altimenti aggiorno
		$sql="select pratica from pe.recenti where utente=$userid";
		$db->sql_query ($sql);
		$row =$db->sql_fetchrow();
		$nrec=$db->sql_numrows();
		if ($nrec >11)
			$sql="update pe.recenti set pratica=$idpratica,data=".time()." where utente=$userid and pratica=".$row["pratica"];
		else
			$sql="insert into pe.recenti (pratica,utente,data) values ($idpratica,$userid,".time().")";		
	}else//aggiorno la data di accesso
		$sql="update pe.recenti set data=".time()." where utente=$userid and pratica=$idpratica";
		
	$db->sql_query ($sql);
	//Aggiorno titolo della pagina
	// Da vedere se mettere tutto su una query
	$sql="select * from pe.elenco_pratiche where pratica=$idpratica";
	$db->sql_query ($sql);
	$row =$db->sql_fetchrow();
	$titolo="Pratica n. ".$row["numero"]." del ".$row["data_presentazione"] ." ".$row["tipopratica"];
	if($row["titolo"])
		$titolo.=" n. ".$row["titolo"]." del ".$row["data_rilascio"];
	$_SESSION["TITOLO_$idpratica"]=$titolo;
	$db->sql_close();	
	?>
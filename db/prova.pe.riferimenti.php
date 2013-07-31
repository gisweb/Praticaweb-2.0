<?
	//se ho passato una pratica da elenco ho finito
	if (isset($_POST["refpratica"])){
		$flagOK=1;
		return;
	}
		
	
	$self=$_SERVER["PHP_SELF"];
	$numeroprat=$_POST["numero"];
	$titolo=$_POST["titolo"];
	$via=$_POST["via"];
	$civico=$_POST["civico"];
	$ctsezione=$_POST["ctsezione"];
	$ctfoglio=$_POST["ctfoglio"];
	$ctmappale=$_POST["ctmappale"];
	$cusezione=$_POST["cusezione"];
	$cufoglio=$_POST["cufoglio"];
	$cumappale=$_POST["cumappale"];
	$riferimento=$_POST["riferimento"];
	
	
	if ($via){
		$sqlindi="indirizzi.via ilike '%$via%'";
		if ($civico)
			$sqlindi.=" and indirizzi.civico='$civico'";
		$sqlindi="select pratica from indirizzi where (".$sqlindi.")";		
		if ($sqlunion) $sqlunion.=" union ";
		$sqlunion.=$sqlindi;
	}
	
	if (($ctfoglio) or ($ctsezione)){
		if ($ctsezione) $sqlct="cterreni.sezione='$ctsezione' and ";
		if ($ctfoglio) $sqlct.="cterreni.foglio='$ctfoglio' and ";
		if ($ctmappale) $sqlct.="cterreni.mappale='$ctmappale' and ";
		$sqlct="select pratica from cterreni where (".substr($sqlct,0,strlen($sqlct)-4) .")";
		if ($sqlunion) $sqlunion.=" union ";
		$sqlunion.=$sqlct;		
	}
	//se non ho filtro su elemento territoriale 
	if($sqlunion) $flag_terr=1;//Ho già filtrato su un elemento territoriale
	if(($sqlunion)&&($riferimento)) $flagOK=1;
	
	if ($step==2)  return;
	
	
	if ($numeroprat) $sqlprat="avvioproc.numero='$numeroprat'";
	if ($riferimento){
		if($sqlprat) $sqlprat.=" or ";
		$sqlprat.="riferimenti.descrizione ilike '%$riferimento%'";
	}
	
	if ($titolo){
		if ($sqlunion) $sqlunion.=" union ";
		$sqlunion.="select pratica from titolo where titolo='$titolo'";
	}
	
	if (($cufoglio) or ($cusezione)){
		if ($cusezione) $sqlcu="curbano.sezione='$cusezione' and ";
		if ($cufoglio) $sqlcu.="curbano.foglio='$cufoglio' and ";
		if ($cumappale) $sqlcu.="curbano.mappale='$cumappale' and ";
		$sqlcu="select pratica from curbano where (".substr($sqlcu,0,strlen($sqlcu)-4) .")";
		if ($sqlunion) $sqlunion.=" union ";
		$sqlunion.=$sqlcu;
	}
		
	if ($sqlprat.$sqlunion){
		$sql="select riferimenti.id,riferimenti.descrizione,avvioproc.pratica from riferimenti,avvioproc where avvioproc.riferimento=riferimenti.id and avvioproc.riferimento<>0";
		if ($sqlprat) $sql.=" and ($sqlprat)";
		if ($sqlunion) $sql.=" and pratica in ($sqlunion)";
		$sql.=" order by riferimenti.descrizione, avvioproc.data_presentazione;";
		echo $sql;
		$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase $dbtype");
		$db->sql_query ($sql);
		$riferimenti = $db->sql_fetchrowset();
		$nrif= $db->sql_numrows();
	}
	else{
		$noquery=1;
	}
	

	
	?>
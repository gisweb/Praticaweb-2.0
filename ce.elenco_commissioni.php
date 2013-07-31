<? 
	for($i=$offset;$i<$prat_max;$i++){
		$idcomm=$elenco_pratiche[$i];
		 
		$sql="select * from ce.comm_edilizia where pratica=$idcomm";   // seleziono la commissione
		$db->sql_query ($sql); 
		$dati_commissione = $db->sql_fetchrow();												//relazione 1 a 1
		$sql="select nominativo from ce.membri_commissione where commissione=$idcomm";
		$db->sql_query($sql);	// seleziono i membri della commissione
		$elenconomi=$db->sql_fetchrowset();																//relazione 1 a molti
		foreach($elenconomi as $val) $elenco_membri.=$val["nominativo"]." - ";							// creo stringa dei partecipanti
		$elenco_membri=substr($elenco_membri,0,strlen($elenco_membri)-3);
		//$host=$_SERVER["HTTP_HOST"];
             
              if ($_REQUEST["comm_paesaggio"]=="1"){
                 $url="praticaweb.php?pratica=$idcomm&comm_paesaggio=1";
                 $titolo=$dati_commissione["tipo"]." del ".$dati_commissione["data_convocazione"];
              }
              else if ($_REQUEST["comm"]=="1"){
                 $url="praticaweb.php?pratica=$idcomm&comm=1";
                 $titolo=$dati_commissione["tipo"]." del ".$dati_commissione["data_convocazione"];
                 	
              }	

		//RISULTATI DELLA RICERCA
		if($chk or $modo=="cancella")																// modalitÃ  di cancellazione con checkbox per cancellare
			print("<H2><input type=\"checkbox\" value=\"$idcomm\" name=\"ref[$idcomm]\">$titolo</H2>");
		else																						// modalitÃ  di normale con link per andare alla commissione
			print ("<H2><a href=\"javascript:NewWindow('$url','Praticaweb',0,0,'yes')\">$titolo</a></H2>");
		//Scrittura delle info sulla commissione
				
		print ("Sede: ".$dati_commissione["sede"]."<br>");
		print ("Data convocazione: ".$dati_commissione["data_convocazione"]."<br>");
		print ("Orario Convocazione: ".$dati_commissione["ora_convocazione"]."<br>");
		print ("Membri della commissione: $elenco_membri<br>");
		print("<img height=1 src=\"images/gray_light.gif\" width=\"100%\"  vspace=1><BR> ");
		unset($dati_commissione);
		unset($elenco_membri);
              unset($titolo);

	}
?>
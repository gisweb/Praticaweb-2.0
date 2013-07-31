<?
include_once "./lib/date_format.php";
$nomi_richiedenti='';
$nomi_progettisti='';
$elenco_indirizzi='';
$elenco_terreni='';
$elenco_urbano='';
for($i=$offset;$i<$prat_max;$i++){
	$idpratica=$elenco_pratiche[$i];//['pratica'];
	$sql="select * from pe.elenco_pratiche where pratica=$idpratica";
	$db->sql_query ($sql);
	$dati_pratica = $db->sql_fetchrow();
	
	$sql="select * from pe.elenco_soggetti where pratica=$idpratica and (richiedente=1 or progettista=1)";
	$db->sql_query ($sql);
	$nrec=$db->sql_numrows();
	$soggetti = $db->sql_fetchrowset();	
	for($j=0;$j<$nrec;$j++){
		if($soggetti[$j]["richiedente"]==1){
			if($nomi_richiedenti) $nomi_richiedenti.=" - ";
			$nomi_richiedenti.=$soggetti[$j]["soggetto"];
		}
	}
	for($j=0;$j<$nrec;$j++){
		if($soggetti[$j]["progettista"]==1){
			if($nomi_progettisti) $nomi_progettisti.=" - ";
			$nomi_progettisti.=$soggetti[$j]["soggetto"];
		}
	}

	$sql="select * from pe.indirizzi where pratica=$idpratica";
	$db->sql_query ($sql);
	$indirizzi = $db->sql_fetchrowset();
	$nrec=$db->sql_numrows();		
	for($j=0;$j<$nrec;$j++){					
		if($elenco_indirizzi) $elenco_indirizzi.=" - ";	
		$elenco_indirizzi.=$indirizzi[$j]["via"]." ".$indirizzi[$j]["civico"];
	}
	
	$sql="select * from pe.cterreni where pratica=$idpratica";
	$db->sql_query ($sql);
	$terreni = $db->sql_fetchrowset();
	$nrec=$db->sql_numrows();		
	for($j=0;$j<$nrec;$j++){				
		if($elenco_terreni) $elenco_terreni.=" - ";			
		$elenco_terreni.="Foglio ".$terreni[$j]["foglio"]." Mappale ".$terreni[$j]["mappale"];
	}	
	
     $sql="select * from pe.curbano where pratica=$idpratica";
	$db->sql_query ($sql);
	$urbano = $db->sql_fetchrowset();
	$nrec=$db->sql_numrows();		
	for($j=0;$j<$nrec;$j++){				
		if($elenco_urbano) $elenco_urbano.=" - ";			
		$elenco_urbano.="Foglio ".$urbano[$j]["foglio"]." Mappale ".$urbano[$j]["mappale"];
	}	

	$url="praticaweb.php?pratica=$idpratica";
	$titolo="Pratica n. ".$dati_pratica["numero"]." del ".gw_date_format($dati_pratica["data_presentazione"]) ." ".$dati_pratica["tipopratica"];
	if($dati_pratica["titolo"])
		$titolo.=" n. ".$dati_pratica["titolo"]." del ".gw_date_format($dati_pratica["data_rilascio"]);
	//RISULTATI DELLA RICERCA
	if(isset($chk))
		print("<H2><input type=\"checkbox\" value=\"$idpratica\" name=\"ref[$idpratica]\">$titolo</H2>");
	else
		print ("<H2><a href=\"javascript:NewWindow('$url','Praticaweb',0,0,'yes')\">$titolo</a></H2>");
	print ("<p>Oggetto:".$dati_pratica["oggetto"]."<br>");
	print ("Richiedenti: $nomi_richiedenti<br>");
	print ("Progettista: $nomi_progettisti<br>");
	print ("Ubicazione: $elenco_indirizzi<br>");
	print ("Catasto Terreni: $elenco_terreni<br>");
     print ("Catasto Urbano: $elenco_urbano</p>");
	print("<img height=1 src=\"images/gray_light.gif\" width=\"100%\"  vspace=1><BR> ");
	unset($nomi_richiedenti);
	unset($nomi_progettisti);
	unset($elenco_indirizzi);
	unset($elenco_terreni);
     unset($elenco_urbano);

}
?>

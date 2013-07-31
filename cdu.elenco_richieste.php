<?
include_once "./lib/date_format.php";
for($i=$offset;$i<$prat_max;$i++){
	$idpratica=$elenco_pratiche[$i];//['pratica'];
	$sql="select * from cdu.richiesta where pratica=$idpratica";
	$db->sql_query ($sql);
	$dati_richiesta = $db->sql_fetchrow();
			
	$url="praticaweb.php?pratica=$idpratica&cdu=1";
	$titolo="Certificato di Destinazione Urbanistica prot. n. ".$dati_richiesta["protocollo"]." del ".$dati_richiesta["data"];
	//RISULTATI DELLA RICERCA
	if($chk)
		print("<H2><input type=\"checkbox\" value=\"$idpratica\" name=\"ref[$idpratica]\">$titolo</H2>");
	else
		print ("<H2><a href=\"javascript:NewWindow('$url','Praticaweb',0,0,'yes')\">$titolo</a></H2>");
	
	print ("<p>Richiedente: ".$dati_richiesta["richiedente"]."<br>");
	print ("Titolo richiedente: ".$dati_richiesta["titolo"]."<br>");
	print ("Indirizzo: ".$dati_richiesta["indirizzo"]."</p>");
	print("<img height=1 src=\"images/gray_light.gif\" width=\"100%\"  vspace=1><BR> ");
}
?>

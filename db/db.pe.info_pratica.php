<?
//DA VEDERE SE SOSTITUIRE TUTTO CON UN A QUERY DI UNIONE

		$sql="select * from pe.elenco_pratiche where pratica=$idpratica";
		$db->sql_query ($sql);
		$dati_pratica = $db->sql_fetchrowset();
		$sql="select soggetto from pe.elenco_soggetti where pratica=$idpratica and richiedente=1";
		$db->sql_query ($sql);
		$richiedenti = $db->sql_fetchrowset();
		$sql="select soggetto from pe.elenco_soggetti where pratica=$idpratica and progettista=1";
		$db->sql_query ($sql);
		$progettisti = $db->sql_fetchrowset();
		$sql="select via,civico from pe.indirizzi where pratica=$idpratica";
		$db->sql_query ($sql);
		$indirizzi = $db->sql_fetchrowset();
		$sql="select sezione,foglio,mappale,sub from pe.cterreni where pratica=$idpratica";
		$db->sql_query ($sql);
		$terreni = $db->sql_fetchrowset();
		//DA SOSTITUIRE IN CASO DI VALORE MESSO NELLA QUERY
		$infopratica="Pratica ".$dati_pratica[0]["numero"]." ".$dati_pratica[0]["tipopratica"] ." ".$dati_pratica[0]["titolo"];
		print("
		<TR>
			<TD valign=\"top\" width=\"10\"><input type=\"radio\" value=\"".$dati_pratica[0]["pratica"]."\" name=\"refpratica\" onclick=\"set_info('$infopratica','$infogruppo')\"></TD>
			<TD>");
//aggiungere piano scala ......			
		print ("<p><b>$infopratica</b><br>");
		print ("Oggetto:".$dati_pratica[0]["oggetto"]."<br>");
		print ("Richiedenti:".$richiedenti[0]["soggetto"]."<br>");
		print ("Progettista:".$progettisti[0]["soggetto"]."<br>");
		print ("Ubicazione:".$indirizzi[0]["via"]." ".$indirizzi[0]["civico"]."</p>");
		print("<img height=1 src=\"images/gray_light.gif\" width=\"100%\"  vspace=1> 
			</TD>
		</TR>");
?>
<?
//print_r($_REQUEST);
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
if ($_REQUEST["comm"]==1){
	$commissione="comm";
	$active_form="ce.ordinegiorno.php";
}
else if ($_REQUEST["comm_paesaggio"]==1){
	$commissione="comm_paesaggio";
	$active_form="ce.ordinegiorno_paesaggio.php";
}

include_once "./lib/date_format.php";
echo "<form  method=\"post\" action=\"praticaweb.php\">";
include_once "./db/db.pe.queryricerca.php";	// Trovo gli id di tutte le pratiche che soddisfano le condizioni
$ric=0;
$sqlPratichepres=" except select distinct pareri.pratica,data_presentazione from pe.pareri,pe.avvioproc where ente in (2,8,78) and data_rich=(select data_convocazione from ce.commissione where id=$idcomm) order by data_presentazione DESC;";  //"select distinct pratica from ce.discusse where commissione=$idcomm"
//echo "$sqlRicerca$sqlPratichepres<br>";
//print_debug($sqlRicerca.$sqlPratichepres);
//$db->sql_query($sqlRicerca.$sqlPratichepres);//trovo l'elenco degli id delle pratiche che mi interessano tolte quelle giÃ  presenti
$db->sql_query($sqlRicerca);
$elenco_pratiche=$db->sql_fetchlist("pratica");
//$elenco=serialize($elenco_pratiche);
$totrec=count($elenco_pratiche);
//Cerco i richiedenti
for($i=0;$i<$totrec;$i++){
	$idpratica=$elenco_pratiche[$i];
// Seleziono i soggetti richiedenti
	$sql="select * from pe.soggetti where pratica=$idpratica and (richiedente=1)";
	//echo "$sql<br>";
	$db->sql_query ($sql);
	$nrec=$db->sql_numrows();
	$soggetti = $db->sql_fetchrowset();	
	//Creo la stringa dei richiedenti
	for($j=0;$j<$nrec;$j++){
		if($nomi_richiedenti) $nomi_richiedenti.=" - ";
		$nomi_richiedenti.=$soggetti[$j]["app"]." ".$soggetti[$j]["cognome"]." ".$soggetti[$j]["nome"];
	}
	// Cerco l'ubicazione
	$sql="select * from pe.indirizzi where pratica=$idpratica";
	//echo "$sql<br>";
	$db->sql_query ($sql);
	$nrec=$db->sql_numrows();
	$indirizzi = $db->sql_fetchrowset();	
	for($j=0;$j<$nrec;$j++){
		if ($ubicazione) $ubicazione.=" - ";
		$ubicazione=$indirizzi[$j]["via"]." ".$indirizzi[$j]["civico"];
		if ($indirizzi[$j]["interno"]) $ubicazione.="/".$indirizzi[$j]["interno"];
	}
	//Cerco info sulla pratica
	$sql="select * from pe.avvioproc where pratica=$idpratica"; 
	$db->sql_query($sql);
	$dati_pratica=$db->sql_fetchrow();
	$titolo="Pratica n° ".$dati_pratica["numero"]." del ".$dati_pratica["data_presentazione"];
	//Scrivo i dati sulla pratica 
	print("\n<H2><input type=\"checkbox\" value=\"$idpratica\" name=\"idpratica[]\"><a href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$idpratica','PraticaWeb',0,0,'yes');\">$titolo</a></H2>");
	print("\n<input name=\"numero[]\" type=\"hidden\" value=\"".$dati_pratica["numero"]."\">");
	print ("\n<p>Oggetto:".$dati_pratica["oggetto"]."<br>");
	print ("\nRichiedenti: $nomi_richiedenti<br>");
	print ("\nUbicazione: $ubicazione");
	print("<img src=\"images/gray_light.gif\" height=\"2\" width=\"90%\">");
	unset($nomi_richiedenti);
	unset($indrizzi);
}	
$modo="view";
if ($totrec===0){			// Ricerca senza risultato
	$azione="<br>\n\t\t<center><input name=\"azione\" type=\"button\" class=\"hexfield\" tabindex=\"14\" value=\"Indietro\" onclick=\"javascript:document.location='$active_form?$commissione=1&pratica=$idcomm&mode=edit&ricerca=0'\"></center>";
	echo "<h2><center>La ricerca non ha prodotto nessun risultato</center><h2><br>";
}
else						// Ricerca con risultato
	$azione="<br>
		<input name=\"azione\" id=\"close\" type=\"button\" tabindex=\"14\" value=\"Annulla\" style=\"margin-top:10\">
		<input name=\"azione\" id=\"insert\" type=\"submit\" tabindex=\"14\" value=\"Inserisci\" style=\"margin-top:10\">
		<script>
			\$('#close').button({label:'Annulla',icons:{primary:'ui-icon-circle-triangle-w'}}).click(function(){document.location='praticaweb.php?$commissione=1&pratica=$idcomm&active_form=$active_form'});
			\$('#insert').button({label:'Inserisci',icons:{primary:'ui-icon-circle-plus'}})
		</script>";
		// Stampa dei bottoni e dei campi nascosti 	
		$hidden=$azione."\n\t\t<input name=\"active_form\" type=\"hidden\" value=\"$active_form\">
		<input name=\"mode\" type=\"hidden\" value=\"$modo\">
		<input name=\"$commissione\" type=\"hidden\" value=1>
		<input name=\"ricerca\" type=\"hidden\" value=\"$ric\">
		<input name=\"pratica\" type=\"hidden\" value=\"$idcomm\">
		
</form>\n";
print($hidden);
?>

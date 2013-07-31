<?
if($_POST["numero"]){

	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	
	
	$sql="select id from pe.avvioproc where numero='".$_POST["numero"]."';";
	
	$db->sql_query($sql);
	$pratica=$db->sql_fetchfield("id");
	
	if($pratica){
	
		$sql="
		DELETE FROM oneri.calcolati WHERE PRATICA=$pratica;
		DELETE FROM oneri.fidi WHERE PRATICA=$pratica;
		DELETE FROM oneri.monetizzazione WHERE PRATICA=$pratica;
		DELETE FROM oneri.rate WHERE PRATICA=$pratica;
		DELETE FROM oneri.totali WHERE PRATICA=$pratica;
		DELETE FROM pe.abitabi WHERE PRATICA=$pratica;
		DELETE FROM pe.allegati WHERE PRATICA=$pratica;
		DELETE FROM pe.asservimenti WHERE PRATICA=$pratica;
		DELETE FROM pe.asservimenti_map WHERE PRATICA=$pratica;
		DELETE FROM pe.avvioproc WHERE PRATICA=$pratica;
		DELETE FROM pe.cterreni WHERE PRATICA=$pratica;
		DELETE FROM pe.curbano WHERE PRATICA=$pratica;
		DELETE FROM pe.file_allegati WHERE PRATICA=$pratica;
		DELETE FROM pe.indirizzi WHERE PRATICA=$pratica;
		DELETE FROM pe.infodia WHERE PRATICA=$pratica;
		DELETE FROM pe.integrazioni WHERE PRATICA=$pratica;
		DELETE FROM pe.iter WHERE PRATICA=$pratica;
		DELETE FROM pe.lavori WHERE PRATICA=$pratica;
		DELETE FROM pe.menu WHERE PRATICA=$pratica;
		DELETE FROM pe.parametri_prog WHERE PRATICA=$pratica;
		DELETE FROM pe.pareri WHERE PRATICA=$pratica;
		DELETE FROM pe.progetto WHERE PRATICA=$pratica;
		DELETE FROM pe.proroga WHERE PRATICA=$pratica;
		DELETE FROM pe.soggetti WHERE PRATICA=$pratica;
		DELETE FROM pe.sopralluoghi WHERE PRATICA=$pratica;
		DELETE FROM pe.titolo WHERE PRATICA=$pratica;
		DELETE FROM pe.vincoli WHERE PRATICA=$pratica;
		DELETE FROM pe.volture WHERE PRATICA=$pratica;
		";
		$result=$db->sql_query($sql);
		
		if($result)
			echo "pratica eliminata con successo";

	}
}
?>
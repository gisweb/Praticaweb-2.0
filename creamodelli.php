<?
include "login.php";
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);

$sql="SELECT id,nome from stp.e_modelli where nome ilike '%.htm%' order by nome";
$db->sql_query($sql);
$ris=$db->sql_fetchlist('nome');
$id=$db->sql_fetchlist('id');
for($i=0;$i<count($ris);$i++){
	$f=fopen(MODELLI_DIR.$ris[$i],'r');
	$testo=fread($f,filesize(MODELLI_DIR.$ris[$i]));
	$testo=str_replace('<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: green">','<span class="valore">',$testo);
	$testo=str_replace('<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: yellow">IN_CICLO</span>','<span class="iniziocicli">IN_CICLO</span>',$testo);
	$testo=str_replace('<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: yellow">FI_CICLO</span>','<span class="finecicli">FI_CICLO</span>',$testo);
	$sql="UPDATE stp.e_modelli SET testohtml='".addslashes($testo)."' WHERE id=$id[$i]";
	if(!$db->sql_query($sql))
		echo "<p>Errore nell'Aggiornamento del modello $ris[$i]</p>";
}
	
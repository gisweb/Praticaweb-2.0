<?
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die("Impossibile connettersi al database");
$idcomm=$_POST["pratica"];
$id=$_POST["id"];
if ($_POST["azione"]="Salva"){
	$ruolo=$_POST["id_ruolo"];
	$pres=$_POST["presente"];
	$sql="UPDATE ce.partecipanti SET ruolo=$ruolo,presente=$pres WHERE membro=$id and commissione=$idcomm;";
	$db->sql_query($sql);
	print_debug($sql);
}
if ($_REQUEST["comm"]==1) $active_form="ce.commissione.php?pratica=$idcomm&mode=view";
else if ($_REQUEST["comm_paesaggio"]==1) $active_form="ce.commissione_paesaggio.php?pratica=$idcomm&mode=view";?>
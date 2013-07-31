<?
if ($_POST["azione"]=="Chiudi") $active_form="ce.iter.php?comm=1&pratica=$idpratica";
elseif($_POST["azione"]=="Elimina"){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);
	$sql="SELECT stampe from ce.iter where id=".$_POST["idriga"];
	$db->sql_query($sql);
	$id_stampa=$db->sql_fetchfield("stampe");
	$sql="DELETE FROM stp.stampe WHERE id=$id_stampa;";
	$db->sql_query($sql);
	include_once "./db/db.savedata.php";
}
else{
	include_once "./db/db.savedata.php";
	$sql="UPDATE ce.iter SET nota=nota_edit WHERE id=$lastid;";
	$db->sql_query($sql);
}


?>

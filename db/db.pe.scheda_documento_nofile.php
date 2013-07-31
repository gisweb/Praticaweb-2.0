<?
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	if (strlen($_POST["note"])>0)
		$sql="update pe.allegati set note='".addslashes($_POST["note"])."' where id=".$_POST["id"];
	else
		$sql="update pe.allegati set note=null where id=".$_POST["id"];
	$db->sql_query ($sql);
}
$active_form="pe.scheda_documento_nofile.php?pratica=$idpratica&id=all_".$_POST["id"];
?>
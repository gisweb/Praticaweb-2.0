<?

if ($_POST["azione"]=="Chiudi")
	$active_form="pe.iter.php?pratica=$idpratica";
elseif($_POST["azione"]=="Elimina"){
	$pr=new pratica($idpratica);
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);
	$sql="SELECT stampe from pe.iter where id=".$_POST["idriga"];
	$db->sql_query($sql);
	$id_stampa=$db->sql_fetchfield("stampe");
	if($id_stampa){
		$sql="SELECT file_doc,file_pdf FROM stp.stampe WHERE id=$id_stampa;";
		//echo "<p>$sql</p>";
		$db->sql_query($sql);
		$row=$db->sql_fetchrow();
		$file_doc=str_replace('.xml','',basename(basename($row[0]))).".doc";
		$file_pdf=$row[1];
		//extract($row);
		//echo "<p>Unlinking ".$pr->documenti.$file_doc."</p>";
		if($file_doc){
			unlink($pr->documenti.$file_doc);
		}
		if($file_pdf){
			@unlink($pr->documenti.$file_pdf);
		}
		$sql="DELETE FROM stp.stampe WHERE id=$id_stampa;";
		$db->sql_query($sql);
	}
	include_once "./db/db.savedata.php";
}
else{
	//print_array($_REQUEST);
	include_once "./db/db.savedata.php";
	$sql="UPDATE pe.iter SET nota=nota_edit WHERE id=$lastid;";
	$db->sql_query($sql);
	//echo "<p>$sql</p>";
}


?>
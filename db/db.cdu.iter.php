<?php
if ($_POST["azione"]=="Chiudi" || $_POST["azione"]=="Annulla") $active_form="cdu.iter.php?cdu=1&pratica=$idpratica";
elseif($_POST["azione"]=="Elimina"){
	$pr=new pratica($idpratica,1);
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);
	$sql="SELECT stampe from cdu.iter where id=".$_POST["idriga"];
	$db->sql_query($sql);
	$id_stampa=$db->sql_fetchfield("stampe");
	$sql="SELECT file_doc,file_pdf FROM stp.stampe WHERE id=$id_stampa;";
	$db->sql_query($sql);
	$nome_doc=$db->sql_fetchfield("file_doc");
	$nome_pdf=$db->sql_fetchfield("file_pdf");
	$sql="DELETE FROM stp.stampe WHERE id=$id_stampa;";
    $db->sql_query($sql);
	if($id_stampa){
		$sql="SELECT file_doc,file_pdf FROM stp.stampe WHERE id=$id_stampa;";
		//echo "<p>$sql</p>";
		$db->sql_query($sql);
		$row=$db->sql_fetchrow();
		$file_doc=basename($row[0]).".doc";
		$file_pdf=$row[1];
		//extract($row);
		//echo "<p>Unlinking ".$pr->documenti.$file_doc."</p>";
		if($file_doc){
			@unlink($pr->documenti.$file_doc);
		}
		if($file_pdf){
			@unlink($pr->documenti.$file_pdf);
		}
	}
	
	include_once "./db/db.savedata.php";
}
else{
	include_once "./db/db.savedata.php";
	$sql="UPDATE cdu.iter SET nota=nota_edit WHERE id=$lastid;";
	//echo "<p>$sql</p>";
	$db->sql_query($sql);
}


?>

<?php
if ($_POST["azione"]=="Chiudi" || $_POST["azione"]=="Annulla") $active_form="cdu.iter.php?cdu=1&pratica=$idpratica";
elseif($_POST["azione"]=="Elimina"){
	$pr=new pratica($idpratica,1);
	$conn = utils::getDb();
	$sql="SELECT stampe from cdu.iter where id=?";
        $sth=$conn->prepare($sql);
        $sth->execute(Array($_POST["idriga"]));
	$id_stampa=$sth->fetchAll(PDO::FETCH_COLUMN);
	$sql="SELECT file_doc,file_pdf FROM stp.stampe WHERE id=?;";
	$sth=$conn->prepare($sql);
        $sth->execute(Array($id_stampa));
	$nome_doc=$sth->fetchColumn();
	$nome_pdf=$sth->fetchColumn(1);
	$sql="DELETE FROM stp.stampe WHERE id=?;";
        $sth=$conn->prepare($sql);
        $sth->execute(Array($id_stampa));
	if($id_stampa){
		$sql="SELECT file_doc,file_pdf FROM stp.stampe WHERE id=?;";
		$sth=$conn->prepare($sql);
                $sth->execute(Array($id_stampa));
		$row=$sth->fetch();
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
	$sql="UPDATE cdu.iter SET nota=nota_edit WHERE id=?;";
	$sth=$conn->prepare($sql);
        $sth->execute(Array($lastid));
}


?>

<?

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") )
	include_once "./db/db.savedata.php";
	
$active_form="ce.comm_edilizia.php?condono=1&pratica=$idpratica";
	
?>
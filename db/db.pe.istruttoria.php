<?

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") )
	include_once "./db/db.savedata.php";
	
	
$active_form="pe.istruttoria.php?pratica=$idpratica";
	
?>
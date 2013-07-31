<?

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
	if ($_POST["mode"]=="new"){
		$idpratica= $_SESSION["ADD_NEW"];
		$db->sql_query ("update cdu.richiesta set pratica=id where id=$idpratica");
	}
}
	
$active_form="cdu.richiesta.php?pratica=$idpratica";
?>
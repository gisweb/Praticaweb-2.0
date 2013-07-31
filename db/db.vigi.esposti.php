<?	
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php"; 
	if($_POST["mode"]=="new"){
		$sql="update vigi.esposti set termine_verifica=data_presentazione + INTERVAL '30 days'  where id=$lastid and termine_verifica is null;";
		$db->sql_query($sql);
	}
}

$active_form="vigi.esposti.php?pratica=$idpratica";
?>
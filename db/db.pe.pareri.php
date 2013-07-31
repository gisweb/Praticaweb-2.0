<?

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
	if ($_POST["azione"]=="Salva" and ($_POST["ente"]==2 or $_POST["ente"]==8)) {
		$id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_POST["id"]);
        	if ($_POST["data_ril"]) 
			$sql="UPDATE pe.pareri SET data_rich='".$_POST["data_ril"]."'::date WHERE id=$id";
		elseif($_POST["data_rich"])
			$sql="UPDATE pe.pareri SET data_ril='".$_POST["data_rich"]."'::date WHERE id=$id";
		elseif($_POST["data_rice"])
			$sql="UPDATE pe.pareri SET data_ril='".$_POST["data_rice"]."'::date, data_rich='".$_POST["data_rice"]."'::date WHERE id=$id";
	        $db->sql_query($sql);
        	print_debug($sql);
	}

}
	
$active_form="pe.pareri.php?pratica=$idpratica";
	
?>

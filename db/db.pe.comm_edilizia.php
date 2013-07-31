<?

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") )
	include_once "./db/db.savedata.php";
	if ($_POST["azione"]=="Salva") {
		$id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_POST["id"]);
		$sql="UPDATE pe.pareri SET data_rich='".$_POST["data_ril"]."'::date WHERE id=$id";
		$db->sql_query($sql);
        print_debug($sql);
	}
	
$active_form="pe.comm_edilizia.php?pratica=$idpratica";
	
?>
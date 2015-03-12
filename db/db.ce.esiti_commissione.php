<?	
include "db/db.savedata.php";
$idcomm=$_POST["pratica"];
if ($_POST["azione"]=="Salva" and $_POST["parere"]) {
	$sql="UPDATE pe.pareri SET data_ril=data_rich WHERE id=$id";
	$db->sql_query($sql);
	print_debug($sql);
}
$active_form="ce.esiti_commissione.php?comm=1&pratica=$idcomm";
?>
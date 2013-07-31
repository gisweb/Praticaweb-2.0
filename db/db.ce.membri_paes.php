<?	

if ($_POST["azione"]=="Salva" or $_POST["azione"]=="Elimina") {
	include("./db/db.savedata.php");
       if ($_POST["mode"]=="new") {
		$idmembro=$_SESSION["ADD_NEW"];
		$sql="UPDATE ce.e_membri SET tipo_comm='paesaggio' WHERE id=$idmembro";  
		$db->sql_query($sql);
		print_debug($sql);
       }
}

?>
<?
//$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
//if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$id=$_POST["id"];
$nome=$_POST["nome"];
$script=$_POST["script"];
$desc=$_POST["descrizione"];
$def=$_POST["definizione"];
$size=$_POST["orientamento"];
$or=$_POST["dimensione"];
$azione=$_POST["azione"];
$modo=$_POST["mode"];

include "db/db.savedata.php";

if($azione=="Salva") {
	
	//if($modo=="new")
	//	$sql="insert into stp.css (nome,script,descrizione,definizione,dimensione,orientamento) values ('$nome','$script','$desc','$def','$size','$or');"; 
	//else 
	//	$sql="update stp.css set nome='$nome',script='$script',descrizione='$desc',definizione='$def',dimensione='$size',orientamento='$or' where id=$id;"; 
	//if ($db->sql_query($sql)){
		echo APPS_DIR."css/stp.$nome.css";
		$handle=fopen(APPS_DIR."css/stp.$nome.css",'w');
		fwrite($handle,$def);
		fclose($handle);
	//}
	//else
	//	print_debug("Errore nella query:\n\t\t $sql");
}

elseif ($azione=="Elimina"){
	//$sql="delete from stp.css where id=$id;"; 
	//if($db->sql_query ($sql))
		@unlink(APPS_DIR."css/$nome.css");
	//else
	//	print_debug("Errore nella query:\n\t\t $sql");
}
?>
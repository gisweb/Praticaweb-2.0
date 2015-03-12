<?//GESTIONE salvataggio form allegati
//print_r($_POST);
/*
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$vincolo=$_POST["vincolo"];
$zona=$_POST["zona"];
$tavola=$_POST["tavola"];
if(NEW_VINCOLI==1){
	if($_POST["azione"]!="Chiudi"){ 
		$sql="insert into pe.vincoli (pratica,vincolo,zona,tavola,uidins,tmsins) values ($idpratica,'$vincolo','$zona','$tavola',".$_SESSION["USER_ID"].",".time().");";
		$db->sql_query ($sql);
	}
}
*/
$active_form="pe.vincoli.php?pratica=$idpratica";

?>
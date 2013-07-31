<?
$id=$_SESSION["USER_ID"];
$pwd =(isset($_POST['pwd']))?($_POST['pwd']):(null);
$enc_pwd=md5($pwd);
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$azione=(isset($_REQUEST["azione"]))?($_REQUEST["azione"]):(null);


$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

if ($azione=="Annulla" or $azione=="Chiudi"){
	if ($modo=="edit") $modo="view";
	
}
elseif($azione=="Salva"){
	if (strlen($pwd) < 4) $errors["pwd"]="La password deve essere almeno di 4 caratteri";
	
	else{
		$sql="UPDATE admin.users SET pwd='$pwd',enc_pwd='$enc_pwd' WHERE userid=$id";
		if (!$errors) $db->sql_query($sql);	
	}
	if (!$errors) $modo="view";
}

$active_form="admin.pwd.php?mode=$modo&id=$id";
?>

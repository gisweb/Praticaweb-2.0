<?php
$id=$_SESSION["USER_ID"];
$pwd =(isset($_POST['pwd']))?($_POST['pwd']):(null);
$enc_pwd=md5($pwd);
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$azione=(isset($_REQUEST["azione"]))?($_REQUEST["azione"]):(null);


$conn=utils::getDb();

if ($azione=="Annulla" or $azione=="Chiudi"){
	if ($modo=="edit") $modo="view";
	
}
elseif($azione=="Salva"){
	if (strlen($pwd) < 4) $errors["pwd"]="La password deve essere almeno di 4 caratteri";
	
	else{
		$sql="UPDATE admin.users SET pwd='$pwd',enc_pwd='$enc_pwd' WHERE userid=$id";
		if (!$errors) $conn->execute($sql);	
	}
	if (!$errors) $modo="view";
}

$active_form="admin.pwd.php?mode=$modo&id=$id";
?>

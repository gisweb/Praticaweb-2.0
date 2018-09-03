<?php
$pwd =(isset($_POST['pwd']))?($_POST['pwd']):(null);
$pwd1 =(isset($_POST['pwd1']))?($_POST['pwd1']):(null);
$attivato= (isset($_POST["attivato"]) && $_POST["attivato"]=="on")?(1):(0);
$livello_utente =(isset($_POST["permessi"]))?($_POST["permessi"]):(null);
$role=(isset($_POST["role"]) && $_POST["role"]=="praticaweb")?(2):(1);
$cognome = (isset($_POST['cognome']))?(stripslashes($_POST['cognome'])):(null);
$nominativo = (isset($_POST['nominativo']))?(stripslashes($_POST['nominativo'])):(null);
$app = (isset($_POST['app']))?(stripslashes($_POST['app'])):(null);
$username = (isset($_POST['username']))?(stripslashes($_POST['username'])):(null);
$tel=(isset($_POST["num_tel"]))?($_POST["num_tel"]):(null);
$mail = (isset($_POST["mail"]))?($_POST["mail"]):(null);
$info=(isset($_POST['info']))?(addslashes($_POST['info'])):(null);
$enc_pwd=md5($pwd);
$gc=($_REQUEST['gisclient'])?('1'):('0');
$azione=(isset($_REQUEST["azione"]))?($_REQUEST["azione"]):(null);

$modo=$_REQUEST["mode"];

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

if ($azione=="Annulla" or $azione=="Chiudi"){
	if ($modo=="edit") $modo="view";
	else
		$modo="all";
}
elseif($azione=="Salva"){
	if ($pwd!==$pwd1) $errors["pwd"]="PassWord non Corrispondenti";
	if ($livello_utente<$_SESSION["PERMESSI"]) $errors["permessi"]="Non si dispone dei permessi necessari";
	$gruppi=implode(',',$_REQUEST['gruppi']);
	if ($modo=="new"){
		
		if (defined('GC_PROJECT')){
			if($_SESSION["PERMESSI"]>1 && $role==1){
				$errors["role"]="Non si dispone dei diritti per assegnare questo ruolo all'utente";
			}
			else{
				include "./db/db.gisclientuser.php";
				$sql="INSERT INTO admin.users(userid,app,cognome,nominativo,username,pwd,enc_pwd,permessi,attivato,num_tel,info,gruppi,data_creazione,gisclient) VALUES($newUserId,'$app','$cognome','$nominativo','$username','$pwd','$enc_pwd',$livello_utente,$attivato,'$tel','$info','$gruppi',now(),$gc);";
                $db->sql_query($sql);
				$id=$newUserId;
			}
		}
		else{
			$sql="INSERT INTO admin.users(app,cognome,nominativo,username,pwd,enc_pwd,permessi,attivato,num_tel,mail,info,gruppi,data_creazione,gisclient) VALUES('$app','$cognome','$nominativo','$username','$pwd','$enc_pwd',$livello_utente,$attivato,'$tel','$mail','$info','$gruppi',now(),$gc);";
			if (!$errors){ 
				$db->sql_query($sql);
				$db->sql_query("SELECT max(userid) as lastvalue FROM admin.users");
				$id=$db->sql_fetchfield("lastvalue");
			}
		}
	}
	else{
		$sql="UPDATE admin.users SET app='$app',nominativo='$nominativo',cognome='$cognome',username='$username',pwd='$pwd',enc_pwd='$enc_pwd',gruppi='$gruppi',permessi='$livello_utente',attivato='$attivato',num_tel='$tel',info='$info',data_modifica=now(),mail='$mail',gisclient=$gc WHERE userid=$id";
		if (!$errors) $db->sql_query($sql);	
	}
	if (!$errors) $modo="view";
}
elseif($azione=="Elimina"){
	if ($livello_utente<$_SESSION["PERMESSI"]) $errors["permessi"]="Non si dispone dei permessi necessari";
	$sql="DELETE FROM admin.users WHERE userid=$id";
	if (!$errors) {
		$db->sql_query($sql);
		$modo="all";
	}
}


$active_form="admin.utenti.php?mode=$modo&id=$id";
?>

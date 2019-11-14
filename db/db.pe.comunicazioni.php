<?php
//error_reporting(E_ALL);
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



require_once LIB.DIRECTORY_SEPARATOR."PHPMailer.php";
require_once LIB.DIRECTORY_SEPARATOR."SMTP.php";
require_once LIB.DIRECTORY_SEPARATOR."OAuth.php";
require_once LIB.DIRECTORY_SEPARATOR."Exception.php";


*/
$idpratica=$_REQUEST["pratica"];

$action = strtolower($_REQUEST["azione"]);
$active_form = $_REQUEST["active_form"];

$dbName = basename(__FILE__); 
$localSave = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."db".DIRECTORY_SEPARATOR.$dbName;

if (in_array($action,Array(ACTION_SAVE,ACTION_DELETE,ACTION_MAIL))){
	
	if ($action==ACTION_MAIL) $_REQUEST["azione"] = "Salva";
	require_once APPS_DIR."db/db.savedata.php";	
	$id = ($_REQUEST["id"])?($_REQUEST["id"]):($_SESSION["ADD_NEW"]);
	
	//utils::debugAdmin($id);
	if (file_exists($localSave)){
		include_once $localSave;
	}
	
/*
	$id = ($_REQUEST["id"])?($_REQUEST["id"]):($_SESSION["ADD_NEW"]);
	
	if ($action==ACTION_MAIL){
		$r = appUtils::getComunicazione($id);
		if($r["success"]==1){
			require_once DATA_DIR."config.mail.php";
			//require_once LIB."mail.class.php";
			$rr = inviaPec("",$r["comunicazione"]["to"],$r["comunicazione"]["subject"],$r["comunicazione"]["text"],$r["comunicazione"]["attachments"]);
			if($rr["success"]==1){
				$dbh = utils::getDB();
				$sql = "UPDATE pe.comunicazioni SET data_invio=?, id_comunicazione=? WHERE pratica=? AND id=?;";
				$stmt = $dbh->prepare->sql($sql);
				if(!$stmt->execute(Array(date('d/m/Y'),$rr["uuid"],$idpratica,$id)){
					$err = $stmt->errorInfo();
					$Errors["data_invio"]=$err[2];
					include_once $active_form;
					exit;
				}
			}
			else{
				$Errors["data_invio"]="Si sono verificati degli errori durante l'invio della comunicazione";
				include_once $active_form;
				exit;
			}
		}
	
	}
*/		

}

$active_form="pe.comunicazioni.php?pratica=$idpratica&mode=list";
	
?>

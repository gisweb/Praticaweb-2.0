<?php
$idpratica=$_REQUEST["pratica"];
$action = $REQUEST["azione"];
if (in_array($action,Array("Salva","Elimina"))){
	
	if ($action=="Salva" && defined('PROT_OUT') && $_REQUEST["richiedi_protocollo_out"] && PROT_OUT==1){
				
		//$user=appUtils::getUserProtocollo($_SESSION["USER_ID"]);
		$user=appUtils::getUserProtocollo(52);
		$username=$user["username"];
		
		$_REQUEST["data_comunicazione"] =($_REQUEST["data_comunicazione"])?($_REQUEST["data_comunicazione"]):(date('d/m/Y', time()));
		//$_REQUEST["data_comunicazione"] = date('d/m/Y', time());
		$res = appUtils::richiediProtocolloOut($username,"","Richiesta Protocollo per documento in uscita","",$_REQUEST["data_comunicazione"]);
		if ($res["success"]==1){
			$_REQUEST["protocollo_comunicazione"] = $res["protocollo"];
		}
		else{				
			$message=sprintf("<p>%s</p>",$res["message"]);
			if (!$username)
				$message .= "<p>Richiesta Protocollo Uscita da parte dell'utente ".appUtils::getUserName()."</p>";
			echo "<div class=\"errors\" style=\"height:50px !important;\">".$message."</div>";
		}
	}
	
	include_once "./db/db.savedata.php";
}

	
$active_form="pe.comunicazioni.php?pratica=$idpratica&mode=list";
	
?>

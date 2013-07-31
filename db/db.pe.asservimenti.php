<?
if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
	if ($_POST["azione"]=="Salva"){
		/*if (!$_POST["numero"]){
			$sql="UPDATE pe.asservimenti SET numero='$lastid' WHERE id=$lastid";
			if(!$db->sql_query($sql)) echo "$sql<br>";
		}*/
		$time=time();
		if (!$_POST["id"]) $sql="INSERT INTO pe.asservimenti_prat(asservimento,pratica,uidins,tmsins) VALUES($lastid,$idpratica,".$_SESSION["USER_ID"].",$time);";
		if (!$db->sql_query($sql)) echo $sql."<br>";
	}
}
$active_form="pe.asservimenti.php?pratica=$idpratica";
?>

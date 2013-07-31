<?
//Inserimento automatico delle date di scadenza lavori quando viene inserita la data di notifica.

if (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
	$tabella=$_POST["tabella"];
	$data=$_POST["data_notifica"];
	//se non ho la data di notifica non salvo nulla
	
	$sql="select id from pe.lavori where pratica=$idpratica";
	$db->sql_query($sql);
	$res=$db->sql_fetchrow();
	// se ho già il record esco
	if(!$res){;
		$sql="insert into pe.lavori (pratica,scade_il,scade_fl,uidins,tmsins) values ($idpratica,TIMESTAMP '$data' + INTERVAL '1 year',TIMESTAMP '$data' + INTERVAL '3 year',".$_SESSION["USER_ID"].",".time().");";
		$db->sql_query($sql);
	}
}

$active_form="pe.titolo_amb.php?pratica=$idpratica";
?>

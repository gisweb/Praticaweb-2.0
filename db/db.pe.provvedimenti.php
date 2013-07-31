<?
include "db/db.savedata.php";
if ($_POST["azione"]=="Elimina"){
	$db->sql_query("DELETE FROM pe.dest_provvedimenti WHERE pratica=$idpratica and id_provv=$id");
}
elseif ($_POST["azione"]=="Salva" and $_POST["tipo"]!=1){
	if ($_POST["mode"]=="new"){
		$id=$_SESSION["ADD_NEW"];
		$sql="UPDATE pe.ordinanze SET tipo=".$_POST["tipo"]." WHERE pratica=$idpratica and id=$id";
		$db->sql_query($sql);
		//if (DEBUG) echo "$sql<br>";
	}
	$db->sql_query("DELETE FROM pe.dest_provvedimenti WHERE pratica=$idpratica and id_provv=$id");
	/*foreach($_POST as $key=>$value){
		if ($key=="data_notifica"){
			//Cerco nell'array delle date delle notifiche i valori da inserire
			foreach($value as $id_soggetto=>$data){
				//Controllo se la data è OK
				$sql="insert into vigi.dest_provvedimenti(pratica,id_soggetto,id_provv,data_notifica) values($idpratica,$id_soggetto,$id,'$data')";
				if ($data and strtotime($data)!=-1) $db->sql_query($sql);
				//if (DEBUG) echo "$sql<br>";
			}
		}
	}*/
	
	
}
$active_form="pe.provvedimenti.php?pratica=$idpratica";
?>
<?
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
if ($_POST["azione"]=="Elimina"){
	$sql="DELETE FROM pe.dest_provvedimenti where pratica=".$_POST["pratica"]." and id_provv=".$_POST["id"]." and id_soggetto=".$_POST["id_soggetto"];
	//echo "<br>$sql<br>";
	$db->sql_query($sql);
}
elseif ($_POST["azione"]=="Salva"){
	
	foreach($_POST as $key=>$value){
		if ($key=="data_notifica"){
			//Cerco nell'array delle date delle notifiche i valori da inserire
			foreach($value as $id_soggetto=>$data){
				//Controllo se la data è OK
				if ($data and strtotime($data)!=-1){
					$sql="insert into pe.dest_provvedimenti(pratica,id_soggetto,id_provv,data_notifica) values($idpratica,$id_soggetto,".$_POST["id"].",'$data')";
					//echo "<br>$sql<br>";
					$db->sql_query($sql);
				}
				
				
			}
		}
	}
}
?>
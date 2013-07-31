<?
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

	if ($_POST["azione"]=="Salva"){
		$data=($_POST['data_protocollo'])?("'".$_POST['data_protocollo']."'::date"):("null");
		$sql="update pe.allegati set note='".$_POST["note"]."',protocollo='".$_REQUEST['protocollo']."',data_protocollo=$data where id=".$_POST["id"];
		$db->sql_query ($sql);
		//echo "<p>$sql</p>";
		//file_allegati
		if ($_POST["elimina"]){
			$eliminati=implode(",",array_keys($_POST["elimina"]));
			$sql="delete from pe.file_allegati where id in ($eliminati)";
			$db->sql_query($sql);
		}
		if ($_POST["descrizione"]){
			$descrizioni=$_POST["descrizione"];
			foreach ($descrizioni as $key=>$value){
				if ($value){
					$sql="update pe.file_allegati set note='$value' where id=$key;";
					$db->sql_query($sql);
				}
			}
		}
		if ($_POST["ordine"]){
			$ordine=$_POST["ordine"];
			foreach ($ordine as $key=>$value){
				if ($value){
					$sql="update pe.file_allegati set ordine=$value where id=$key;";
					$db->sql_query($sql);
				}
			}
		}
	}
	$db->sql_close();
	$active_form="pe.scheda_documento.php?pratica=$idpratica&id=all_".$_POST["id"];

	?>
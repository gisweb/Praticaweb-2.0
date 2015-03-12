<? 
      
	include_once "./db/db.savedata.php";
	if ($_POST["mode"]=="new") {
		$idpratica=$_SESSION["ADD_NEW"];
		//print_r($_POST);
             
              $db->sql_query("update ce.commissione set pratica=$idpratica , tipo_comm='78' where id=$idpratica"); 
              
		$uid=$_SESSION['USER_ID'];
		foreach($_POST as $key=>$value){
			$tmsins=time();
			$sql="insert into ce.partecipanti(membro,commissione,uidins,tmsins) values($key,$idpratica,$uid,$tmsins)";

			if ($value=="id"){
				if (!$db->sql_query($sql)) echo "$sql<br>";
			}
		}
				
	}
	elseif ($_POST["mode"]=="edit") {
		if ($_POST["azione"]=="Salva"){
			$uid=$_SESSION['USER_ID'];
			foreach($_POST as $key=>$value){
				$tmsins=time();
				if ($value=="id") $db->sql_query("insert into ce.partecipanti(membro,commissione,uidins,tmsins) values($key,$idpratica,$uid,$tmsins)");
			}
			
		}
		if ($_POST["membro"]>0){
			$idmembro=$_POST["membro"];
			$sql="delete from ce.partecipanti where membro=$idmembro and commissione=$idpratica";
			//echo "$sql<br>";
			$db->sql_query ($sql);
			
		}
		
	}
	
	//if ($_POST["membro"]>0) $active_form="ce.commissione_paesaggio?pratica=$idpratica&mode=edit&head=no";
	//else
		$active_form="ce.commissione_paesaggio.php?pratica=$idpratica&mode=view";
	
		
?>

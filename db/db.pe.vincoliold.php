<?//GESTIONE salvataggio form allegati
//print_r($_POST);

$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$vincolo=$_POST["vincolo"];
$zona=$_POST["zona"];
$tavola=$_POST["tavola"];
if(NEW_VINCOLI==1){
	$sql_zona="select * from vincoli.zona where descrizione='$zona' and nome_vincolo='$vincolo' and nome_tavola='$tavola'"; 
	print_debug($sql_zona);
	$db->sql_query($sql_zona);
	$zona = $db->sql_fetchfield("nome_zona"); 
	if($_POST["azione"]!="Chiudi"){
		$sql="insert into pe.vincoli (pratica,vincolo,zona,tavola,uidins,tmsins) values ($idpratica,'$vincolo','$zona','$tavola',".$_SESSION["USER_ID"].",".time().");";
		$db->sql_query ($sql); 
	}
}

	
else {	/*	 print_array($_POST);	
	foreach ($_POST as $key=>$value){
		if ($value=="id"){
			$sql="delete from pe.vincoli where id=$key;";
		}
		elseif($value=="mapkey"){
			$insert=1;
			if (!$_SESSION["ADD_NEW"]){//inserisco solo se non ho già inserito il dato
				$dato=explode("@",$key);
				$zona=trim($dato[0]);
				$vincolo=trim($dato[1]);
				$sql="insert into pe.vincoli (pratica,vincolo,zona,uidins,tmsins) values ($idpratica,'$vincolo','$zona',".$_SESSION["USER_ID"].",".time().");";
				
				}
			}
			else
				echo "<p>Inserimento già fatto</p>";
	}*/
	$sql="insert into pe.vincoli (pratica,vincolo,zona,uidins,tmsins) values ($idpratica,'$vincolo','$zona',".$_SESSION["USER_ID"].",".time().");";
	$db->sql_query ($sql); 
}


if($debug) echo $sql;
$_SESSION["ADD_NEW"]=$insert; // se ho inserito qualcosa lo segno
$active_form="pe.vincoli.php?pratica=$idpratica";

?>
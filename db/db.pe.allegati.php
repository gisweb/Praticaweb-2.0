<?//GESTIONE salvataggio form allegati

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
//per la gestione del form integrazioni
if (isset($_POST["integrazione"])) $integrazione=$_POST["integrazione"];
$flag_mancante=$insert=null;

if ($modo=="new") $integrazione=$_SESSION["ADD_NEW"];
if (isset($integrazione)) $integrazione=",integrazione=$integrazione";
if (isset($_POST["azione"]) && $_POST["azione"]=="Salva"){
	foreach ($_POST as $key=>$value){
		$id=substr($key,4);
		$tab=substr($key,0,3);
		if ($tab=="all"){
			if($value=="allegato")
				$sql="update pe.allegati set allegato=1,mancante=0,sostituito=0,integrato=0,uidupd=".$_SESSION["USER_ID"].",tmsupd=".time()." where id=$id";
			elseif($value=="integrato")
				$sql="update pe.allegati set allegato=0,mancante=0,sostituito=0,integrato=1 $integrazione ,uidupd=".$_SESSION["USER_ID"].",tmsupd=".time()." where id=$id";	
			elseif($value=="sostituito")
				$sql="update pe.allegati set allegato=0,mancante=0,sostituito=1,integrato=0 $integrazione ,uidupd=".$_SESSION["USER_ID"].",tmsupd=".time()." where id=$id";			
			elseif($value=="mancante"){
				$sql="update pe.allegati set allegato=0,mancante=1,sostituito=0,integrato=0 ,uidupd=".$_SESSION["USER_ID"].",tmsupd=".time()." where id=$id";
				$flag_mancante=1;
				}
			elseif($value=="id")
				$sql="delete from pe.allegati where id=$id";
			$db->sql_query ($sql);
			//echo "<p>$sql</p>"
		}
		elseif ($tab=="doc"){			
			$insert=1;
			if (!$_SESSION["ADD_NEW"]){//inserisco solo se non ho già inserito il dato
				$sql="insert into pe.allegati (pratica,documento,$value,chk,uidins,tmsins) values ($idpratica,$id,1,1,".$_SESSION["USER_ID"].",".time().");";
				//print_debug($sql);
				$db->sql_query ($sql);
				if ($value=="mancante")
					$flag_mancante=1;
				//echo $sql."......".$flag_mancante;
			}
			else
				echo "<p>Inserimento già fatto</p>";
		}
	}
}
//ripulisco il db dai record che non hanno flag settati
//$sql="delete from allegati where ((pratica=$idpratica) and (allegato+mancante+sostituito+integrato=0)) ;";
//$db->sql_query ($sql);
//echo $sql;

//Se ho almeno un documento mancante devo attivare il menu integrazione: vedo prima se il menu è gia presente


//###########COMMENTATA LA GESTIONE DELLE INTEGRAZIONI#################
if ($flag_mancante==1){
	$menu->add_menu($idpratica,45);//45=id menu integrazioni
}

$_SESSION["ADD_NEW"]=$insert; // se ho inserito qualcosa lo segno
$active_form=$_POST["active_form"]."?pratica=$idpratica";

?>

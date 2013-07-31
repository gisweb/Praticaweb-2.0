<?
include_once "config/config.php";
$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);	
//print_r($_POST);	
//print_r($_FILES);
$descr=$_POST['descrizione'];
$propr=$_POST['propr'];
$uploaddir = getcwd()."/modelli/";
$err_msg="errore nel caricamento del file!";
if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploaddir.$_FILES['myfile']['name'])){
	list($nome,$ext)=explode(".",$_FILES['myfile']['name']);
	$sql="SELECT * FROM stp.e_modelli WHERE nome='".$_FILES['myfile']['name']."' AND form='$form' and proprietario='$propr'";
	if ($db->sql_query($sql)){
		
		$elenco_modelli = $db->sql_fetchrowset();
		$nrighe=$db->sql_numrows();
		if ($nrighe===0){
			$nome.=".".$ext;
			$sql="INSERT INTO stp.e_modelli(nome,form,descrizione,proprietario) VALUES('$nome','$form','$descr','$propr')";
			if (!$db->sql_query($sql)){
				 //echo "caricamento non riuscito di :".$_FILES['myfile']['name']."<br>$sql<br>";
				 $err_msg="caricamento del modello ".$_FILES['myfile']['name']." non riuscito!";
			}
			else
				unset($err_msg);
		}
		else
			$err_msg="Il modello ".$_FILES['myfile']['name']." è già presente!";
	}
}

?>
<?
include_once "login.php";
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);	
//echo "<pre>";print_r($_POST);echo "</pre>";


$descr=$_POST['descrizione'];
$propr=$_POST['propr'];
$categoria=$_POST['categoria'];
$tipopr=$_POST['tipopr'];

$err_msg="errore nel caricamento del file!";
$sql="SELECT * FROM stp.e_modelli WHERE nome='".$_FILES['myfile']['name']."' AND form='$form' and proprietario='$propr'";
	if ($db->sql_query($sql)){
		$elenco_modelli = $db->sql_fetchrowset();
		$nrighe=$db->sql_numrows();
		if ($nrighe===0){
		$tmp=explode(".",$_FILES['myfile']['name']);
		$ext=strtolower(array_pop($tmp));
		$nome= str_replace("'","",implode(".",$tmp));
			$nome.=".".$ext;
			$sql="INSERT INTO stp.e_modelli(nome,form,descrizione,proprietario,categoria,tipo_pratica) VALUES('$nome','$form','$descr','$propr','$categoria','$tipopr')";
			if (!$db->sql_query($sql)){
                            //print_r($sql);
				 $err_msg="caricamento del modello ".$_FILES['myfile']['name']." non riuscito!";
			}
			else { 
				if (move_uploaded_file($_FILES['myfile']['tmp_name'], str_replace("\'","",MODELLI_DIR.$_FILES['myfile']['name'])))
					$err_msg="Il modello ". str_replace("\'","",$_FILES['myfile']['name'])." è stato caricato!";
                            else $err_msg="Il modello ". str_replace("\'","",$_FILES['myfile']['name'])." non è stato caricato!";

			}
				
		}

		else{ 
			$file=MODELLI_DIR.$_FILES['myfile']['name'];
			@unlink($file);
			if (move_uploaded_file($_FILES['myfile']['tmp_name'],  str_replace("\'","",MODELLI_DIR.$_FILES['myfile']['name'])))
				$err_msg="Il modello ". str_replace("\'","",$_FILES['myfile']['name'])." è stato sostituito!"; 
		}	
	}

else
	$err_msg="Il modello ". str_replace("\'","",$_FILES['myfile']['name'])." non è stato caricato!";
?>

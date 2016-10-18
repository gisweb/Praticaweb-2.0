<?
include_once "./login.php";
//error_reporting(E_ALL);
$usr=$_SESSION['USER_NAME'];
$idpratica=$_REQUEST["pratica"];
$form=$_POST["form"];
$modello=$_POST["modello"];
$file=$_POST["file"];
$azione=$_POST["azione"];
$procedimento=$_POST["procedimento"];
$id_modello=$_POST["id"];
list($schema_iter,$nomeform)=explode(".",$form);
if ($schema_iter=="oneri" || $schema_iter=="vigi") $schema_iter="pe";	//Redirigo gli schemi collegati a PE
$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);

//if ($_SESSION["PERMESSI"]==1) print_r($_POST);

//if(!isset($_SESSION["ADD_NEW"])){
	if ($_POST["azione"]==="Crea Documento") {	//	Creo un nuovo documento
		$sql="SELECT nome,print_type FROM stp.e_modelli WHERE id=".$_POST["id"];
		print_debug($sql,null,"stampe");
		$db->sql_query ($sql);
		$modello=$db->sql_fetchfield("nome");
		$modal=$db->sql_fetchfield("print_type");
		$tmp=explode(".",$modello);
		$ext=array_pop($tmp);
		$nome=implode(".",$tmp);
		$nome_file="$idpratica-$nome.$ext";
		
		$sql="SELECT * FROM stp.stampe WHERE file_doc='$nome_file' and form='$form'";
		print_debug($sql);
		//if ($_SESSION["PERMESSI"]==1) echo "<p>$sql</p>";

		$result=$db->sql_query ($sql);
		if ($result and !$tab_err){
			$elenco_stampe = $db->sql_fetchrowset();
			$nrighe=$db->sql_numrows();
			if ($nrighe<=1) {
				$nomefile=$nfile;
				if ($nrighe===1) {
				
					$tab_err[0][]="Il file $nome_file è gia presente. Modificare il nome";
					$hidden="visible";
				}
				else{ 
					$file_doc=$nome_file;
					$file_pdf=str_replace(".html",".pdf",$file_doc);
					if ($ext==="htm" or $ext=="html") {
						include_once "lib/stampe.class.php";
						list($sc,$f)=explode(".",$form);
						$schema=($sc=="cn")?("stp_condono"):("stp");
					
						$m=new stampe($idpratica,$id_modello,$nome,$schema,1,1);
						$m->sostituisci_valori();
						$m->crea_documento();
						$m->print_debug();
						//print_debug($m,null,"__STAMPE");
						//if ($_SESSION["USER_ID"]==1) 
						//$m->pdf->debug=1;
						if (!$modal)
							$m->crea_pdf();
						else
							$m->crea_pdf_1();
						$sql="INSERT INTO stp.stampe(pratica,modello,file_doc,file_pdf,form,utente_doc,utente_pdf,data_creazione_doc,data_creazione_pdf,testohtml) VALUES($idpratica,$id_modello,'$file_doc','$idpratica-".$nome.".pdf','$form','$usr','$usr',now(),now(),'".addslashes($m->body)."')";
						if(!$db->sql_query($sql)) print_debug($sql);
						$lastid=$db->sql_nextid();
						$testoedit="Creato il documento <img src=\"images/table.gif\" border=0 onclick=\"window.open(\'stp.editor_documenti.php?id_doc=$lastid\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'stp.editor_documenti.php?id_doc=$lastid\');\">$nome</a>";
						$testoview="Creato il documento <img src=\"images/acrobat.gif\" border=0 onclick=\"window.open(\'stampe/$file_pdf\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'stampe/$file_pdf\');\">$nome</a>";
						
					}
					else{
					
						include("stp.leggi_rtf.php");
						$sql="INSERT INTO stp.stampe(pratica,modello,file_doc,form,utente_doc,data_creazione_doc) VALUES($idpratica,$id_modello,'$file_doc','$form','$usr',now())";
						if(!$db->sql_query($sql)){
							echo "<p class='error-query'>$sql</p>";
						}

						$lastid=$db->sql_nextid();
						$testoedit="Creato il documento <img src=\"images/word.gif\" border=0 onclick=\"window.open(\'stampe/$idpratica-$nome.rtf\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'stampe/$idpratica-$nome.rtf\');\">$nome</a>";
						$testoview="Creato il documento <img src=\"images/word.gif\" border=0 onclick=\"window.open(\'stampe/$idpratica-$nome.rtf\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'stampe/$idpratica-$nome.rtf\');\">$nome</a>";
						//$testoedit="Creato il documento <img src=\"images/word.gif\" border=0 onclick=\"window.open(''stampe/$idpratica-$nome.rtf'');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(''stampe/$idpratica-$nome.rtf'');\">$nome</a>";
						//$testoview="Creato il documento <img src=\"images/word.gif\" border=0 onclick=\"window.open(''stampe/$idpratica-$nome.rtf'');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(''stampe/$idpratica-$nome.rtf'');\">$nome</a>";
					}
					//echo "<p class='error-query'>$sql</p>";

					$today=date('j-m-y'); 
					$sql="INSERT INTO $schema_iter.iter(pratica,data,utente,nota,nota_edit,uidins,tmsins,stampe,immagine) VALUES($idpratica,'$today','$usr','$testoview','$testoedit',".$_SESSION["USER_ID"].",".time().",$lastid,'laserjet.gif');";
					//echo "<p class='error-query'>$sql</p>";

					print_debug($sql);
					$db->sql_query($sql);
				}
			}		
		}
	}
	
?>

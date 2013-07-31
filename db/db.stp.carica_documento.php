<?
include_once "login.php";

//print_array($_REQUEST);
$pr=new pratica($idpratica);
$descr=$_POST['descrizione'];
$propr=$_POST['propr'];
$idpratica=$_POST["pratica"];
$today=date('j-m-y');
$usr=$_SESSION['USER_NAME'];
$schema_iter=$_POST["schema"];
//$err_msg="errore nel caricamento del file!";
$info=pathinfo($_FILES['myfile']['name']);
$filename= str_replace("\'","",$info["filename"]); 
$ext=strtolower($info["extension"]); 
$docname=str_replace("\'","",$info["basename"]);
if (in_array($ext,Array("doc","docx","rtf","xls","pdf","p7m"))) { 
	$filename=implode(".",$tmp);
	if (file_exists($pr->documenti.$docname)){ 
		$i=1;
		do{
			$found=file_exists($pr->documenti.$filename."-$i.$ext");
		}
		while(!$found);
		$docname=$filename."-$i.$ext";
	}
	
		
	switch($ext){
		case "xls":
			$image="xls_hover.png";
			break;
		case "pdf":
			$image="pdf.png";
			break;
		default:
			$image="word.gif";
			break;
	}
	
	
	if (move_uploaded_file($_FILES['myfile']['tmp_name'],  $pr->documenti.$docname)){
        $db=appUtils::getDB();
		$edit="Caricato il documento <img src=\"images/$image\" border=0 onclick=\"window.open('".$pr->url_documenti."$docname');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open('".$pr->url_documenti."$docname');\">$descr</a>";
		$view="Caricato il documento <img src=\"images/$image\" border=0 onclick=\"window.open('".$pr->url_documenti."$docname');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open('".$pr->url_documenti."$docname');\">$descr</a>";
		$data=Array(
            'pratica'=>$idpratica,
            'data'=>'NOW',
            'utente'=>$usr,
            'nota'=>$view,
            'uidins'=>$_SESSION["USER_ID"],
            'tmsins'=>time(),
            'nota_edit'=>$edit,
            'immagine'=>$image
        );
        $db->insert($schema_iter.'.iter',$data);
	}
	else
		$err_msg="errore nel caricamento del file!";
}
else{
	$err_msg="errore tipo di file non corrispondente!";
}


?>

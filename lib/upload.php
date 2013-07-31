<?
//Gestisce l'upload dei file se si tratta di immagini crea la miniatura
	//print_r($_FILES);
	include_once "../login.php";
    require_once APPS_DIR."lib/pratica.class.php";
//print_array($_FILES);
//print_array($_REQUEST);

	$var=ini_get('max_execution_time');
	ini_set('max_execution_time',600);
    
	//inserisco in database
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database $dbtype");
    /*
    $sql="SELECT numero FROM pe.avvioproc WHERE pratica=$idpratica;";
    $db->sql_query($sql);
    $numero=$db->sql_fetchfield('numero');
    $numero=preg_replace("|([^A-z0-9\-]+)|",'',str_replace('/','-',str_replace('\\','-',$numero)));
    $updDir=DATA_DIR ."praticaweb/documenti/$numero/allegati/";*/
    $pr=new pratica($idpratica);
    $updDir=$pr->allegati;
	if (!@move_uploaded_file($_FILES['myfile']['tmp_name'], $updDir. $_FILES['myfile']['name'])) { 
          die("***ERROR: Non è possibile copiare il file.<br />\n". $updDir. $_FILES['myfile']['name']); 
		  exit;
	} 
   
/*   print "Il file è valido, e inviato con successo.  Ecco alcune informazioni:\n"; 
	print "<pre>";
    print_r($_FILES);*/

	$file_name=$_FILES['myfile']['name'];
	$file_type=$_FILES['myfile']['type'];
	$file_size=$_FILES['myfile']['size'];
	

	$sql="select count(id)+1 from pe.file_allegati allegati where pratica=$idpratica and allegato=$idallegato";
	if ($desc) $sql="insert into pe.file_allegati (pratica,allegato,nome_file,tipo_file,size_file,ordine,chk,uidins,tmsins,form,note) values ($idpratica,$idallegato,'$file_name','$file_type',$file_size,($sql),1,".$_SESSION["USER_ID"].",".time().",'$form','$desc');";
	else
		$sql="insert into pe.file_allegati (pratica,allegato,nome_file,tipo_file,size_file,ordine,chk,uidins,tmsins,form) values ($idpratica,$idallegato,'$file_name','$file_type',$file_size,($sql),1,".$_SESSION["USER_ID"].",".time().",'$form');";
	$db->sql_query ($sql);
	$_SESSION["ADD_NEW"]=1;
	if(DEBUG) echo $sql;
	
	//creo l'oggetto immagine se il file è  tipo immagine
	 if (ereg("jpeg",$file_type)){

	$im_file_name =$updDir. $file_name; 
	$image_attribs = getimagesize($im_file_name); 
	$im_old = @imageCreateFromJpeg($im_file_name); 
	
	//setto le dimensioni del thumbnail
	$th_max_width = 100; 
	$th_max_height = 100; 
	
	$ratio = ($width > $height) ? $th_max_width/$image_attribs[0] : $th_max_height/$image_attribs[1]; 
	$th_width = $image_attribs[0] * $ratio; 
	$th_height = $image_attribs[1] * $ratio; 
	$im_new = imagecreatetruecolor($th_width,$th_height); 
	//@imageantialias ($im_new,true); 

	//creo la thumbnail e la copio sul disco
	$th_file_name = $updDir."tmb/tmb_".$file_name; 
	@imageCopyResampled($im_new,$im_old,0,0,0,0,$th_width,$th_height, $image_attribs[0], $image_attribs[1]); 
	@imageJpeg($im_new,$th_file_name,100); 

	ini_set('max_execution_time',$var);
	
}
	?>
<?

$testo=stripslashes(htmlentities($_POST["testo"]));
$testo=preg_replace('|<head>(.+)</head>|Umi',"",$testo);

$id=$_POST["id"];
$id_doc=$_POST["id_doc"];
$id_modelli=$_REQUEST["id_modelli"];
$nomemodello=$_POST["file"]; 
$css_modello=$_POST["css"]; 
//ELENCO DEI TIPI DI TAG
$regexp_cicli='|<span class="cicli">(.*)IN_CICLO(.*)</span>(.+)<span class="cicli">FI_CICLO</span>|Umi';
$regexp_if='|<span class="se">INIZIO_SE</span>(.+)<span class="se">FINE_SE</span>|Umi';
$regexp_tag='|<span class="valore">(.+)</span>|Umi';
$regexp_tag_obbl='|<span class="obbligatori">(.+)</span>|Umi';

if ($_POST["azione"]=="Salva" ){ 
	if(!$testo){
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$sql="SELECT CASE WHEN coalesce(testohtml,'')='' THEN 0 ELSE 1 END as presente FROM stp.e_modelli WHERE nome='$nomemodello'";
	if(!$db->sql_query($sql))
		print_debug($sql,null,"modelli");
	$presente=$db->sql_fetchfield('presente');
	if ($presente){
		$testo=html_entity_decode($testo);
		//str_replace(chr(10),"",$testo);
		$testo=str_replace(chr(13),"",$testo);
		$testo=str_replace('/"','"',$testo);
		$exist=1;
		//$testo=$testo;
				   }
	else{
		include "./lib/modelli.class.php";
		$model=new print_model($testo);
		$model->check();
		$model->save(0,$nomemodello,$form,$css_modello);
		//$testo='<head><LINK media="screen" href="./src/modelli.css" type="text/css" rel="stylesheet"></head>'.	$model->model."";
		$testo=$model->model;
		}	
				}
else{
		include "./lib/modelli.class.php";
		$model=new print_model($testo);
		$model->check(); 
		$model->save($id_modelli,$file,$form,$css_modello); 
		//$testo='<head><LINK media="screen" href="./src/modelli.css" type="text/css" rel="stylesheet"></head>'.$model->model."";	
		$testo=$model->model;
	}
}
elseif ($_POST["azione"]=="Elimina"){
	if ($_POST["tipo"]=="modelli"){
		$sql="DELETE FROM stp.e_modelli WHERE id=$id_modelli";
		if($db->sql_query($sql)){
			@unlink(MODELLI_DIR.$file);
			header("Location: stp.elenco_modelli.php?tipo=html"); 
		}
	}
}
?>
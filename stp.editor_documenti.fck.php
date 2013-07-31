<?
include "login.php";
include("./src/fckeditor/fckeditor.php") ;

/*GESTIONE DEL FILE*/
if ($_REQUEST["id_doc"]){
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$sql="SELECT file_doc,definizione,css.nome,print_type FROM stp.stampe left join stp.e_modelli on(stampe.modello=e_modelli.id) left join stp.css on(css_id=css.id) WHERE stampe.id=".$_REQUEST['id_doc'];
	$db->sql_query($sql);
	$file=$db->sql_fetchfield('file_doc');
	$definizione=$db->sql_fetchfield('definizione');
	$css_name=$db->sql_fetchfield('nome');
	$modal=$db->sql_fetchfield('print_type');
	$tipo="documenti";
	$id_doc=$_REQUEST["id_doc"];
	$id=$_REQUEST["id"];
}

if ($_REQUEST["form"]) $form=$_REQUEST["form"];

$dir=STAMPE;
$action="window.opener.focus();window.close();";
$f=LIB.'HTML_ToPDF.conf';
$handle = fopen($f, "r");
$conf=fread($handle,filesize($f));
fclose($handle);

if ($_POST["azione"] and $_POST["azione"]!=="Annulla" ){
	$testo=stripslashes(htmlentities($_POST["testo"])); 
	//$testo="<html><head><style media=\"print\">$conf</style></head><body>$testo</body></html>";
	include "./db/db.stp.editor_documenti.php";
}
else{

	$sql="SELECT testohtml FROM stp.stampe WHERE id=$_REQUEST[id_doc]";
	$db->sql_query($sql);
	$testo=$db->sql_fetchfield('testohtml');  
}
$testo="<html><head></head><body>$testo</body></html>";
/*FINE GESTIONE DEL FILE*/
?>
<html>
<head>
<title>Editor</title>

<link href="src/screen.css" rel="stylesheet" type="text/css">
<LINK media="screen" href="./src/modelli.css" type="text/css" rel="stylesheet">
<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="./src/fckeditor/fckeditor.js"></script>
<script language="javascript" type="text/javascript" src="./src/ajax2.js"></script>

</head>
<body>
<?//echo "<pre>";print_r($cols);?>
<div class="content">
	<form method="post" name="dati" action="">
	<form name="dati">
		<table width="100%">
			<tr>
				<td valign="top" width="60%">
				
					<?
						$oFCKeditor = new FCKeditor('testo');
						$oFCKeditor->BasePath = 'src/fckeditor/';
						$oFCKeditor->Value = $testo;
						$oFCKeditor->Create();  
					?>
					
					<hr>
					<div style="margin-top:10px;">
						<input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Salva">
						<!--<input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Elimina" onclick="return confirm('Sicuro di voler eliminare questo documento?');">-->
						<input type="button" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" value="Chiudi" onclick="<?=$action?>">
					</div>
				</td>
				<td valign="top" width="40%">
					<div id="rif" name="rif" style="visibility:hidden;border:1px solid black;background-color:rgb(240,240,238);"></div>
					<input type="hidden" name="form" value="<?=$form?>">
					<input type="hidden" name="id_doc" value="<?=$id_doc?>">
					<input type="hidden" name="id" value="<?=$id?>">
					<input type="hidden" name="pratica" value="<?=$id?>">
				</td>
			</tr>
		</table>
	</form>
</div>

</body>
</html>

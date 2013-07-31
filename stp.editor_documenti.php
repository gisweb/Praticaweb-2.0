<?
include "login.php";
//include("./src/fckeditor/fckeditor.php") ;

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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link href="./css/screen.css" rel="stylesheet" type="text/css">
<LINK media="screen" href="./css/modelli.css" type="text/css" rel="stylesheet">
<LINK media="screen" href="./css/styles.css" type="text/css" rel="stylesheet">

<script language="javascript" type="text/javascript" src="./js/LoadLibs.js"></script>
<script language="javascript" type="text/javascript" src="./js/tinymce/jquery.tinymce.js"></script>
<script>
	var ed;
	function saveData(){
		$.ajax({
			url:'/services/xSaveDocument.php',
			dataType:'json',
			type:'POST',
			data:{
				id_doc:$('#id_doc').val(),
				id:$('#id').val(),
				pratica:$('#pratica').val(),
				testo:$('#elm1').html()
			},
			success:function(data){
				$('#message').html(data.message);
			}
		});
	}
	$().ready(function() {
		ed=$('textarea.tinymce').tinymce({
		//ed=new tinymce.Editor('elm1',{
			// Location of TinyMCE script
			script_url : '/js/tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,example",
			language : 'it',
			// Theme options
			theme_advanced_buttons1 : "save,cancel,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/content.css,css/stp.standard.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",
			theme_advanced_font_sizes : "3pt,4pt,5pt,6pt,7pt,8pt,9pt,10pt,11pt,12pt,13pt,14pt,15pt,16pt,17pt,18pt,19pt,20pt",
			
			save_onsavecallback : 'saveData',
			save_oncancelcallback : function(v){
				window.blur();
				(window.open(window.opener.location, window.opener.name) || window).focus();
				window.close();
			}
			// Replace values for the template plugin
			//template_replace_values : {
			//	username : "Some User",
			//	staffid : "991234"
			//}
		});
	});	
	//var w=window.opener;
	
</script>
<style>
	<?php print $definizione ?>
</style>
</head>
<body>
<?//echo "<pre>";print_r($cols);?>
<div class="content">
	<form method="post" name="dati" action="">
	<form name="dati">
		<table width="100%">
			<tr>
				<td valign="top" width="60%">
					<textarea id="elm1" name="testo" rows="40" cols="150" style="width: 90%" class="tinymce">
						<?php echo $testo;?>
					</textarea>
					<?
						//$oFCKeditor = new FCKeditor('testo');
						//$oFCKeditor->BasePath = 'src/fckeditor/';
						//$oFCKeditor->Value = $testo;
						//$oFCKeditor->Create();  
					?>
					
					<hr>
					<div style="margin-top:10px;">
						<div id="btn_close"></div>
						<div id="btn_save"></div>
						
						<script>
							$('#btn_save').button({
								label:'Salva',
								icons:{primary:'ui-icon-disk'}
							}).click(function(){
								saveData();
							});
							$('#btn_close').button({
								label:'Chiudi',
								icons:{primary:'ui-icon-close'}
							}).click(function(){
								window.blur();
								(window.open(window.opener.location, window.opener.name) || window).focus();
								window.close();
							});
						</script>
						<!--<input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Salva">
						<input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Elimina" onclick="return confirm('Sicuro di voler eliminare questo documento?');">
						<input type="button" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" value="Chiudi" onclick="<?=$action?>">-->
					</div>
				</td>
				<td valign="top" width="40%">
					<div id="rif" name="rif" style="visibility:hidden;border:1px solid black;background-color:rgb(240,240,238);"></div>
					<input type="hidden" id="form" name="form" value="<?=$form?>">
					<input type="hidden" id="id_doc" name="id_doc" value="<?=$id_doc?>">
					<input type="hidden" id="id" name="id" value="<?=$id?>">
					<input type="hidden" id="pratica" name="pratica" value="<?=$id?>">
				</td>
			</tr>
			<tr>
				<td colspan="2"><div class="texbox" id="message" style="font-color:#E21818 !important;font-weight:bold;"></div></td>
			</tr>
		</table>
	</form>
</div>

</body>
</html>

<?php
include "login.php";
//include("./src/fckeditor/fckeditor.php") ;

/*GESTIONE DEL FILE*/
if ($_REQUEST["id_doc"]){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
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

<?php
	utils::loadJS(Array("tinymce/tinymce.min","tinymce/jquery.tinymce.min"));
	utils::loadCss(Array("modelli","styles","screen"));
?>
<script>
	window.name = 'PW_Editor';
	var w = window.open("", "praticaweb");
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
			script_url : '/js/tinymce/tinymce.js',
			language : 'it',
			plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
		  ],
		
		  toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
		  toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		  toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",
		
		  menubar: false,
		  toolbar_items_size: 'small',
		
		  style_formats: [{
			title: 'Bold text',
			inline: 'b'
		  }, {
			title: 'Red text',
			inline: 'span',
			styles: {
			  color: '#ff0000'
			}
		  }, {
			title: 'Red header',
			block: 'h1',
			styles: {
			  color: '#ff0000'
			}
		  }, {
			title: 'Example 1',
			inline: 'span',
			classes: 'example1'
		  }, {
			title: 'Example 2',
			inline: 'span',
			classes: 'example2'
		  }, {
			title: 'Table styles'
		  }, {
			title: 'Table row 1',
			selector: 'tr',
			classes: 'tablerow1'
		  }],
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

<div class="content">
	<form method="post" name="dati" action="">
	<form name="dati">
		<table width="100%">
			<tr>
				<td valign="top" width="60%">
					<textarea id="elm1" name="testo" rows="40" cols="150" style="width: 90%" class="tinymce">
						<?php echo $testo;?>
					</textarea>
					<?php
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
								w.focus();
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

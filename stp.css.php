<?
include_once ("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";

$db = $dbconn;
//print_r($_REQUEST);

$file_config="stp/css";
$id=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
print_array($Errors);
if ($_POST["azione"]=="Salva" or $_POST["azione"]=="Elimina") {
	include("./db/db.stp.css.php");
	
	if (!$Errors) $modo="list";
}

elseif($_REQUEST["azione"]=='Annulla'){
	$modo="list";
}


?>
<html>
<head>
<title>Fogli di stile </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language=javascript src="src/x_core.js" type="text/javascript"></SCRIPT>
<SCRIPT language=javascript>
function link(i){
	document.css_v.mode.value="edit";
	document.css_v.id.value=i;
	document.css_v.submit();
}
function preview(fName){
	var f=xGetElementById(fName);
	f.target='_new';
	f.action='previewPDF.php';
	f.submit();
	f.target='';
	f.action='';
}
</SCRIPT>
</head>
<?
include "./inc/inc.page_header.php";
if (($modo=="edit") or ($modo=="new")) {
	$size_opt=array('A4'=>'a4','A3'=>'a3','A2'=>'a2','A1'=>'a1','A0'=>'a0');
	$or_opt=array('Verticale'=>'portrait','Orizzontale'=>'landscape');
	
	$sql="select * from stp.css where id=$id";
	$db->sql_query($sql);
	$risultato=$db->sql_fetchrowset();
	$nome=$risultato[0]['nome'];
	$def=$risultato[0]['definizione'];
	$script=$risultato[0]['script'];
	$desc=$risultato[0]['descrizione'];
	$size=$risultato[0]['dimensione'];
	$or=$risultato[0]['orientamento'];
	
	unset($_SESSION["ADD_NEW"]);	
	foreach($size_opt as $key=>$val){
		$sel=($size==$val)?('selected'):('');
		$tmp[]="<option value=\"$val\" $sel>$key</option>";
	}
	$size_list=@implode('\n\t',$tmp);
	$tmp=Array();
	foreach($or_opt as $key=>$val){
		$sel=($or==$val)?('selected'):('');
		$tmp[]="<option value=\"$val\" $sel>$key</option>";
	}
	$or_list=@implode('\n\t',$tmp);
?>

<BODY>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="css" name="css" method="post" action="stp.css.php">		  
			<tr> 
				<td> 
				<H2 class="blueBanner">Inserimento di uno stile di stampa</H2>	  
				</td>
			</tr>
			
			<tr><td><b>Nome:</b></td></tr>
			<tr><td><input type="text" name="nome" value="<?=$nome?>"></td></tr>
			<tr><td><b>Definizione css:</b></td></tr>
			<tr><td><textarea name="definizione" rows="5" cols="80" value="$def"><?=$def?></textarea></td></tr>
			<tr><td><b>Script:</b></td></tr>
			<tr><td><textarea name="script" rows="5" cols="80" value="$script"><?=$script?></textarea></td></tr>
			<tr><td><b>Descrizione:</b></td></tr>
			<tr><td><textarea name="descrizione" rows="5" cols="80" value="$desc"><?=$desc?></textarea></td></tr>
			<tr><td><b>Dimensione foglio:</b>
			<select name="dimensione">
			<?php echo $size_list;?>
			</select>
			<b>Orientamento foglio:</b>
			<select name="orientamento">
			<?php echo $or_list;?>
			</select></td></tr>
			
			<tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   	</tr>
			<tr>
				<TD valign="bottom" height="50">
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva">
				<?if ($modo=="edit"){?><input name="azione" type="submit" class="hexfield" tabindex="14" value="Elimina" onclick="return confirm('Sicuro di volerlo eliminare ?');">
				<input name="azione" type="button" class="hexfield" tabindex="14" value="Anteprima" onclick="javascript:preview('css');"><?}?>
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla">
				
				</TD>
			</tr>
			<input type="hidden" value="stp.css.php" name="active_form">
			<input type="hidden" name="mode" value="<?=$modo?>">
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" value="stp/css.tab" name="config_file">
		</form>
		</TABLE>
		
		<?if($modo=="edit"){	?>
		<form name="css_v" method="POST" action="stp.css.php">
		<input type="hidden" name="mode" value="<?=$modo?>">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="hidden" value="stp.css.php" name="active_form">
		<input type="hidden" value="stp/css.tab" name="config_file">
		</form>
		
</BODY> 
<?include "./inc/inc.window.php"; // contiene la gesione della finestra popup
}} elseif($modo=="list") {
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA TUTTI DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<body>
	<H2 class="blueBanner">Elenco degli stili di stampa</H2>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_h($file_config,'list');echo $file_config;
				$tabella->set_titolo("Elenco stili di stampa","nuovo");
				$tabella->get_titolo();
				$nrec=$tabella->set_dati("true");
				$tabella->elenco();
				$tabella->close_db();?>
				<form name="css_v" method="POST" action="stp.css.php">
					<input type="hidden" name="mode" value="<?=$modo?>">
					
					<input type="hidden" name="id" value="<?=$id?>">
				</form>
			<!-- fine contenuto-->
			 </TD>
	      	</TR>
	      	<TR>
	      		<TD valign="bottom" height="50"><input name="close" type="button" class="hexfield" tabindex="14" value="Chiudi" onclick="javascript:window.parent.focus();window.close();"></TD>
		</TR>
	</TABLE>

</body>	

<?}?>

<?
include_once ("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";

//print_r($_REQUEST);

$tabpath="ce";
$file_config="$tabpath/membri";
$id=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

if ($_POST["azione"]=="Salva" or $_POST["azione"]=="Elimina") {
	include("./db/db.ce.membri_paes.php");
	if (!$Errors) $modo="all";
}

elseif ($_POST["azione"]=="Chiudi" and $modo=="view") $modo="all";
elseif ($_POST["azione"]=="Annulla" and $modo="new") $modo="all";
elseif ($_POST["azione"]=="Annulla") $modo="view";


?>
<html>
<head>
<title>Membri della Commissione Locale per il Paesaggio </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language=javascript>
function link(i){
	document.membri_v.mode.value="view";
	document.membri_v.id.value=i;
	document.membri_v.submit();
}
</SCRIPT>
</head>
<?
include "./inc/inc.page_header.php";
if (($modo=="edit") or ($modo=="new")) {

	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);	
?>
<BODY>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="membri" method="post" action="ce.membri_paes.php">		  
			<tr> 
				<td> 
				<H2 class="blueBanner">Inserimento o modifica di un membro</H2>
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();?>			  
				</td>
			</tr>
			<tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   	</tr>
			<tr>
				<TD valign="bottom" height="50">
                           
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva">
				<?if ($modo=="edit"){?><input name="azione" type="submit" class="hexfield" tabindex="14" value="Elimina" onclick="return confirm('Sicuro di volerlo eliminare ?');"><?}?>
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla">
				
				</TD>
			</tr>
                    
			<input type="hidden" name="mode" value="<?=$modo?>">
			<input type="hidden" name="id" value="<?=$id?>">
		</form>
		</TABLE>
		
</BODY>
<?include "./inc/inc.window.php"; // contiene la gesione della finestra popup
} elseif($modo=="all") {
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA TUTTI DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<body>
	<H2 class="blueBanner">Elenco dei membri delle Commissioni Locali per il Paesaggio</H2>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_h($file_config."_paes",'list');
				$tabella->set_titolo("Elenco membri","nuovo");
				$tabella->get_titolo();
				$nrec=$tabella->set_dati("pratica=0");
				$tabella->elenco();
				$tabella->close_db();?>
				<form name="membri_v" method="POST" action="ce.membri_paes.php">
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

<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

<?
} else {
	
	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);?>
<body>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
		<TR>
			<TD>
			<?
				$tabella->set_titolo("Membro della Commissione Locale per il Paesaggio","modifica",array("id"=>$id));
				$tabella->set_dati("id=$id");
				$tabella->get_titolo();
				$tabella->edita();?>
				
			</TD>
			
		</TR>
		<TR>
			<TD></TD>
		</TR>
	</TABLE>
	<form name="membri_v" method="POST" action="ce.membri_paes.php">
		<input type="hidden" name="mode" value="<?=$modo?>">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="submit" name="azione" class="hexfield" tabindex="14" value="Chiudi">
	</form>
</body>
<?}?>

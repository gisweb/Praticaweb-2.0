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
	include("./db/db.savedata.php");
	if (!$Errors) $modo="all";
}
elseif ($_POST["azione"]=="Chiudi" and $modo=="view") $modo="all";
elseif ($_POST["azione"]=="Annulla" and $modo="new") $modo="all";
elseif ($_POST["azione"]=="Annulla") $modo="view";


?>
<html>
<head>
<title>Membri della Commissione Edilizia </title>
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
	<FORM id="" name="membri" method="post" action="ce.membri.php">	
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
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
			<input type="hidden" name="mode" value="<?=$modo?>">
			<input type="hidden" name="id" value="<?=$id?>">
		
		</TABLE>
	</form>	
</BODY>
<?php
include "./inc/inc.window.php"; // contiene la gesione della finestra popup
} elseif($modo=="all" or $modo=='list') {
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA TUTTI DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<body>
	<H2 class="blueBanner">Elenco dei membri delle Commissioni Edilizie</H2>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_h($file_config,'list');
				$tabella->set_titolo("Elenco membri","nuovo");
				$tabella->get_titolo();
				$nrec=$tabella->set_dati("pratica=0");
				$tabella->elenco();
				$tabella->close_db();?>
				<form name="membri_v" method="POST" action="ce.membri.php">
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
	
	$tabella=new Tabella_v($file_config);					
	unset($_SESSION["ADD_NEW"]);?>
<body>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
		<TR>
			<TD>
			<?
				$tabella->set_titolo("Membro della Commissione Edilizia","modifica",array("id"=>$id));
				$tabella->set_dati("id=$id");
				$tabella->get_titolo();
				$tabella->edita();?>
				
			</TD>
			
		</TR>
		<TR>
			<TD></TD>
		</TR>
	</TABLE>
	<form name="membri_v" method="POST" action="ce.membri.php">
		<input type="hidden" name="mode" value="<?=$modo?>">
		<input type="hidden" name="id" value="<?=$id?>">
	</form>
</body>
<?}?>

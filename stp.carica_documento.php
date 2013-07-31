<?
session_start();
include_once "login.php";
$schema=$_REQUEST['schema'];
$idpratica=$_REQUEST['pratica'];
$usr=$_REQUEST['utente'];
$form=$_REQUEST['form'];
$procedimento=$_REQUEST['procedimento'];
$upfile=$_FILES['myfile']['name'];
//print_array($_REQUEST);
if ($_POST["azione"])
	include("./db/db.stp.carica_documento.php");		
?>
<html>
<head>
<title>Carica modello di stampa</title>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="JavaScript">
function chiudi(){
	
	win=window.opener;
	win.document.iter.azione.value="";
	win.document.iter.submit();
	win.focus();
	window.close();
}
</script>
<body onload="<?if ($_POST["azione"] and ($err_msg=='File caricato correttamente!')) print("javascript:chiudi();");?>">
<form name='upload' action='stp.carica_documento.php' method='POST' enctype="multipart/form-data">
	<table width="500" class="stiletabella"><td colspan=3 bgColor="#728bb8"><font face="Verdana" color="#FFFFFF" size="2"><b>Carica Documento</b></font></td>
		<tr>
			<td colspan=2><img src="images/gray_light.gif" height="4" width=100% style="margin-bottom:5"></td>
		</tr>
		<tr>
			<td colspan=2>
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<input name='myfile' type='file' style="width:360px;FONT: 11px/1.3em">
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<table width=450 border=2 bordercolor="#728bb8" class="stiletabella">
					<tr bgcolor="#728bb8">
						<td width="300">
							<font face="Verdana" color="#FFFFFF" size="2"><b>Descrizione</b></font>
						</td>
					<tr>
					<tr>
						<td colspan="2">				
							<input type="text" name='descrizione' style="width:360px;FONT: 11px/1.3em">
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><?=$err_msg?></td>
		</tr>
		<tr>
			<td>
				<input type="button" class="hexfield1" onclick="window.opener.focus();window.close();" value="Chiudi">
			</td>
			<td>
				<input class="hexfield1" type="submit" name='carica' value="Carica File" onclick="javascript:if (myfile.value.length>0) return true;else {alert('Inserire un file da caricare');return false;}">
			</td>
			
		</tr>
	</table>
	<input type="hidden" name="pratica" value="<?=$idpratica?>">
	<input type="hidden" name="schema" value="<?=$schema?>">
	<input type=hidden name="form" value="<?=$form?>">
	<input type=hidden name="procedimento" value="<?=$procedimento?>">
	
	<input type=hidden name="azione" value="carica_documento">
</form>
</body>
</html>

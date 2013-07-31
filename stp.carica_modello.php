<?
session_start();
include_once "login.php";
$idpratica=$_REQUEST['pratica'];
$usr=$_REQUEST['utente'];
$form=$_REQUEST['form'];
$procedimento=$_REQUEST['procedimento'];
$upfile=$_FILES['myfile']['name'];	
if ($_POST["azione"])
	include("./db/db.stp.carica_modello.php");		
?>
<html>
<head>
<title>Carica modello di stampa</title>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="JavaScript">
function get_parent(){
alert(window.opener.name);
}
function chiudi(){
	
	win=window.opener;
	win.document.main.azione.value='';
	win.document.main.submit();
	win.focus();
	window.close();
}
</script>
<body onload="<?if ($_POST["azione"] and !$err_msg) print("javascript:chiudi();");?>">
<form name='upload' action='stp.carica_modello.php' method='POST' enctype="multipart/form-data">
	<table width="500" class="stiletabella"><td colspan=3 bgColor="#728bb8"><font face="Verdana" color="#FFFFFF" size="2"><b>Carica nuovo modello</b></font></td>
		<tr>
			<td colspan=3><img src="images/gray_light.gif" height="4" width=100% style="margin-bottom:5"></td>
		</tr>
		</tr>
		<tr>
			<td width="300">
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<input name='myfile' type='file' style="width:360px;FONT: 11px/1.3em">
			</td>
			<td >
				<input class="hexfield1" type="submit" name='carica' value="Carica File" style="width:100px;margin-left:53px" onclick="javascript:if (myfile.value.length>0) return true;else {alert('Inserire un file da caricare');return false;}">
			</td>
			<td>&nbsp;
			</td>
		</tr>
		<tr>
			<table width=500 border=2 bordercolor="#728bb8" class="stiletabella">
				<tr bgcolor="#728bb8">
					<td width="300">
						<font face="Verdana" color="#FFFFFF" size="2"><b>Descrizione</b></font>
					</td>
                                   <td>
						<font face="Verdana" color="#FFFFFF" size="2"><b>Tipo pratica</b></font>
					</td>

                            </tr>
                            <tr>
					<td >				
						<textarea name='descrizione' cols=35 rows=4></textarea>
					</td>
                                   <td>
                                          <input type="radio" name="tipopr" value="0" checked>Non specificato<br>
						<input type="radio" name="tipopr" value="11000">Permesso di costruire<br>
						<input type="radio" name="tipopr" value="10000">Denuncia di inizio attivitÃ <br>
                                          <input type="radio" name="tipopr" value="18000">Comunicazione avvio attivitÃ <br>
						<input type="radio" name="tipopr" value="14000">Autorizzazione Paes. Amb.<br>
						<input type="radio" name="tipopr" value="12000">Abuso Edilizio<br>
                                          <input type="radio" name="tipopr" value="13000">Nulla Osta<br>
                                          <input type="radio" name="tipopr" value="16000">Condono Edilizio
					</td>

                           </tr>
                            <tr  bgcolor="#728bb8">

                                   <td>
						<font face="Verdana" color="#FFFFFF" size="2"><b>Categoria</b></font>
					</td>

					<td>
						<font face="Verdana" color="#FFFFFF" size="2"><b>Proprietario</b></font>
					</td>
				</tr>
				
                           <tr>
                                   <td >				
						<input type='text' name='categoria' width='30'>
					</td>

					
					<td>
						<input type="radio" name="propr" value="<?=$usr?>" style="margin-bottom:5">Privato<br>
						<input type="radio" name="propr" value="pubblico" checked>Pubblico
					</td>
				</tr>
			</table>
		</tr>
		<tr>
			<td colspan="3" align="center"><?=$err_msg?></td>
		</tr>
	</table>
	<input type="hidden" name="pratica" value="<?=$idpratica?>">
	<input type=hidden name="form" value="<?=$form?>">
	<input type=hidden name="procedimento" value="<?=$procedimento?>">
	<input type=hidden name="azione" value="carica_modello">
	<div align="center"><input type="button" class="hexfield1" onclick="self.close();" value="Chiudi" style="margin-top:20;"></div>
</form>
</body>
</html>

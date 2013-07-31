<?
session_start();
include_once "login.php";

?>
<html>
<head>
<title>Carica foto</title>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
<script language="JavaScript">
function get_parent(){
alert(window.opener.name);
}

</script>
<?
//Pagina di caricamento dei modelli di stampa
$idpratica=$_GET['pratica'];
$form=$_GET['form'];
$soprall=$_GET['id_sopralluoghi'];
$upfile=$_FILES['myfile']['name'];			
	/*$tabella="\n<body><form name='upload' action='sopralluoghiv.php' method='POST' enctype=\"multipart/form-data\" onsubmit=\"javascript:if (myfile.value.length>0) window.close(); else return false;\">
	<table width=\"800\" class=\"stiletabella\"><td colspan=3 bgColor=\"#728bb8\"><font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\"><b>Carica nuovo modello</b></font></td>
		<tr>
			<td colspan=3><img src=\"images/gray_light.gif\" height=\"4\" width=100% style=\"margin-bottom:5\"></td>
		</tr>
		</tr>
		<tr>
			<td width=\"300\">
				<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"3000000\">
				<input name='myfile' type='file' style=\"width:360px;FONT: 11px/1.3em\">
			</td>
			<td >
				<input class=\"hexfield1\" type=\"submit\" name='carica' value=\"Carica File\" style=\"width:100px;margin-left:53px\" onclick=\"javascript:this.form.target=window.opener.name\">
			</td>
			<td>&nbsp;
			</td>
		</tr>
		<tr>
			<table width=500 border=2 bordercolor=\"#728bb8\" class=\"stiletabella\">
				<tr bgcolor=\"#728bb8\">
					<td width=\"300\">
						<font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\"><b>Descrizione</b></font>
					</td>
				</tr>
				<tr>
					<td >				
						<textarea name='descrizione' cols=50 rows=4></textarea>
					</td>
				</tr>
			</table>
		</tr>
	</table>";
	print $tabella;*/?>
	<body>
	<form name='upload' action='sopralluoghiv.php' method='POST' enctype="multipart/form-data" onsubmit="javascript:if (myfile.value.length>0) window.close(); else return false;">
	<table width="400" class="stiletabella"><td colspan=3 bgColor="#728bb8"><font face="Verdana" color="#FFFFFF" size="2"><b>Carica una nuova foto</b></font></td>
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
				<input class="hexfield1" type="submit" name='carica' value="Carica File" style="width:100px;margin-left:53px" onclick="javascript:this.form.target=window.opener.name">
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
				</tr>
				<tr>
					<td >				
						<textarea name='descrizione' cols=62 rows=3></textarea>
					</td>
				</tr>
			</table>
		</tr>
	</table><?
	$tmp="\t<input type=hidden name=\"pratica\" value=\"$idpratica\">
	<input type=hidden name=\"form\" value=\"vigilanza\">
	<input type=hidden name=\"id_sopralluoghi\" value=\"$soprall\">
	<input type=hidden name=\"pratica\" value=\"$idpratica\">
	<input type=hidden name=\"nfile\" value=\"\">
	<input type=hidden name=\"mode\" value=\"edit\">
	<input type=hidden name=\"modal\" value=\"add\">\n</form>\n</body>\n</html>\n";
	print $tmp;

?>

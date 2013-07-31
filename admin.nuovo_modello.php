<html>
<head>
	<title>Nuovo modello di stampa</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?include "./inc/inc.page_header.php";?>
	<H2 class="blueBanner">Nuovo modello di Stampa</H2>
	<form name="" action="admin.modelli.php" method="POST">
		<TABLE cellPadding=3  cellspacing=2 border=0 class="stiletabella" width="99%" align="center">
			<tr>
				<td width="20%">Nome del Documento</td>
				<td><input type="text" name="modello" value=""></td>
			</tr>
			<tr>
				<td width="20%">Margini del documento</td>
				<td><input type="text" name="margini" value=""></td>
			</tr>
			<tr>
				<td width="20%">Intestazione del documento</td>
				<td><SELECT name="intestazione">
						<OPTION value="1">SI</OPTION>
						<OPTION value="0">NO</OPTION>
					</SELECT>
				</td>
			</tr>
		</table>
		<HR>
		<table>
			<tr>
				<td width="20%"><input class="hexfield" type="button" name="azione" value="Chiudi" onclick="window.opener.focus();window.close();"></td>
				<td><input class="hexfield" type="submit" name="azione" value="Avanti >>>"></td>
			</tr>
		</table>
	</form>
</body>

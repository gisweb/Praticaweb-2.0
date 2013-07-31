<?
include_once("./login.php");
include_once "./lib/tabella_h.class.php";

$tabpath="cn";
$file_config="tipoxallegati";

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

?>
<html>
<head>
	<title>Gestione modelli di Stampa</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language=javascript src="src/window.js" type=text/javascript></SCRIPT>
	<SCRIPT>
		function link(nome){
			pagina="admin.editor.php?file="+nome;
			window.open(pagina,'new',"menubar=0,toolbar=1,scrollbars=1,title=0,resizable=1");
		}
	</SCRIPT>
</head>
<body>
	<?include "./inc/inc.page_header.php";
	if ($modo=="new"){?>
	<H2 class="blueBanner">Nuovo modello di Stampa</H2>
	<form name="" action="admin.editor.php" method="POST">
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
	<?}
	elseif ($modo=="view"){
		$tabella=new tabella_h("$tabpath/$file_config",$modo);
		$tabella->def_col[0][0]="";
		$tabella->def_col[1][0]="Nome del Modello";
		$tabella->def_col[0][1]="modello";
		$tabella->def_col[1][1]="nome";
		$tabella->def_col[0][2]="5%";
		$tabella->def_col[1][2]="95%";
		$tabella->def_col[0][3]="info";
		$tabella->def_col[1][3]="text";
		
		if ($handle = opendir(MODELLI_DIR)) {
			$i=0;
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") {
					if (count(explode(".",$file))==2){
						list($nome,$ext)=explode(".",$file);
						if (strtolower($ext)=="html" or strtolower($ext)=="htm"){
							$tabella->array_dati[$i]["modello"]=$file;
							$tabella->array_dati[$i]["nome"]=$nome;
							$i++;
						}
					}
				} 
			}
			closedir($handle); 
		}
		//echo "<pre>";print_r($tabella);
		$tabella->set_titolo("Elenco dei Modelli di Stampa");
		$tabella->get_titolo();
		$tabella->num_record=$i;
		$tabella->elenco();
	}
	?>
	
</body>
</html>
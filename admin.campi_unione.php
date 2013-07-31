<?
include "login.php";
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$sql="SELECT alias_nome,nome_vista,descrizione,tag FROM stp.colonne WHERE visibile=1 order by nome_vista,alias_nome;";
$db->sql_query($sql);
$ris=$db->sql_fetchrowset();
for($i=0;$i<count($ris);$i++){
	$tabella[$ris[$i]["nome_vista"]][$i]["alias_nome"]=$ris[$i]["alias_nome"];
	$tabella[$ris[$i]["nome_vista"]][$i]["descrizione"]=$ris[$i]["descrizione"];
	$tabella[$ris[$i]["nome_vista"]][$i]["tag"]=$ris[$i]["tag"];
}	
?>
<html>
<head>
<title>Pagina di Informazione sui campi per i modelli di stampa</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="JavaScript">
	function show(i){
		
		st=document.getElementById("info-"+i).style;
		if (st.display=='none') st.display='';
		else
			st.display='none';
	}
</script>
</head>

<body>
<?include "./inc/inc.page_header.php";?>
<H2 class="bluebanner">ELENCO DELLE TABELLE</H2>
	<table width="99%" class="stiletabella">
	<?	$i=0;
		foreach($tabella as $key=>$val){
		$i++;
		echo "\n\t\t<tr>
			<td><a onclick=\"show($i)\" onmouseover=\"this.style.cursor='hand'\" onmouseout=\"this.style.cursor='pointer'\"><b>$key</b></a>";?>
				<!-- ricerca avanzata pratica -->
				<DIV id="info-<?=$i?>" style="DISPLAY: none">
					<table cellPadding="1" border="1" class="stiletabella" width="100%">
						<tr>
							<td width="5%">&nbsp;</td>
							<td><b>COLONNA</b></td>
							<td><b>DESCRIZIONE</b></td>
							<td><b>TAG DI INSERIMENTO</b></td>
						</tr>
					<?foreach($val as $v) echo "\n\t\t\t\t\t\t<tr>
							<td width=\"5%\">&nbsp;</td>
							<td>".$v["alias_nome"]."&nbsp;</td>
							<td>".$v["descrizione"]."&nbsp;</td>
							<td>".$v["tag"]."&nbsp;</td>
						</tr>"; ?>
					</table>
					<img onclick="show(<?=$i?>)" src="images/top.gif" >Chiudi
				</DIV>
			<hr></td>
		</tr>
	<?} ?>
	</table>
</body>
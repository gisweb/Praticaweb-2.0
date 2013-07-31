<?
//print_r($_REQUEST);
include_once ("login.php");
if ($_SESSION["PERMESSI"]>2){
	include_once HOME;
	exit;
}
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$tabpath="oneri";
$file_config="$tabpath/tariffe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$anno=$_POST["anno"];

if ($_POST["azione"]=="Salva") {
	include("./db/db.oneri.tariffe.php");
	if (!$Errors) {
		$modo="view";
		
		//$pratica=$codice;
	}
}

?>
<html>
<head>
<title>Aggiornamento tariffe Oneri</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<?
include "./inc/inc.page_header.php";
if ($modo=="new") {

	$tabella=new Tabella_v($file_config,$modo);	
	unset($_SESSION["ADD_NEW"]);	
?>
<BODY>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM method="post" action="oneri.tariffe.php">		  
			<tr> 
				<td> 
				<H2 class="blueBanner">Inserimento nuove tariffe degli oneri</H2>
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
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
				<input name="azione" type="button" class="hexfield" tabindex="14" value="Annulla" onclick="window.location='oneri.elenco_tariffe.php'">
				</TD>
			</tr>
			<input type="hidden" name="mode" value="<?=$modo?>">
			
		</form>
		</TABLE>
		
</BODY>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

<?
include "./inc/inc.window.php";
} elseif ($modo=="view") {
	
	$tabella=new Tabella_h($file_config);					
	unset($_SESSION["ADD_NEW"]);?>
<body>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
		<TR>
			<TD>
			<?
				$tabella->set_titolo("Tariffe Oneri per l'anno ".$anno,'modifica',Array("mode"=>"edit","anno"=>$anno));
				$tabella->set_dati("anno=$anno");
				$tabella->get_titolo();
				$tabella->elenco();?>
				
			</TD>
			
		</TR>
		<TR><TD></TD></TR>
		<TR>
			<TD><input type="button" name="azione" class="hexfield" tabindex="14" value="Chiudi" onclick="window.location='oneri.elenco_tariffe.php'"></TD>
		</TR>
	</TABLE>
</body>
<?}
elseif($modo=="edit"){
$tabella=new Tabella_h($file_config);
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql="select funzione,tr,a from oneri.e_tariffe where anno=".$anno.";";
//echo "<p>$sql</p>";
if(!$db->sql_query($sql)) print_debug($sql);
$destuso=$db->sql_fetchlist('funzione');
$tr=$db->sql_fetchlist('tr');
$a=$db->sql_fetchlist('a');
?>
<FORM method="post" action="oneri.tariffe.php">
<table cellPadding="1"  cellspacing="1" border="0" class="stiletabella" width="95%">
	
	<?	
		$destuso_col[]="<td></td>";
		$tr_col[]="<td bgcolor=\"#E7EFFF\" style=\"font-family:Verdana;color:#415578;font-size:10px;font-weight:bold\">TR</td>";
		$a_col[]="<td bgcolor=\"#E7EFFF\" style=\"font-family:Verdana;color:#415578;font-size:10px;font-weight:bold\">A</td>";
		for($i=0;$i<count($destuso);$i++){
			$destuso_col[]="<td align=\"center\" style=\"font-family:Verdana;color:#415578;font-size:12px;font-weight:bold\">".$destuso[$i]."</td>";
			$tr_col[]="<td bgcolor=\"#E7EFEF\" align=\"center\"><input type=\"text\" name=\"tr[".$destuso[$i]."]\" class=\"textbox\" size=\"6\" value=\"".$tr[$i]."\"></input></td>";
			$a_col[]="<td bgcolor=\"#E7EFEF\" align=\"center\"><input type=\"text\" name=\"a[".$destuso[$i]."]\" class=\"textbox\" size=\"6\" value=\"".$a[$i]."\"></input></td>";
		}
		print "<tr>";
		print "<td colspan=\"9\" width=\"95%\" bgColor=\"#728bb8\" style=\"font-family:Verdana;color:#FFFFFF;font-size:13px;font-weight:bold;\">Tariffe oneri per l'anno 2005</td>";
		print "</tr>";
		
		/*echo "<TR><TD colspan=\"9\">";
		$tabella->set_titolo("Tariffe Oneri per l'anno ".$anno);
		$tabella->get_titolo();
		echo "</TD></TR>";*/
		
		print "<tr><td bgcolor=\"#B7CFFF\"></td>";
		print "<td align=\"center\" bgcolor=\"#B7CFFF\" colspan=\"9\" style=\"font-family:Verdana;color:#415578;font-size:16px;font-weight:bold\">Destinazione d'uso</td></tr>";
		print "<tr height=\"30px\" bgcolor=\"#E7EFFF\">".implode("",$destuso_col)."</tr>\n";
		print "<td>";
		print "<tr>".implode("",$tr_col)."</tr>\n";
		print "<tr>".implode("",$a_col)."</tr>\n";
		print "<tr>";
		print "<td colspan=\"9\"><hr style=\"margin-top:5px;margin-bottom:5px\"><input name=\"azione\" type=\"submit\" class=\"hexfield\" tabindex=\"14\" value=\"Salva\" style=\"margin-right:20px;margin-left:8px\">";
		print "<input name=\"azione\" type=\"button\" class=\"hexfield\" tabindex=\"14\" value=\"Annulla\" onclick=\"window.location='oneri.elenco_tariffe.php'\"></td>";
		print "</tr>";
		print "<input type=\"hidden\" name=\"mode\" value=\"edit\">";
		print "<input type=\"hidden\" name=\"anno\" value=\"$anno\">";
	?>
</table>
</FORM>
</body>	
	

<?


}
?>
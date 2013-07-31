<?
include_once ("login.php");
include_once "./lib/tabella_v.class.php";
if ($_POST["azione"]=="Elimina") include "./db/db.pe.eliminapratica.php";

?>
<html>
<head>
	<TITLE>Elimina Pratica Esistente</TITLE>
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
</head>
<body>

<?
include "./inc/inc.page_header.php";	
if ($_REQUEST["ricerca"]){
	$tabella=new tabella_v("pe/eliminapratica_ric.tab");
	$tabella->set_titolo("Ricerca Pratica");
	
?>
	<br>	
	<form action="pe.eliminapratica_amb.php" method="POST">
	<table width="70%">
		<tr> 
			<td colspan="2"> 
			<!-- intestazione-->
				<H2 class="blueBanner">Procedura di Eliminazione delle Pratiche Edilizie</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		<tr><TD colspan="2" width="90%">
		
		<?
		$tabella->get_titolo();
		$tabella->edita();
		?>
		</TD></tr>
		<tr><TD colspan="2">&nbsp;</TD></tr>
		<tr>
			<td colspan="2">
				<input class="hexfield" type="button" value="Annulla" onclick="window.opener.focus();window.close();">
				<input class="hexfield" type="submit" name="azione" value="Cerca" onclick="if (numero.value.length>0) return true; else{alert('Inserire un nÂ° di pratica');return false;}">
			</td>
		</tr>
		
	</table>
	</form>
	
<?}
else{
	include_once "./lib/tabella_h.class.php";
	
	$tabella_v=new tabella_v("pe/eliminapratica_info_amb.tab");
	$tabella_ubi=new tabella_h("pe/eliminapratica_ubi.tab");
	$tabella_richied=new tabella_h("pe/eliminapratica_richied.tab");
	
	$tabella_v->set_titolo("Dati Pratica");
	$tabella_ubi->set_titolo("Ubicazione");
	$tabella_richied->set_titolo("Richiedenti");
	
	$numrows=$tabella_v->set_dati("numero='".$_POST["numero"]."'");
	$pratica=$tabella_v->array_dati[0]["pratica"];
	$numrows_u=$tabella_ubi->set_dati("pratica=".$pratica."");
	$numrows_r=$tabella_richied->set_dati("pratica=".$pratica." and richiedente=1");
?>

	<form action="pe.eliminapratica_amb.php" method="POST" name="main">
	<table width="70%">
		<tr> 
			<td colspan="2"> 
			<!-- intestazione-->
				<H2 class="blueBanner">Procedura di Eliminazione delle Pratiche Edilizie</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		<tr><TD colspan="2" width="90%">
		
		<?
		$tabella_v->get_titolo();
		if ($numrows>0){			 
			 $tabella_v->edita();
			 $tabella_ubi->get_titolo();
			 if ($numrows_u) $tabella_ubi->elenco();
			 else
			 	print ("<p><b>Nessuna ubicazione salvata</b></p>");
			 $tabella_richied->get_titolo();
			 if ($numrows_r) $tabella_richied->elenco();
			 else
			 	print ("<p><b>Nessun richiedente salvato</b></p>");
		}
		else
			print ("<p><b>Pratica nÂ° ".$_POST["numero"]." non trovata</b></p>");
		?>
		</TD></tr>
		<tr><TD colspan="2">&nbsp;</TD></tr>
		<tr>
			<td colspan="2">
				<input class="hexfield" type="submit" value="Annulla">
				<?if ($numrows>0){?> <input class="hexfield" type="submit" name="azione" value="Elimina" onclick="return confirm('Sicuro di voler procedere con l\'eliminazione della pratica ?');"><?}?>
				<input type="hidden" name="ricerca" value="1">
				<input type="hidden" name="pratica" value="<?=$pratica?>">
				<input type="hidden" name="config_file" value="pe/eliminapratica.tab">
			</td>
		</tr>
		
	</table>
	</form>
<?}?>
</body>
</html>
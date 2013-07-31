<?include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>
<title>Elimina - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente la pratica indicata?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}
</script>


</head>
<body>
<?
		$tabella=new tabella_v("$tabpath/elimina.tab");
		include "./inc/inc.page_header.php";?>	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="pe.elimina.php">		  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Elimina pratica selezionata</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?
				if ($_POST["azione"]) include "./db/db.pe.elimina.php";
				$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="center"><img src="images/gray_light.gif" height="2" width="99%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>				
			<td>
				<input  name=""  id="" class="hexfield1"  type="button" value="Chiudi" onClick="javascript:window.open('<?=HOME?>','indexPraticaweb');window.close()"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield1" tabindex="14" value="Elimina" onClick="return confirmSubmit()"></td>
		</FORM>		
		</table>	

</body>
</html>

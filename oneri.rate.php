<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="oneri";
?>
<html>
<head>
<title>Rate - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente la rata corrente?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}
</script>
</head>
<body  background="">
<?php
if ($modo=="edit"){
		include "./inc/inc.page_header.php";
		$id=$_POST["id"];
		$nome_rata=$_POST["descrizione"];
		$tabella=new tabella_v("$tabpath/rate",$modo);?>	
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="protocollo" name="protocollo" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Rata</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
<?php
                        if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();?>				
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
		<table>
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="active_form" type="hidden" value="oneri.rate.php">
				
		</FORM>			

<?php }
else{
		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>
		<H2 class="blueBanner">Elenco Rate</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
<?php
                                $tabella=new tabella_v("$tabpath/rate");
				$nrec=$tabella->set_dati("pratica=$idpratica");
				if($nrec){
					$tabella->set_titolo("Rate Oneri","modifica",array("titolo"=>"","id"=>""));
					$tabella->elenco();
				}else
				{
					$tabella->set_titolo("Nuova Rateizzazione","nuovo");
					$tabella->get_titolo();
					print ("<p> Mancano le rate </p>");
				}
	
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->

			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>	
		</TABLE>
<?php }?>

</body>
</html>

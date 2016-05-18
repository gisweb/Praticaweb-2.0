<?php
require_once "../../login.php";
require_once APPS_DIR."lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
?>
<html>
<head>
<title>Agibilità  - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente il record corrente?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}
</script>
</head>
<body>
<?php
	if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/abitabi.tab",$modo);
		unset($_SESSION["ADD_NEW"]);
		$tabella->set_errors($errors);
		include "./inc/inc.page_header.php";
		$id=$_POST["id"];?>	
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="abitabi" name="abitabi" method="post" action="/praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Certificato di Agibilità </H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
					if ($id && $modo=='edit')
						$tabella->set_dati("id=$id");
					$tabella->edita();
				?>
				<!-- fine contenuto-->
			</td>
		  </tr>

		</TABLE>

		<input name="active_form" type="hidden" value="pe.abitabi.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
	</form>

<?php }else{
		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>	<H2 class="blueBanner">Elenco Agibilità </H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_v("$tabpath/abitabi");
				$tabella->set_titolo("Certificato rilasciato","modifica",array("id"=>""));
				$tabella->set_dati("pratica=$idpratica");
				$tabella->elenco();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
				<?php
				$tabella->set_titolo("Aggiungi un nuovo certificato","nuovo");
				$tabella->get_titolo();
				print("<br>");
				if ($tabella->editable) print($tabella->elenco_stampe());
				?>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>				  
		</TABLE>
<?php }?>

</body>
</html>

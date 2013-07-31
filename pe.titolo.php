<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
//print_array($_REQUEST);
?>
<html>
<head>
<title>Titolo - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?

$tab=$_POST["tabella"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	if ($tab=="titolo"){
		$titolo_form="Titolo rilasciato";
		$file_config="$tabpath/titolo";
	}
	elseif ($tab=="volture"){
		$titolo_form="Voltura";		
		$file_config="$tabpath/voltura";
	}
	
	$tabella=new Tabella_v($file_config,$modo);	
	include "./inc/inc.page_header.php";
	?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" name="" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?=$titolo_form?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("pratica=$idpratica");
				}
				$tabella->edita();?>			  
			</td>
		  </tr>  
		</TABLE>

		<input name="active_form" type="hidden" value="pe.titolo.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="tabella" type="hidden" value="<?=$tab?>">
	</FORM>	
<?}else{
		$tabella=new Tabella_v("$tabpath/titolo");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Rilascio del titolo</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?if($tabella->set_dati("pratica=$idpratica")){
						$tabella->set_titolo("Rilascio Titolo","modifica",array("tabella"=>"titolo"));
						$tabella->elenco();
						echo("<br>");					
						$tabella_voltura=new tabella_v("$tabpath/voltura");
						$tabella_voltura->set_titolo("Voltura","modifica",array("tabella"=>"volture"));
						$tabella_voltura->set_dati("pratica=$idpratica");
						$tabella_voltura->elenco();
						echo("<br>");					
						$tabella_voltura->set_titolo("Inserisci Voltura ","nuovo",array("tabella"=>"volture"));
						$tabella_voltura->get_titolo();
						print("<br>");
						if ($tabella->editable) print($tabella->elenco_stampe("pe.titolo"));

						}				
					else{
						$tabella->set_titolo("Inserisci dati relativi al titolo rilasciato","nuovo",array("tabella"=>"titolo"));
						print $tabella->get_titolo();
						print ("<p><b>Nessun titolo rilasciato</b></p>");
						print("<br>");
						if ($tabella->editable) print($tabella->elenco_stampe("pe.titolo"));
					}
?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>

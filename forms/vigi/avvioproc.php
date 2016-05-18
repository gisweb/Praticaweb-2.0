<?php
//Nota conservo il tipo per poter verificere se Ãš cambiato
require_once "../../login.php";
$tabpath="vigi";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=isset($_REQUEST["pratica"])?($_REQUEST["pratica"]):('');

$pr=new pratica($idpratica,3);
$pr->createStructure();

if ($_REQUEST['tipologia_pratica']=='esposto' or $pr->info['tipologia']=='esposto'){
	$file_config="$tabpath/avvio_procedimento_esposto";
	$intestazione='Esposto di infrazione edilizia';
}
else{
	$file_config="$tabpath/avvio_procedimento";
	$intestazione='Avvio del procedimento e comunicazione responsabile della procedura di infrazione edilizia';
}

?>

<html>
<head>
    <title>Avvio Procedimento - <?=$_SESSION["TITOLO_".$idpratica]?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
	utils::loadJS(Array("select2.min","select2_locale_it","form/vigi.avvioproc"));
	utils::loadCss(Array("select2"));
?>
</head>
<body>
<?php
 if (($modo=="edit") or ($modo=="new")) {

	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
	

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="" name="avvioproc" method="post" action="/praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
				<?php
				if(isset($Errors) && $Errors){
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
<input name="active_form" type="hidden" value="vigi.avvioproc.php">						
<input name="via" type="hidden" value="<?=$_POST["via"]?>">
<input name="civico" type="hidden" value="<?=$_POST["civico"]?>">
<input name="ctsezione" type="hidden" value="<?=$_POST["ctsezione"]?>">
<input name="ctfoglio" type="hidden" value="<?=$_POST["ctfoglio"]?>">
<input name="ctmappale" type="hidden" value="<?=$_POST["ctmappale"]?>">
<input name="oldtipo" type="hidden" value="<?=$tabella->get_campo("tipo")?>">
<input name="mode" type="hidden" value="<?=$modo?>">
<input name="vigi" type="hidden" value="1">

</FORM>
<?php
}else{
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

		<H2 class="blueBanner">Avvio del procedimento e comunicazione responsabile</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
	<?php
                $pr=new pratica($idpratica,3);
                $tabella=new tabella_v($file_config,"view");
                $tabella->set_titolo("Dati della pratica","modifica");
                $nrec=$tabella->set_dati("pratica=$idpratica");
                $tabella->elenco();
                $tabella->close_db();
         ?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?php
}
?>
</body>
</html>

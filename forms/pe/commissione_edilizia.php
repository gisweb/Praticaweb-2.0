<?php
require_once "../../login.php";

$tabpath="pe";
$config_file="ce";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$filetab="$tabpath/$config_file";
$id=$_REQUEST["id"];
$pageTitle="Commissione Edilizia - ".$_SESSION["TITOLO_".$idpratica];
?>
<html>
<head>
<title><?php echo $pageTitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>

<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente il parere corrente?';
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


$form="pareri";
if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);

		//aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
		$tabella=new tabella_v($filetab,$modo);?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM height=0 method="post" action="/praticaweb.php">
				<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
						<TR> <!-- intestazione-->
								<TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
						</TR> 
						<TR>
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
                $tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
		<input name="active_form" type="hidden" value="pe.pareri.php">
		<input name="mode" type="hidden" value="<?=$modo?>">

		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica and ente IN (SELECT DISTINCT id FROM pe.e_enti WHERE codice='ce')");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco Pareri Commissione Locale del Paesaggio</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
                                $tabella->set_titolo("nome_ente","modifica",array("nome_ente"=>"","id"=>""));
				for($i=0;$i<$numrec;$i++){
					$tabella->curr_record=$i;
					$tabella->idtabella=$tabella->array_dati[$i]['id'];
					$tabella->get_titolo();
					$tabella->tabella();
						
				}
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
	<?php
                $tabella->set_titolo("Aggiungi un nuovo Parere della Commisione Locale del Paesaggio","nuovo");
                $tabella->get_titolo();
                print "<BR>";
				if ($tabella->editable) print($tabella->elenco_stampe());
        ?>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?php
}
?>

</body>
</html>

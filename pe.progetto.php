<?
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once ("login.php");
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
$tabpath="pe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];

if ($_POST["azione"]){
	$idrow=$_POST["idriga"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		//include_once "./db/db.savedata.php";
		$_SESSION["ADD_NEW"]=$_POST;	
	}
}

/*

if ($_POST["edita_ubicazione"])  include "./db/ubicazione.db.php";
unset($_SESSION["ADD_NEW"]);*/

?>
<html>
<head>
<title>Parametri progetto - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language=javascript src="src/window.js" type=text/javascript>
</SCRIPT>
<script language=javascript>
function confirmSubmit()
{
	document.getElementById("azione").value="Salva";
	return true ;
}

function elimina(id){
	var agree=confirm('Sicuro di voler eliminare definitivamente la riga selezionata?');
	if (agree){
		$("#btn_azione").val("Elimina");
		$("#id").val(id);
		$('#progetto').submit();
	}
}

</script>
</head>

<body  background="">
<?php
	if (in_array($modo,Array('edit','new'))){
		include "./inc/inc.page_header.php";	
		unset($_SESSION["ADD_NEW"]);
		if ($_POST["parametri"]) {
			$tabellah=($modo=='edit')?(new tabella_h("$tabpath/parametri",$modo)):(new tabella_v("$tabpath/parametri",$modo));

?>
	<form method=post id="progetto" name="progetto" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Elenco Parametri</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				
				
<?php
		if($modo=='edit'){
			$numrows=$tabellah->set_dati("pratica=$idpratica");
			$tabellah->elenco();
		}
		else{
			$tabellah->edita();
		}
?>
				
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>

		<input name="pratica" type="hidden" value="<?=$idpratica?>">
		<input name="active_form" type="hidden" value="pe.progetto.php">
		<INPUT type="hidden" name="config_file" value="pe/parametri.tab">
		<input type="hidden" name="parametri" value="1">
		<input type="hidden" name="id" id="id" value="">
		<input type="hidden" name="mode" value="<?php echo $modo?>">		
	</form>	
		<?
		}
		elseif ($_POST["progetto"]) {
				$tabella=new tabella_v("$tabpath/progetto",$modo);
		?>

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" name="" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class=blueBanner>Modifica Dati di Progetto</H2>
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
				<!-- fine contenuto-->
				</td>
		  </tr>
		</TABLE>
		<input name="active_form" type="hidden" value="pe.progetto.php">
		<input name="pratica" type="hidden" value="<?echo($idpratica);?>">				
		<input type="hidden" name="mode" value="<?=$modo?>"></td>
	</FORM>		

<?
		
		
		}
		
	include "./inc/inc.window.php"; // contiene la gesione della finestra popup	
}else{

// modalità  vedi
?>

<script language=javascript>
function link(id){
	window.open('norme.htm','Norme',
'left=20,top=20,width=500,height=500,scrollbars=1,toolbar=1,resizable=1');
}
</script>

		<H2 class="blueBanner">Dati di progetto</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <tr> 
			<td> 
			<?$tabella_progetto=new tabella_v("$tabpath/progetto");
			$nrec=$tabella_progetto->set_dati("pratica = $idpratica");	
			if($nrec){
				$tabella_progetto->set_titolo("Progetto","modifica",array("progetto"=>1));	
				$tabella_progetto->elenco();
				print("<br>");
				$tabella_parametri=new tabella_h("$tabpath/parametri");
				$numrows=$tabella_parametri->set_dati("pratica=$idpratica;");					
				$tabella_parametri->set_titolo("Parametri di progetto","modifica",array("parametri"=>1));
				$tabella_parametri->get_titolo();
				if ($numrows){				
					$tabella_parametri->elenco();
				}
				else
					print ("<p><b>Nessun parametro impostato</b></p>");
					
				$tabella_parametri->set_titolo("Nuovo Parametro di progetto","nuovo",array("parametri"=>1));
				$tabella_parametri->get_titolo();
			}				
			else{
					$tabella_progetto->set_titolo("Aggiungi nuovo Progetto","nuovo",array("progetto"=>1));
					print $tabella_progetto->get_titolo();
					print ("<p><b>Nessun progetto</b></p>");
				}		
			?>
			</td>
		  </tr>
		</table>
<?}?>

</body>
</html>

<?php
require_once "../../login.php";

$tabpath="cdu";

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$today=date('j-m-y'); 


if ($_POST["azione"]){
	//$id=$_POST["idrow"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		include "db/db.cdu.iter.php";
	}
	$_SESSION["ADD_NEW"]=$_POST;				
}

$titolo="Iter della pratica";

?>

<html>
<head>
<title>Iter - <?php echo $titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadJS();
    utils::loadCss(Array('iter'));
?>
<script language="javascript">
function confirmSubmit()
{
	document.getElementById("azione").value="Salva";
	return true ;
}
function elimina(id){
	var agree=confirm('Sicuro di voler eliminare definitivamente la riga selezionata?');
	if (agree){
		$("#btn_azione").val("Elimina");
		$("#idriga").val(id);
		$('#iter').submit();
	}
}
</script>
</head>
<body>
<?php if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		$tabella=new tabella_h("$tabpath/iter",'edit');
		include "./inc/inc.page_header.php";?>
		
	<form method="post" name="iter" id="iter" action="cdu.iter.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">			
                    <tr> 
			<td> 
				<!-- contenuto-->
				<?php
				$numrows=$tabella->set_dati("pratica=$idpratica");
				if ($numrows){
					$tabella->set_titolo("Eventi");
					$tabella->get_titolo(); 
					$tabella->elenco();
					
				}
				else{
					print '<H2 class="blueBanner">Iter della pratica</H2>';
					$tabella->set_titolo("Eventi");
					$tabella->print_titolo(); 
					print ("<p><b>Nessun evento</b></p>");
					print $tabella->set_buttons();
				}
				
				
				?>	
				<input type="hidden"  id="idriga" name="idriga" value="0">
				<input type="hidden" name="mode" value="new">
				<INPUT type="hidden" name="pratica" value="<?php echo $idpratica?>">
				<INPUT type="hidden" name="chk" value="">
				<INPUT type="hidden" name="cdu" value="1">
				<INPUT type="hidden" name="config_file" value="<?php echo $tabpath?>/iter.tab">
				<br><br><br>
				<!-- fine contenuto-->			
			</td>
		  </tr>
		  
		   
		</TABLE>
	</form>		
	<?php
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		$tabella=new tabella_h("$tabpath/iter_pratica");
		
		$nrec=$tabella->set_dati("pratica = $idpratica");	?>			
		<H2 class="blueBanner">Iter della pratica</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
					$tabella->set_titolo($titolo);
					$tabella->get_titolo();
					if ($nrec)	
						$tabella->elenco();
					else
						print ("<p><b>Nessun evento</b></p>");			
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
	
<?php }?>		

</body>
</html>

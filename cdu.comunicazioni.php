<?php
include_once("login.php");
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

$tabpath="cdu";
$config_file="$tabpath/comunicazioni";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
?>
<html>
<head>
<title>Provvedimenti - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>

</head>
<body>
<?php
if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		$id = $_POST["id"];
		$tabella=new tabella_v("$tabpath/comunicazioni",$modo);
		include "./inc/inc.page_header.php";?>
		<form method="post" name="comunicazioni" action="cdu.comunicazioni.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">					  
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
                  $numrows=$tabella->set_dati("id=$id AND pratica=$idpratica");
				  if ($numrows)  print $tabella->edita();
				?>	
				<input type="hidden" name="mode" value="<?php echo $modo;?>">
				<INPUT type="hidden" name="pratica" value="<?php echo $idpratica;?>">
				<INPUT type="hidden" name="id" value="<?php echo $id;?>">
				<input name="active_form" type="hidden" value="cdu.comunicazioni.php">
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
		$tabella=new tabella_h("$tabpath/comunicazioni",$modo);

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
						print ("<p><b>Nessuna Comunicazione</b></p>");			
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
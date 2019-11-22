<?php
include_once("login.php");
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

$tabpath="pe";
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
	utils::loadJS(Array("select2.min","select2_locale_it",'form/pe.comunicazioni'));
	utils::loadCss(Array("select2"));
?>

</head>
<body>
<?php
if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		unset($_SESSION["ADD_NEW"]);
		$id = $_POST["id"];
		$tabella=new tabella_v("$tabpath/comunicazioni",$modo);
		include "./inc/inc.page_header.php";?>
		<form method="post" name="comunicazioni" action="praticaweb.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">					  
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
                  	if($Errors){
						$tabella->set_errors($Errors);
						$tabella->set_dati($_POST);
					}
					else{
						$numrows=$tabella->set_dati("id=$id AND pratica=$idpratica");
					}
				  
                  print $tabella->edita();
				?>	
				<input type="hidden" name="mode" value="<?php echo $modo;?>">
				<INPUT type="hidden" name="pratica" value="<?php echo $idpratica;?>">
				<INPUT type="hidden" name="id" value="<?php echo $id;?>">
				<input name="active_form" type="hidden" value="pe.comunicazioni.php">
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
		$tabella=new tabella_h("$tabpath/comunicazioni","list");
		$titolo = "Comunicazioni Inviate/Ricevute";
        $nrec=$tabella->set_dati("pratica = $idpratica");
        $dataFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."pe.comunicazioni.php";
        if (file_exists($dataFile)){
            $arrayData = $tabella->array_dati;
            require_once $dataFile;
            $tabella->array_dati = $arrayData;
        }
        
?>			
<!--<h2 style="color:red">Attenzione il servizio di protocollazione e invio Mail &egrave; momentaneamente sospeso.</h2>-->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
					$tabella->set_titolo($titolo,"nuovo");
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

<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$filetab="$tabpath/conformita_puc";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$titolo = "Verifica Finale Conformità";
?>
<html>
<head>
<title><?php echo "$titolo - " .$_SESSION["TITOLO_".$idpratica];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>

</head>
<body  background="">
<?php


$form="pareri";
if (($modo=="edit")){
		include "./inc/inc.page_header.php";
            $tabella=new tabella_v($filetab,$modo);

			$id=$_POST["id"];
			$filtro="id=$id";
            $tabella->set_dati($filtro);
                        



		?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM height=0 method="post" action="praticaweb.php">
				<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
						<TR> <!-- intestazione-->
								<TD><H2 class="blueBanner"><?php echo $titolo;?></H2></TD>
						</TR> 
						<TR>
								<td>
						<!-- contenuto-->
		<?php

        
		$tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
		<input name="active_form" type="hidden" value="pe.conformita_puc.php">
		<input name="mode" type="hidden" value="<?=$modo?>">

		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);

		$numrec=$tabella->set_dati("pratica=$idpratica AND tipo = (SELECT id FROM pe.e_conformita WHERE codice = 'ptcp')");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
			<?php
                $tabella->set_titolo($titolo,"modifica",array("id"=>""));
				
					$tabella->get_titolo();
					$tabella->tabella();

		print "</td></tr><tr><td>";
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }
?>

</body>
</html>

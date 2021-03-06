<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Lavori- <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>
</head>
<body  background="">

<?php

$tab=$_POST["tabella"];
$id=$_POST["id"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	if ($tab=="proroga"){
		$titolo_form="Proroga";
		$file_config="$tabpath/proroga";
	}
        else{
		$titolo_form="Scadenze Lavori";
		$file_config="$tabpath/lavori";
	}
	
	
	$tabella=new Tabella_v($file_config,$modo);	
	include "./inc/inc.page_header.php";?>
	
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
				<?php
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				if (file_exists(LOCAL_INCLUDE."pe.proroga.edit.before.php") && $tab=="proroga"){
					$html="";
					include_once LOCAL_INCLUDE."pe.proroga.edit.before.php";
					print $html;
				}
				$tabella->edita();
                ?>			  
			</td>
		  </tr>
		</TABLE>

		<input name="active_form" type="hidden" value="pe.lavori.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="tabella" type="hidden" value="<?=$tab?>">
				
	</FORM>	
<?php
}
else
{?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Esecuzione Lavori</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				$tabella=new Tabella_v("$tabpath/lavori");
				
				if($tabella->set_dati("pratica=$idpratica")){
                                    $tabella->set_titolo("Scadenze lavori","modifica",array("tabella"=>"lavori","id"=>""));
                                    $tabella->get_titolo();
                                    $tabella->tabella();
                                    echo("<div class='button_line'></div>");								
                                    $tabella_proroga=new tabella_v("$tabpath/proroga");
                                    $tabella_proroga->set_titolo("Proroga","modifica",array("tabella"=>"proroga","id"=>""));
                                    $tabella_proroga->set_dati("pratica=$idpratica");
                                    $tabella_proroga->elenco();
                                    echo("<div class='button_line'></div>");
                                    $tabella_proroga->set_titolo("Inserisci nuova proroga","nuovo",array("tabella"=>"proroga"));
                                    $tabella_proroga->get_titolo();
				}				
				else{
                                    $tabella->set_titolo("Inserisci dati relativi alle scadenze lavori","nuovo",array("tabella"=>"lavori"));
                                    print $tabella->get_titolo();
                                    print ("<p><b>Scadenze lavori non impostate</b></p>");
				}
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

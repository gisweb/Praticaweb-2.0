<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
require_once "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
if (file_exists(DATA_DIR."praticaweb/include/init.pe.titolo.php")){
    $oggetto="";
    require_once DATA_DIR."praticaweb/include/init.pe.titolo.php";
}

?>
<html>
<head>
<title>Titolo - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss(Array('dropzone','iter'));
    utils::loadJS(Array('form/pe.titolo','dropzone'));
?>
</head>
<body  background="">

<?php

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
        <input type="hidden" id="hidden-oggetto" value="<?php echo $oggetto;?>">
	<FORM id="pe.titolo" name="" method="post" action="praticaweb.php">
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
                    $tabella->set_dati("pratica=$idpratica");
            }
			if (file_exists(LOCAL_INCLUDE."pe.titolo.edit.before.php")){
				$html="";
				include_once LOCAL_INCLUDE."pe.titolo.edit.before.php";
				print $html;
			}
            $tabella->edita();?>			  
			</td>
		  </tr>  
		</TABLE>

		<input name="active_form" type="hidden" value="pe.titolo.php">
		<input id="mode" name="mode" type="hidden" value="<?=$modo?>">
		<input name="tabella" type="hidden" value="<?=$tab?>">
	</FORM>	
<?php

            }else{
		$tabella=new Tabella_v("$tabpath/titolo");?>

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Rilascio del titolo</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
                    if($tabella->set_dati("pratica=$idpratica")){
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

<?php  
    $dropzoneFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."dropzone.titolo.php";
    if (file_exists($dropzoneFile)){
        require_once $dropzoneFile;
    }
}
?>
</body>
</html>

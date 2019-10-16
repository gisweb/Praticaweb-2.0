<?php
include_once("login.php");
//error_reporting(E_ALL);
$tabpath="pe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
$config_file="$tabpath/esposti";
require_once LIB."tabella_v.class.php";
require_once LIB."tabella_h.class.php";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Esposti</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<SCRIPT language="javascript">
function aggiungi_riferimento(id,pratica){
	parent.window.document.location="vigi.esposti.php?mode=new&rif="+id+"&pratica="+pratica;
}
</SCRIPT>
</head>
<body  background="">
<?php	
	if (($modo=="edit") or ($modo=="new")){
		if ($modo=="new"){
			unset($_SESSION["ADD_NEW"]);
			//$config_file.="_new.tab";
		}

		$tabella=new tabella_v($config_file,$modo);	
		include "./inc/inc.page_header.php";
		$id=$_POST["id"];?>
	<FORM id="" name="" method="post" action="praticaweb.php">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
			<tr> 
				<td> 
			<!-- intestazione-->
					<H2 class="blueBanner"><?echo(($modo=="edit")?("Modifica esposto"):("Inserisci nuovo esposto"))?></H2>
			<!-- fine intestazione-->
				</td>
			</tr>
			<tr> 
				<td> 
				<!-- contenuto-->
				<?php
                                print_array($Errors);
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
				</td>
			</tr>
		</TABLE>

		<input name="active_form" type="hidden" value="pe.esposti.php">				
		<input name="mode" type="hidden" value="<?=$modo;?>">
				
	</FORM>			
<?php
	include "./inc/inc.window.php";
}else{
		
		$tabella=new tabella_v($config_file);
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Esposti ricevuti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	
            <TR> 
                <TD> 
			<!-- contenuto-->
				<?php
				$nrec = $tabella->set_dati("pratica=$idpratica");
				$tabella->set_titolo("Esposto","modifica",array("id"=>$id));
				$tabella->elenco();?>
			<!-- fine contenuto-->
                </TD>
            </TR>
		  
            <TR> 
                <TD> 
			<!-- tabella nuovo inserimento-->
				<?php
                $tabella->set_titolo("Aggiungi nuovo Esposto ---> clicca su Nuovo","nuovo");
				$tabella->get_titolo();
                ?><BR>
			<!-- fine tabella nuovo inserimento-->
                </TD>
            </TR>	
            <TR>
                <TD>
                    <?php
                    if ($nrec==0)  print ("<p><b>Nessun Esposto presentato</b></p>");


                    print "<br><div class=\"button_line\"></div>\n";
                    $tabellaStampe=new tabella_h('stp/documenti','list');
                    $nrecStampe=$tabellaStampe->set_dati("pratica=$idpratica and form='pe.esposti'");
                    $tabellaStampe->set_titolo("Documentazione relativa agli esposti","nuovo",Array("form"=>"pe.esposti"));
                    $tabellaStampe->get_titolo("stp.documenti.php");
                    if ($tabellaStampe->num_record)
                        $tabellaStampe->elenco();
                    else
                        print ("<p><b>Nessuna Documento</b></p>");
                    print "<BR>";
                    ?>
                </TD>
            </TR>
        
		</TABLE>
<?php
}
?>

</body>
</html>

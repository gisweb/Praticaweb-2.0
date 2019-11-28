<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
$idpratica=(isset($_REQUEST["pratica"]))?($_REQUEST["pratica"]):('');
$tabpath="pe";
$form="pe.importi_dovuti.php";
$file_config=$filetab="pe/importi_dovuti.tab";
switch ($modo) {
	case "new" :
		$tit="Inserimento nuova richiesta pagamento";
		break;
	case "edit" :
		$tit="Modifica richiesta Pagamento";
		break;

}

//Imposto i permessi di default per il modulo
$_SESSION["PERMESSI"]=$_SESSION["PERMESSI_OK"];


?>
<html>
<head>
    <title>Elenco delle richieste pagamenti della pratica</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php
	utils::loadJS();
	utils::loadCss();
?>
    <SCRIPT language="javascript" type="text/javascript">

        function confirmSubmit(){
            return confirm('Sei sicuro di voler eliminare questa richiesta di pagamento?');
        }
    </SCRIPT>

    </head>
<body>
<?php

if (($modo=="edit") or ($modo=="new")){
        include "./inc/inc.page_header.php";
        unset($_SESSION["ADD_NEW"]);
        if ($modo=="edit"){
            $filtro="id=$id";
            $titolo="Modifica richiesta Pagamento";
            $message =<<<EOT
<p><b style="color:red;font-size:12px;">Attenzione il salvataggio/eliminazione di questo record modificher&agrave; il documento di stampa già creato per questa scadenza</b></p>
EOT;
        }
        else{
            $message =<<<EOT
<p><b style="color:red;font-size:12px;">Attenzione il salvataggio di questo record creer&agrave; un nuovo documento di stampa per questa scadenza</b></p>
EOT;
            $titolo="Inserisci una nuova richiesta di pagamento";
        }

		//aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
		$tabella=new tabella_v($filetab,$modo);?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0 method="post" action="praticaweb.php">
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
            <TR> <!-- intestazione-->
                <TD colspan="2"><H2 class="blueBanner"><?=$titolo?></H2></TD>
            </TR> 
            <TR>
                <TD><?php echo $message;?></TD>
            </TR>
            <TR>
                <TD>
						<!-- contenuto-->
		<?php
		if($Errors){
                    $tabella->set_errors($Errors);
                    $tabella->set_dati($_POST);
                    $titolo="";
                }
                elseif ($modo=="edit"){	
                   $tabella->set_dati($filtro);
                   $titolo="";
                }
                $tabella->edita();
		?>
		<!-- fine contenuto-->
                </TD>
            </TR>
        </TABLE>
		<input name="active_form" type="hidden" value="<?php echo $form;?>">
		<input name="mode" type="hidden" value="<?=$modo?>">



		</FORM>	
		

	<?php
        include "./inc/inc.window.php";
		
	}?>

</body>
</html>

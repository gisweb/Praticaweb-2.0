<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
$idpratica=(isset($_REQUEST["pratica"]))?($_REQUEST["pratica"]):('');
$tabpath="pe";
$form="pe.pagamenti.php";
$file_config=$filetab="pe/pagamenti.tab";
switch ($modo) {
	case "new" :
		$tit="Inserimento nuovo pagamento";
		break;
	case "edit" :
		$tit="Modifica Pagamento";
		break;
	case "view" :
		$tit="Dettagli sulPagamento";
		break;
	default :
		$tit="Elenco dei Pagamenti";
		break;
}
?>
<html>
<head>
    <title>Elenco dei pagamenti della pratica</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php
	utils::loadJS();
	utils::loadCss();
?>
    <SCRIPT language="javascript" type="text/javascript">

        function confirmSubmit(){
            return confirm('Sei sicuro di voler eliminare questo pagamento?');
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
            $titolo="";
        }
        else{
            $titolo="Inserisci nuovo pagamento";
        }

		//aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
		$tabella=new tabella_v($filetab,$modo);?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0 method="post" action="praticaweb.php">
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
            <TR> <!-- intestazione-->
                <TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
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
		
	}elseif($modo=="view"){
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica;");
		
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco Sanzioni</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
		<?php
                    $tabella->set_titolo("Modica ","modifica",array("id"=>""));
                    for($i=0;$i<$numrec;$i++){
                            $tabella->curr_record=$i;
                            $tabella->idtabella=$tabella->array_dati[$i]['id'];
                            $tabella->get_titolo();
                            $tabella->tabella();	
                    }
		print "</td></tr><tr><td>";
                
                $tabella->set_titolo("Aggiungi un nuovo pagamento","nuovo");
                $tabella->get_titolo();
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }

		else {
	$tabella=new Tabella_h("$file_config",'list');
	$tabella->set_titolo($tit,"nuovo");
	$tabella->set_dati("pratica=$idpratica;");
	
	?>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<TR> 
			<TD> 
				
				<?php
                $tabella->get_titolo();
				if($tabella->num_record > 0) {
					$tabella->elenco();
				}
				else{
					echo "<p><b>Nennun pagamento inserito</b></p>";
				}
				?>
			</TD>
		</TR>
	</TABLE>
  
	<?php
	}?>

</body>
</html>

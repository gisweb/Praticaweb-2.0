<?php
require_once "../../login.php";

$tabpath="storage";
$idpratica=$_REQUEST["pratica"];
$user=$_SESSION["USER_ID"];
$data=date("d/m/Y");
$filetab="$tabpath/invio";

?>
<html>
<head>
<title>Documentazione Inviata</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>

</head>
<body  background="">
<?php

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$form="invio";
$titolo = "Informazioni di Invio";
if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    $tabella=new tabella_v($filetab,$modo);
    if($modo=="edit"){
        $id=$_POST["id"];
        $filtro="id=$id";
        $tabella->set_dati($filtro);
    }
    else{

        $tabella->set_dati($_REQUEST);
    }

    //aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
    
                ?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM height=0 method="post" action="/praticaweb.php">
				<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
						<TR> <!-- intestazione-->
								<TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
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
			<input name="active_form" type="hidden" value="storage.invio.php">
            <input name="storage" type="hidden" value="1">
			<input name="mode" type="hidden" value="<?=$modo?>">	
		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Informazioni di invio</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				
                $tabella->set_titolo($titolo,"modifica",array("id"=>""));
                $tabella->get_titolo();
                $tabella->tabella();

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

<?php
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once ("login.php");
$tabpath="cdu";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$pr=new pratica($idpratica,1);
$pr->createStructure();
if ($_POST["azione"]){
       if ($_POST["azione"]=="Aggiungi" && !$_POST["foglio"]){
          $array_dati["errors"]["foglio"]="Campo obbligatorio";
       }
       if ($_POST["azione"]=="Aggiungi" && !$_POST["mappale"]){
          $array_dati["errors"]["mappale"]="Campo obbligatorio";
       }
   
	$idrow=$_POST["idriga"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5

	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		include_once "./db/db.cdu.vincoli.php";
	}
	$_SESSION["ADD_NEW"]=$_POST;				
}

?>

<html>
<head>
<title>CDU - <?php echo $titolo;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php

    utils::loadJS(Array('init'));
    utils::loadCss();
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
		
		mappali.azione.value="Elimina";
		mappali.id.value=id;
		document.mappali.submit();
	}
}

function link(id){
	window.location='pe.nomappa.php?mapkey='+id;
}

  function openNew(startParameters){
  	var winWidth = window.screen.availWidth-8;
	var winHeight =window.screen.availHeight-55;
	var mywin=window.open("mappe/map.phtml?" + startParameters, "pmapper", "width=" + winWidth + ",height=" + winHeight + ",menubar=no,toolbar=no,scrollbar=auto,location=no,resizable=yes,top=0,left=0,status=yes");
	mywin.focus();
  }

  function openMap(mapsetid,template,parameters){
		  	var winWidth = window.screen.availWidth-8;
				var winHeight = window.screen.availHeight-55;
				var winName = 'mapset_'+mapsetid;
				if(!parameters) parameters='';
				var mywin=window.open("/gisclient/" + template + ".html?mapset=" + mapsetid + parameters, winName,"width=" + winWidth + ",height=" + winHeight + ",menubar=no,toolbar=no,scrollbar=auto,location=no,resizable=yes,top=0,left=0,status=yes");
				mywin.focus();
  }

  function OpenMapset(mapsetid,template,parameters){
		if(!template) template = this.Template;
		var winWidth = window.screen.availWidth-8;
		var winHeight = window.screen.availHeight-55;
		var winName = 'mapset_'+mapsetid;
		template="template/" + template;
		if(!parameters) parameters='';
		if(template.indexOf('?')>0)
			template=template + '&';
		else
			template=template + '?';
		var mywin=window.open('/gisclient/' + template + "mapset=" + mapsetid + "&" + parameters, winName,"width=" + winWidth + ",height=" + winHeight + ",menubar=no,toolbar=no,scrollbar=auto,location=no,resizable=yes,top=0,left=0,status=yes");
		mywin.focus();
  }

</script>
</head>
<body>
<?php if (($modo=="edit") or ($modo=="new")){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA MAPPALI ASSERVITI >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
	if($_POST["mappali"]){
		$comune = appUtils::getComune($idpratica,'cdu');
	//print_r($_POST);
		$tabellav=new tabella_v("$tabpath/mappali",'new');
		$tabellah=new tabella_h("$tabpath/mappali",'edit');
		$tabellav->set_errors($errors);
		include "./inc/inc.page_header.php";	?>
        <form method=post name="mappali" action="cdu.richiesta.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Modifica elenco mappali</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<input name="cod_belfiore" type="hidden" value="<?php echo $comune;?>">
				<input type="hidden" name="idriga" id="idriga" value="0">
				<input name="active_form" type="hidden" value="cdu.richiesta.php">
				<input type="hidden" name="mode" value="new">
				<input type="hidden" name="mappali" value="1">
				<input name="cdu" type="hidden" value="1">
				<?php
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				  $tabellav->edita();
				  $numrows=$tabellah->set_dati("pratica=$idpratica");
				  if ($numrows)  $tabellah->elenco();?>
				
				<!-- fine contenuto-->			

			</td>
		  </tr>
 
		</TABLE>
		</form>
	<?php 
        }
        else{
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ASSERVIMENTO >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>	
		unset($_SESSION["ADD_NEW"]);
		$tabella_richiesta=new tabella_v("$tabpath/richiesta",$modo);
		include "./inc/inc.page_header.php";	
        ?>
	<FORM method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="95%" >		
		 	
		 <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class=blueBanner>Modifica Richiesta</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<?php
                            if($Errors){
                                $tabella_richiesta->set_errors($Errors);
                                $tabella_richiesta->set_dati($_POST);
                            }
                            elseif ($modo=="edit"){	
                                $tabella_richiesta->set_dati("pratica=$idpratica");
                            }
                            $tabella_richiesta->edita();
                        ?>	
				<!-- fine contenuto-->
			</td>
		  </tr>
 
		</TABLE>
				<input name="active_form" type="hidden" value="cdu.richiesta.php">
				<input name="cdu" type="hidden" value="1">
				<input name="mode" type="hidden" value="<?php echo $modo;?>">
				<input name="pratica" type="hidden" value="<?php echo $idpratica;?>">
	</FORM>		

	
	<?php
	}
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA DATI RICHIESTA E MAPPALI >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		
        $pr=new pratica($idpratica,1);
        $pr->createStructure();
        $tabella_richiesta=new tabella_v("$tabpath/richiesta",'view');
	$nrec=$tabella_richiesta->set_dati("pratica = $idpratica");
        ?>
			
		<!-- <<<<<<<<<<<<<<<<<<<<<   RICHIESTA  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Richiesta</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php if ($nrec){
					$tabella_richiesta->set_titolo("Richiesta cdu","modifica");
					$tabella_mappali=new tabella_h("$tabpath/mappali",'view');
					$tabella_mappali->set_titolo("Elenco dei mappali","modifica",array("mappali"=>1));
					$nrow=$tabella_mappali->set_dati("pratica=$idpratica");
					$tabella_richiesta->get_titolo();
					$tabella_richiesta->tabella();
					$tabella_mappali->get_titolo();
					if($nrow)
						$tabella_mappali->elenco();
					else
						print ("<p><b>Nessun mappale</b></p>");
						
				}
				else{
					$tabella_richiesta->set_titolo("Nuova richiesta","nuovo");
					$tabella_richiesta->get_titolo();
					print ("<p><b>Nessuna richiesta</b></p>");
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

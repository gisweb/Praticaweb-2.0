<?php
/*se il record corrente da editare Ãš stato generato con il calcolo automatico rendo editabile solo i valori di scomputo e nascondo il pulsante 
Elimina in quanto l'eliminazione degli importi calcolati va fatta dai singoli calcoli 
se invece il record Ãš stato inserito manualmente posso editare tutto ed eliminare il record*/

include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
$tab=(isset($_POST["tabella"]))?($_POST["tabella"]):(null);
$titpag=$_SESSION["TITOLO_$idpratica"];
$Errors=$array_dati["errors"];
$tabpath="oneri";
 
if($tab=='oneri'|| !$tab){
	$titolo="Costo di Costruzione e Oneri di Urbanizzazione";
	$file_conf="$tabpath/totali";
}
elseif ($tab=="monetizzazione"){	
	$titolo="Monetizzazione aree verdi e parcheggi";
	$file_conf="$tabpath/monetizzazione.tab";
       
}

//Imposto i permessi di default per il modulo
$_SESSION["PERMESSI"]= min($_SESSION["PERMESSI_$idpratica"],$_SESSION["PERMESSI_A_$idpratica"]);


?>
<html>
<head>
<title>Oneri - <?=$titolo." - ".$titpag?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array('form/oneri.monetizzazione'));
	utils::loadCss();
?>
<SCRIPT language=javascript>
    
function link(id){
	window.location="oneri.calcolati.php?pratica=<?=$idpratica?>";
}
</SCRIPT>
</HEAD>
<body  background="">

<?php
				if (($modo=="edit") or ($modo=="new")) {
				$tabella=new tabella_v($file_conf,$modo);
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";	?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<FORM id="" name="" method="post" action="praticaweb.php">
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr>
			<!-- intestazione-->
			<td> <H2 class="blueBanner"><?=$titolo?></H2></td>
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
				$tabella->edita();
			?>	
			</td>
		</tr>
	</TABLE>

	<input name="active_form" type="hidden" value="oneri.importi.php">
	<input name="mode" type="hidden" value="<?=$modo?>">
	<input name="tabella" type="hidden" value="<?=$tab?>">
</FORM>	
<?php
}else{
//           <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>


	<h2 class="blueBanner">Tabella degli oneri</h2>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
        <tr>
            <td colspan="2">
                <?php
    $calcolo=1; //se non ci sono record visualizzo comunque il pulsante per il calcolo automatico
    $tabella_monetizz=new Tabella_v('oneri/monetizzazione','view');
    $tabellav=new Tabella_v("$tabpath/totali",'view');//tabella verticale con totali ed estremi di pagamento
    //$tabellah=new Tabella_h("$tabpath/importi",'view');
    $numrows=$tabellav->set_dati("pratica=$idpratica");//vedo se c'è un record nella tabella dei totali
    $tabella=new Tabella_v("$tabpath/totali",'view');//tabella verticale con totali ed estremi di pagamento
    if ($numrows){
        $calcolo=$tabellav->get_campo("calcolo");//prendo il campo calcolo per capire se il dato è stato inserito con calcolo automatico o a mano
        //$tabellah->set_dati("pratica=$idpratica");
        $tabella->set_titolo("Costo di Costruzione e Oneri di Urbanizzazione","modifica",array("calcolo"=>$calcolo,"tabella"=>"oneri"));			 
        $tabella->get_titolo();
    }
    else{// se non c'e un calcolo fatto propongo il menu nuovo 
        $tabella->set_titolo("Costo di Costruzione e Oneri di Urbanizzazione","nuovo",array("tabella"=>"oneri"));			 
        $tabella->get_titolo();
    }
    
    
    
                ?>
            </td>
        </tr>
		<tr> 
			

<!--  tabella oneri e costo-->
<?php //se non ci sono record visualizzo comunque il pulsante per il calcolo automatico
	//$tabella_oneri=new Tabella_v('oneri/totali','view');//tabella verticale con totali ed estremi di pagamento
    //$tabella_on=new Tabella_h('oneri/importi','view');//tabella verticale con totali ed estremi di pagamento
	
	/*
	$numrows=$tabella_oneri->set_dati("pratica=$idpratica");//vedo se c'è un record nella tabella dei totali
	if ($numrows){
		$tabella_oneri->set_titolo("Costo di Costruzione e Oneri di Urbanizzazione","modifica",array("tabella"=>"concessori"));
		$tabella_oneri->get_titolo();
		$tabella_oneri->tabella();
	}
	else{
		$tabella_oneri->set_titolo("Costo di Costruzione e Oneri di Urbanizzazione","nuovo",array("tabella"=>"concessori"));
		$tabella_oneri->get_titolo();
		print "<p><b>Nessun dato inserito</b></p>";
        print "<hr>";
        if ($tabella_oneri->editable) echo "
        <form method=\"post\" target=\"_parent\" action=\"oneri.calcolati.php\">	
            <input type=\"hidden\" name=\"mode\" value=\"new\">	
			<input type=\"hidden\" name=\"pratica\" value=\"$idpratica\">	
			<INPUT class=\"printhide\" name=\"modifica\"  TYPE=\"image\" SRC=\"images/calcolapicc.gif\" >				
		</form>	";
	}*/
    
    if ($numrows){
        print("<td colspan=\"2\" valign=\"top\">");
        $tabellav->tabella();
        print("</td>");
        //print("<td valign=\"top\">");
        //$tabellah->elenco();
        //print("</td>");
        
    }
    else {
        print "<td align=\"left\" colspan=\"2\"><b>Inserire i dati cliccando su Nuovo o accedere al calcolo automatico con Nuovo Calcolo</b></td>";
    }
    print "</tr>";
	print "<tr><td colspan=\"2\"><hr>";
    if ($tabellav->editable) echo "
        <form method=\"post\" target=\"_parent\" action=\"oneri.calcolati.php\">	
            <input type=\"hidden\" name=\"mode\" value=\"new\">	
			<input type=\"hidden\" name=\"pratica\" value=\"$idpratica\">	
			<INPUT class=\"printhide\" name=\"modifica\"  TYPE=\"image\" SRC=\"images/calcolapicc.gif\" >				
		</form>	";
    print("</td>");
	    
    print "</tr>";
	$numrows=$tabella_monetizz->set_dati("pratica=$idpratica");//vedo se c'Ãš un record nella tabella dei totali
	if ($numrows){
		$tabella_monetizz->set_titolo("Monetizzazione aree verdi e parcheggi","modifica",array("tabella"=>"monetizzazione"));
		$tabella_monetizz->get_titolo();
		$tabella_monetizz->tabella();
	}
	else{
		$tabella_monetizz->set_titolo("Monetizzazione aree verdi e parcheggi","nuovo",array("tabella"=>"monetizzazione"));
		$tabella_monetizz->get_titolo();
		print "<p><b>Nessun dato inserito</b></p>";
	}
	
	?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
					$tabella_oneri=new Tabella_v('oneri/importi','view');
					if ($tabella_oneri->editable) print($tabella_oneri->elenco_stampe());
				?>
			</td>
		</tr>
	</table>
			
<?php }?>
</body>
</html>

<?php

include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="oneri";

?>
<html>
<head>
<title>Fideiussioni - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script LANGUAGE="JavaScript">
function confirmSubmit(){
var msg='Sicuro di voler eliminare definitivamente il record corrente?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}
</script>

</head>
<body  background="">
<?	
if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/fidi",$modo);
		include "./inc/inc.page_header.php";
		unset($_SESSION["ADD_NEW"]);
		$istituto=$_POST["istituto"];
		$id=$_POST["id"];?>	
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM id="fidi" name="fidi" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?echo(($istituto)?($istituto):("Inserisci Nuova Fideiussione"))?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<?if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();?>				
				<!-- fine contenuto-->
			</td>
		  </tr>
 
		</table>	
        <input name="active_form" type="hidden" value="oneri.fidi.php">
        <input name="mode" type="hidden" value="<?=$modo?>">
</FORM>	
<?}else{
		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>		<H2 class="blueBanner">Elenco Fideiussioni</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
                $tabella=new tabella_v("$tabpath/fidi");
                $tabella_svincoli=new tabella_h("$tabpath/svincoli",'list');
				//$tabella->set_titolo("istituto","modifica",array("istituto"=>"","id"=>""));
				$tabella->set_dati("pratica=$idpratica");
                for($i=0;$i<count($tabella->array_dati);$i++){
                    $tb=new tabella_v("$tabpath/fidi");
                    $tb->set_titolo("istituto ".$tabella->array_dati[$i]["istituto"],"modifica",array("istituto"=>"","id"=>$tabella->array_dati[$i]["id"]));
                    $tb->get_titolo();
                    $tb->set_dati($tabella->array_dati[$i]);
                    $tb->tabella();
                    
                    $tabella_svincoli->set_titolo("Elenco degli svincoli","nuovo",Array("fido"=>$tabella->array_dati[$i]["id"]));
                    $tabella_svincoli->get_titolo("oneri.svincoli_fidi.php");
                    $tabella_svincoli->set_dati("fido=".$tabella->array_dati[$i]["id"]);
                    $tabella_svincoli->elenco();
                }
				//$tabella->elenco();
				echo("<br>");
				$tabella->set_titolo("Aggiungi nuova fideiussione","nuovo");
				$tabella->get_titolo();?>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>				  
		</TABLE>
<?}?>

</body>
</html>

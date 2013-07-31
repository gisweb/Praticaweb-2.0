<?php

include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$idpratica=$_REQUEST["pratica"];
$id=$_REQUEST["id"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="oneri";
$config_file="svincoli";

?>
<html>
<head>
    <title>Svincoli Fideiussione - <?=$titolo?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>


</head>
<body  background="">
<?	

if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/$config_file",$modo);
		include "./inc/inc.page_header.php";
		unset($_SESSION["ADD_NEW"]);
		$id=$_POST["id"];
		$fido=$_REQUEST["fido"];?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM id="fidi" name="svincoli" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?echo ("Svincolo Fideiussione")?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<?
                if($Errors){
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
        <input name="active_form" type="hidden" value="oneri.svincoli_fidi.php">
        <input name="mode" type="hidden" value="<?=$modo?>">
        <input name="fido" type="hidden" value="<?=$fido?>">
</FORM>	
<?}else{
		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>		<H2 class="blueBanner">Svincoli Fidi</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
                $tabella=new tabella_v("$tabpath/$config_file",'list');
				//$tabella->set_titolo("istituto","modifica",array("istituto"=>"","id"=>""));
				$tabella->set_dati("id=$id");
                $tabella->tabella();
                ?>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>				  
		</TABLE>
<?}?>

</body>
</html>

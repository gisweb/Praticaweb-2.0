<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];?>
<html>
<head>
<title>Lavori- <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tab=$_POST["tabella"];
$id=$_POST["id"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	$titolo_form="Sopralluoghi";
	$file_config="$tabpath/sopralluoghi_edit.tab";
	$tabella=new Tabella_v($file_config,$modo);	
	include "./inc/inc.page_header.php";?>
	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" name="" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			<tr> 
				<td><!-- intestazione--><H2 class="blueBanner"><?=$titolo_form?></H2><!-- fine intestazione-->
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
			</td>
		  </tr> 
		</TABLE>

		<input name="active_form" type="hidden" value="pe.sopralluoghi.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="tabella" type="hidden" value="<?=$tab?>"></td>
	</FORM>
<?include "./inc/inc.window.php";

}else{
		$tabella=new Tabella_v("$tabpath/sopralluoghi");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Esecuzione Lavori</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				if($tabella->set_dati("pratica=$idpratica")){
					$tabella->set_titolo("Sopralluoghi","modifica",array("id"=>""));
					$tabella->elenco();
				}
				echo ("<br>");
				$tabella->set_titolo("Inserisci dati di un nuovo sopralluogo","nuovo",array("tabella"=>"sopralluoghi"));
				$tabella->get_titolo();
				//print ("<p><b>Nessun sopralluogo registrato</b></p>");
					
?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>

<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
?>
<html>
<head>
<title>Titolo - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?

$tab=$_POST["tabella"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	if ($tab=="grigliatura"){
		$titolo_form="Dati grigliatura/dissabiatura";		
		$file_config="$tabpath/grigliatura_edit.tab";
	}
	elseif ($tab=="disoleatura"){
		$titolo_form="Dati disoleatura";		
		$file_config="$tabpath/disoleatura_edit.tab";
	}
	elseif ($tab=="denitrificazione"){
		$titolo_form="Dati denitrificazione";		
		$file_config="$tabpath/denitrificazione_edit.tab";
	}
	elseif ($tab=="vasca"){
		$titolo_form="Dati vasca raccolta acque";		
		$file_config="$tabpath/vasca_edit.tab";
	}
	$tabella=new Tabella_v($file_config);	
	include "./inc/inc.page_header.php";
	?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="praticaweb.php">		  
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
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("pratica=$idpratica");
				}
				$tabella->edita();?>			  
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>
				<td>
				<input name="active_form" type="hidden" value="pe.impianti_industriali.php">
				<input name="mode" type="hidden" value="<?=$modo?>">
				<input name="tabella" type="hidden" value="<?=$tab?>"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>
				<?if($modo=="edit"){?>
					<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Elimina" onClick="return confirmSubmit()"></td>
				<?}?>
			</tr>
		</FORM>	
		</table>	
<?}else{?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Dati impianti scarichi idrici industriali</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
			<?
			$array_file_tab=array("grigliatura","disoleatura","denitrificazione","vasca");
		    $array_titolo=array("Grigliatura/Dissabiatura","Disoleatura","Denitrificazione","Vasca raccolta acque");
			for($i=0;$i<4;$i++){
			  $file_tab=$array_file_tab[$i];
			  $titolo=$array_titolo[$i];
			  $tabella=new Tabella_v($tabpath."/".$file_tab."_view.tab"); 
			  if($tabella->set_dati("pratica=$idpratica")){
					$tabella->set_titolo($titolo,"modifica",array("titolo"=>$titolo,"tabella"=>$file_tab));
					$tabella->elenco();
				}				
			   else{
					$tabella->set_titolo("Inserisci dati $titolo","nuovo",array("tabella"=>$file_tab));
					print $tabella->get_titolo();
					print ("<p><b>Nessun dato</b></p>");	
			  }
      }?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>

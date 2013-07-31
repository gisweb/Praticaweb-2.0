<?$self=$_SERVER["PHP_SELF"];
include "./lib/tabella_h.class.php";
include "./lib/tabella_v.class.php";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>
<title>Sanzioni - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?	
if ($_POST["mode"]=="new"){
		include "./inc/inc.page_header.php";?>
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   SCELTA DEL NUOVO PROVVEDIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=5  cellspacing=2 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="nuovo_provvedimento.php">		  
		  <tr> 
			<td colspan=2> 
			<!-- intestazione-->
				<H2 class="blueBanner">Nuovo provvedimento</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
				<td colspan=2> 
					<h3>scegli il tipo di provvedimento:</h3>
				</td>
			<tr>
				<td width=10>
					<input type=radio name=tipo value="sanzione">
				</td>
				<td width=100%>
					<font size=1><b>Sanzione amministrativa</b></font>
				</td>
			</tr>
			<tr>
				<td >
					<input type=radio name=tipo value="sospensione">
				</td>
				<td>
					<font size=1><b>Ordinanza di sospensione lavori</b></font>
				</td>
			</tr>			
			<tr>
				<td >
					<input type=radio name=tipo value="demolizione">
				</td>
				<td>
					<font size=1><b>Ordinanza di demolizione</b></font>
				</td>
			</tr>				
		  <tr> 
				<!-- riga finale -->
				<td colspan=2 align="center"><img src="images/gray_light.gif" height="2" width="99%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>
				<td><input name="pratica" type="hidden" value="<?=$idpratica?>"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Invia"></td>
				<td valign="bottom"><input name="azione" type="button" class="hexfield" tabindex="14" value="Annulla"></td>			</tr>
		</FORM>		
		</table>	
<?}elseif ($_POST["mode"]=="edit"){
		$tabella=new tabella_h("sanzioni");	
		include "./inc/inc.page_header.php";
		$idpratica=$_POST["pratica"];
		//$sopralluogo=$_POST[""];
		$id=$_POST["id_row"];?>
		<FORM id="" name="" method="post" action="praticaweb.php">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?echo(($sopralluogo)?($sopralluogo):("Inserisci Nuova Sanzione"))?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?if ($id)	$tabella->set_data("from sanzioni where id_row=$id");
					$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>

				<input name="nome_form" type="hidden" value="<?=$self?>">				
				<input name="sopralluogo" type="hidden" value="<?=$id;?>">
				<input name="pratica" type="hidden" value="<?=$idpratica?>">
				
		</FORM>
<?}else{
		$idpratica=$_GET["pid"];
		if(!isset($idpratica)) $idpratica=$_POST["pratica"];
		$tabella=new tabella_h("sanzioni");
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Provvedimenti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella->set_titolo(array($idpratica,"Sanzione","modifica","id_row"));
				$tabella->set_dati(",id_row from sanzioni where pratica=$idpratica order by data_avvio;");
				$tabella->elenco();
    $conn = pg_connect ("dbname=praticaweb user=Roberto") or die ("non mi sono connesso");?>

			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  <TR>
				<TD>
		  		<!---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< NOTIFICHE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------------>
		  <TABLE   class="printhide" width="100%">
		  <tr> 
			<td> 
			<!--  intestazione-->
			<table  class="printhide" width="100%">
					<form method="post" target="_parent" action="<?=$self?>">
						<input type="hidden" name="mode" value="editasserviti">
						<input type="hidden" name="pratica" value="<?=$idpratica?>">						
						<input type="hidden" name="asservimento" value="<?=$asservimento?>">				
						<tr> 
							<?$tabella_asserviti=new tabella_v($conn,"notifiche","notifiche");
							//$tabella_asserviti->set_color("#FFFFFF","#FF0000",0,0);
							$tabella_asserviti->set_titolo("Notifiche","modifica");?>
						</tr>
					</form>
				</table>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
			<!-- contenuto-->
				<?if ($tabella_asserviti->get_rows("id_row=1"))	
						print $tabella_asserviti->elenco();
					else
						print ("nessun mappale inserito");?>
			<!-- fine contenuto-->
			 </td>
	      </tr>
		</TABLE>
		</TD>
	</TR>
		
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
				<?$tabella->set_titolo(array($idpratica,"Aggiungi nuovo Provvedimento >>>>","nuovo"));?>
				<?print $tabella->get_titolo();?><BR>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?}?>

</body>
</html>

<?
include_once("login.php");
//se annullo torno all'inizio
$tabpath="pe";
if ($_POST["mode"]=="edit") {
	$idpratica=$_POST["pratica"];
	include "./inc/inc.page_header.php";
	include "./lib/tabella_h.class.php";
	
	
	
	$tabellah=new Tabella_h("$tabpath/elenco_gruppi.tab");
	?>
	<html>
	<head>
	<title>Riferimenti</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
	<SCRIPT>
		function link(id){
			w=window.open("pe.rif_pratiche.php?id="+id,"Info","width=500,height=600,resizable=no,scrollbars=yes");
			w.focus();
		}
	</SCRIPT>
	</head>
	<body  onload>
	<H2 class="blueBanner">Elenco dei gruppi di pratiche</H2>
	<form action="praticaweb.php" method="POST" name="main">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="70%">
			<TR> 
				<TD> 
				<!-- contenuto-->
					<?	if($tabellah->set_dati("id>0 order by descrizione")){
							//$tabellah->set_titolo("Gruppi","",Array("pratica"=>$idpratica,"step"=>0,"active_form"=>"pe.riferimenti.php"));
							$tabellah->set_titolo("Inserire la pratica ".$_POST["numero"]." appartenente al gruppo \"".$_POST["descrizione"]."\" nel gruppo : ","");
							$tabellah->get_titolo();
							$tabellah->elenco();
							print("
					<TABLE width=\"100%\">
						<TR>
							<TD colspan=\"2\" width=\"100%\" bgColor=\"#728bb8\"><font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\">
								<b>Nuovo Riferimento: inserisci la descrizione per il nuovo gruppo di pratiche</b></font>
							</TD>
						</TR>
						<TR>
							<TD valign=\"top\" width=\"10\"><input type=\"radio\" value=\"0\" name=\"id\" id=\"newid\" checked>
							</TD>
					   		<TD>
								<input type=\"text\" maxLength=\"100\" size=\"100\" id=\"riferimento\" name=\"riferimento\">
							</TD>
						</TR>
					</TABLE>");
							
							}
						else{
							$tabellah->set_titolo("Gruppi","");
							$tabellah->get_titolo();
							print ("\n\t\t\t\t\t<p><b>Nessun gruppo creato</b></p>");
						}
					?>
				<!-- fine contenuto-->
				</TD>
			</TR>
			<TR>
				<TD>
					<input type="submit" class="hexfield" name="azione" value="Annulla">
					<input type="submit" class="hexfield" name="azione" value="Salva">
				</TD>
			</TR>
		</TABLE>
		<input type="hidden" name="pratica=" value="<?=$idpratica?>">
		<input type="hidden" name="step" value="0">
		<input type="hidden" name="id_prec" value="<?=$_POST["id"]?>">
		<input type="hidden" name="active_form" value="pe.riferimenti.php">
	</form>
	</body>
	</html>
	<?exit;
}
if ($_POST["azione"]!="Annulla") $step=$_REQUEST["step"];
if(isset($step)){
	include "./db/db.pe.riferimenti.php";
	if ($step==0){?>
	<html>
	<head>
	<title>Riferimenti</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
	</head>
	<body  background="">
	<H2 class=blueBanner>Elenco dei riferimenti</H2>
	<TABLE border=0 cellpadding=2 width="90%">
	<form name="main" target="_parent" action="pe.riferimenti.php" method="POST">
	<?
	if ($nrif){
		for($i=0;$i<$nrif;$i++){
			if ($riferimenti[$i]["pratica"]==$pratica) $numero=$riferimenti[$i]["numero"];
			if($idrif<>$riferimenti[$i]["id"]){
				print("
			<TR>
				<TD width=\"90%\" bgColor=\"#728bb8\">
					<font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\">
					<b>Riferimento:</b> ".$riferimenti[$i]["descrizione"]."</font>
				</TD>
				<td>
					<input type=\"image\" src=\"images/modifica_btn2.gif\" onclick=\"main.submit()\">
					
				</td>
			</TR>");
				$idrif=$riferimenti[$i]["id"];
			}
			$idpratica=$riferimenti[$i]["pratica"];
			$infogruppo=$riferimenti[$i]["descrizione"];
			include "./db/db.pe.info_pratica.php";
		}
			print("\n\t\t<input type=\"hidden\" name=\"id\" value=\"".$riferimenti[0]["id"]."\">
			<input type=\"hidden\" name=\"pratica\" value=\"$pratica\">
			<input type=\"hidden\" name=\"numero\" value=\"$numero\">
			<input type=\"hidden\" name=\"descrizione\" value=\"".$riferimenti[0]["descrizione"]."\">
			<input type=\"hidden\" name=\"mode\" value=\"edit\">\n");
	}
	else{
		print("\n\t\t\t<TR>
				<TD width=\"90%\" bgColor=\"#728bb8\">
					<font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\">
					<b>Riferimento:</b> ".$riferimenti[$i]["descrizione"]."</font>
				</TD>
				<td>
					<input type=\"image\" src=\"images/modifica_btn2.gif\" onclick=\"main.submit()\">
					
				</td>
			</TR>
			<TR>
				<TD colspan=\"2\" width=\"90%\" >
					<p><b>Nessun riferimento associato a questa pratica</b>
				</TD>
			</TR>");
	}
	print("\n\t\t<input type=\"hidden\" name=\"id\" value=\"".$riferimenti[0]["id"]."\">
			<input type=\"hidden\" name=\"pratica\" value=\"$pratica\">
			<input type=\"hidden\" name=\"numero\" value=\"$numero\">
			<input type=\"hidden\" name=\"descrizione\" value=\"".$riferimenti[0]["descrizione"]."\">
			<input type=\"hidden\" name=\"mode\" value=\"edit\">\n");
	?>
	</form>
	</TABLE>
	</body>
	</html>
	<?
	exit;}
	elseif ($step==1){
		if($riferimenti){//elenco dei riferimenti trovati
?>
	<html>
	<head>
	<title>Riferimenti</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
	<script language="javascript">
	function set_info(info_pratica,info_gruppo){
		document.getElementById("infopratica").value=info_pratica;
		document.getElementById("infogruppo").value=info_gruppo;
	}
	function set_focus(){
		document.getElementById("newid").checked=true;
	}
	
	</script>
	</head>
	<body  background="">
	<?include "./inc/inc.page_header.php";?>
	<form method="post" action="pe.riferimenti.php"> 
	<input name="mode" type="hidden" value="new">
	<p><b>Selezionare la pratica di riferimento:</b></p>
	<TABLE border=0 cellpadding=2>
	<?	for($i=0;$i<$nrif;$i++){
				if($idrif<>$riferimenti[$i]["id"]){
					print("<TR><TD colspan=\"2\" width=\"90%\" bgColor=\"#728bb8\"><font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\">
					<b>Riferimento:</b> ".$riferimenti[$i]["descrizione"]."</font></TD></TR>");
					$idrif=$riferimenti[$i]["id"];
				}
				$idpratica=$riferimenti[$i]["pratica"];
				$infogruppo=$riferimenti[$i]["descrizione"];
				include "./db/db.pe.info_pratica.php";
			}
			print("<TR><TD colspan=\"2\" width=\"90%\" bgColor=\"#728bb8\"><font face=\"Verdana\" color=\"#FFFFFF\" size=\"2\">
						<b>Nuovo Riferimento: inserisci la descrizione per il nuovo gruppo di pratiche</b></font></TD></TR>");
			print("<TR><TD valign=\"top\" width=\"10\"><input type=\"radio\" value=\"0\" name=\"refpratica\" id=\"newid\" checked></TD>
					   <TD><input type=\"text\" maxLength=\"100\" size=\"100\" id=\"riferimento\" name=\"riferimento\" onclick=\"set_focus()\">
					   <input type=\"hidden\" id=\"infopratica\" name=\"infopratica\">
					   <input type=\"hidden\" id=\"infogruppo\" name=\"infogruppo\">
					   <input type=\"hidden\" name=\"via\" value=\"".$_POST["via"]."\">
					   <input type=\"hidden\" name=\"civico\" value=\"".$_POST["civico"]."\">
					   <input type=\"hidden\" name=\"ctsezione\" value=\"".$_POST["ctsezione"]."\">
					   <input type=\"hidden\" name=\"ctfoglio\" value=\"".$_POST["ctfoglio"]."\">
					   <input type=\"hidden\" name=\"ctmappale\" value=\"".$_POST["ctmappale"]."\">
					   <input type=\"hidden\" name=\"step\" value=\"2\">
					   <img height=\"1\" src=\"images/gray_light.gif\" width=\"100%\"  vspace=\"1\"> 
					   </TD></TR><tr><td colspan=\"2\"><table width=\"100%\">");
			print("\n\t\t\t<TR>
				<TD  align=\"left\"><input name=\"azione\" type=\"submit\" value=\"Avanti >>\" class=\"hexfield1\" style=\"width:120px\"></TD>
				<TD align=\"right\"><input name=\"azione\" type=\"submit\" value=\"Annulla\" class=\"hexfield1\" style=\"width:120px\"></TD>
			</TR>
		</table>
	</td>
</tr>");		
			print ("</TABLE>");
			print ("</FORM></body></html>");
			exit;
			}
			else{
				$notfound=1;
				$step++;
				if(!$flag_terr) $m1="una indicazione territoriale e";
				if(!$noquery) $m2="Nessuna pratica soddisfa i criteri di ricerca. ";
				$msg=$m2."Inserire obbligatoriamente $m1 la descrizione del nuovo gruppo di appartenenza per la nuova pratica.";
				$filetab="$tabpath/riferimenti_obbligatori.tab";
			}
		}//end if step=1
		elseif($step==2){
			if ($flagOK){
				include ("pe.avvioproc.php");
				exit;
				/*echo "vado ad avvioproc con <pre>";
				print_r($_POST);*/
			}
			else{
				//print_r($_POST);
				if(!$flag_terr) $m1="una indicazione territoriale e";
				$msg="Inserire obbligatoriamente $m1 la descrizione del nuovo gruppo di appartenenza per la nuova pratica.";
				$filetab="$tabpath/riferimenti_obbligatori.tab";
			}
		}
		elseif($step==3){
			include ("pe.avvioproc.php");
			exit;
			/*echo "vado ad avvioproc con <pre>";
			print_r($_POST);*/
		}
	}
	else{
		$step=1;
		$msg="Assegnare il riferimento ad una pratica precedente o una indicazione territoriale:";
		$filetab="$tabpath/riferimenti.tab";
	}
		
	include "./lib/tabella_v.class.php";
	if($step==1) $event="onclick=\"NewWindow('index.php','indexPraticaweb',0,0,'yes');window.close();\""; 
?>
<html>
<head>
<title>Nuova pratica</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/window.js" type="text/javascript">
</SCRIPT>
</head>
<body  background="">
<?include "./inc/inc.page_header.php";?>

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM name="riferimenti" action="pe.riferimenti.php" method="post">
		<input type="hidden" name="mode" value="new">
		<TABLE cellPadding=2  cellspacing=2 border=0 class="stiletabella" width="800">	
			<tr>
			<td width="90%" bgColor="#728bb8"><font face="Verdana" color="#FFFFFF" size="2"><b><?=$msg?></b></font>
			</td>
		</tr>	
		
		<TR>
			<TD> 
			<!-- contenuto-->
			<?
				$tabella=new tabella_v($filetab);
				$tabella->set_dati($_POST);
				$tabella->edita();?>
			</TD>
		</TR>
		<tr> 
				<!-- riga finale -->
			<td align="left"><img src="images/gray_light.gif" height="2" width="100%"></td>
		   </tr>  
		   <tr>
		  	<td>
				<table width="800">
					<tr>
						<TD align="left"><input name="azione" type="submit" value="Avanti >>" class="hexfield1" style="width:120px"></TD>
						<TD align="right"><input name="azione" type="submit" value="Annulla" class="hexfield1" style="width:120px" <?=$event?> ></TD>
					</tr>
				</table>
				<input type="hidden" name="step" value="<?=$step?>">
			</td>
		</tr>
		</TABLE>		
		</FORM>				
<?include "./inc/inc.window.php"; // contiene la gesione della finestra popup?>
</body>
</html>


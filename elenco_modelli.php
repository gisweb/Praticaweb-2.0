<?include_once("login.php");
include_once "./lib/tabella_h.class.php";
$tabpath="stp";
$usr=$_SESSION['USER_NAME'];
$form=$_REQUEST["form"];
	$file_tab="$tabpath/modelli";
	$titolo="Modelli";
	$param="modello";
	$filtro="form='$form' and (proprietario='pubblico' or proprietario='$usr');";

?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="javascript">
function check_radio(form,nome,azione){
	f=document.getElementById(form);
	r=f.elements[nome];
	if((r.length>1)){
		for (i=0;i<r.length;i++) if (r[i].checked) id=r[i].value;
		if (id > 0){
			if (((form=="dochtml") || (form=="docpdf")) && (azione=="elimina")) return confirm('Sei sicuro di voler eliminare il documento ?');
			else
				return true;
		}
		else{
			alert("Seleziona un elemento.");
			return false;
		}
	}
	else{
		if (f.elements[nome].checked){
			if (((form=="dochtml") || (form=="docpdf")) && (azione=="elimina")) return confirm('Sei sicuro di voler eliminare il documento ?');
			else
				return true;
		}
		else{
			alert("Seleziona un elemento.");
			return false;
		}
	}
}

</script>
<form method="POST" action="elenco_modelli.php" name="modelli">
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="480">
	<TR>
		<TD colspan="4">
		<?
		$tabella=new Tabella_h($file_tab,'list');
		$tabella->set_titolo($titolo);
		$tabella->set_tag($param);
		$numrows=$tabella->set_dati($filtro);
		$tabella->get_titolo();
		if ($numrows){
			$tabella->elenco();
			
		}
		else
			print ("<p><b>Nessun documento prodotto</b></p>");
		
		?>
		</TD>
	</TR>
	<?
	
		//echo "ERRORI PASSO $i : ".count($tab_err[$i])."<br>";
		for($j=0;$j<count($tab_err[$i]);$j++) {
			$riga_err="\n\t<TR>
		<TD width=\"720\" colspan=\"4\" align=\"left\"><span style=\"margin-left:25px\"><font color=\"red\">".$tab_err[$i][$j]."</font></span></TD>
	</TR>\n";
			print($riga_err);
			
		}
		if ($tab_err[0]) $hidden="visible";
		
	?>
	<TR>
		<TD colspan="4">
			<input type="submit" alt="Crea il documento" value="crea_documento" name="azione" class="hexfield1" style="margin-top:10;margin-bottom:10;margin-left:20;" onclick="return check_radio('modelli','nome','')">

		</TD>
	</TR>
</table>
</form>

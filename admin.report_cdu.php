<?
include "login.php";
if ($_SESSION["PERMESSI"] > 3){ 
   include_once HOME;
   exit;
}
function controlla_data($val){
	$l=strlen($val);
	//primo controllo se i caratteri inseriti sono del tipo corretto
	if (strlen($val)>0 and !ereg ("([0123456789/.-]{".$l."})", $val)){
		$OK_Save=0;
		$errors="Formato della data non valido $val";
	}
	else{
		list($giorno,$mese,$anno) = split('[/.-]', $val);
	//Da Verificare..... il 30 Febbraio 2005 lo prende se scritto come anno-mese-giorno con anno a 2 cifre!!!!! Errore
		if (strlen($val)>0 and (checkdate((int) $mese,(int) $giorno,(int) $anno))){
			$val="'".$giorno."/".$mese."/".$anno."'";
		}
		elseif (strlen($val)>0 and strlen($giorno)>3 and (checkdate((int) $mese,(int) $anno,(int) $giorno))) {
			$val="'".$anno."/".$mese."/".$giorno."'";
		}
		elseif (strlen($val)>0 and strlen($giorno)<=2 and (checkdate((int) $mese,(int) $anno,(int) $giorno))) {
			$OK_Save=0;
			$errors="Data ambigua $val";
		}
		elseif (strlen($val)>0) {
			$OK_Save=0;
			$errors="Data non valida $val";
		}
		elseif (strlen($val)===0) $val="";
		
	}
	return array("data"=>$val,"errore"=>$errors);
}
include "./lib/tabella_h.class.php";

if ($_POST["azione"]=="Avvia"){
	$ricerca["data_in"]=controlla_data($_POST["data_in"]);
	$ricerca["data_fi"]=controlla_data($_POST["data_fi"]);
	//echo "<pre>";print_r($ricerca);
	if (!($ricerca["data_in"]["errore"] or $ricerca["data_fi"]["errore"])){
		//include "./db/db.admin.report.php";
		if ($ricerca["data_in"]["data"]){
			if ($ricerca["data_fi"]["data"]) $arr_cond[]="report.data::date BETWEEN ".$ricerca["data_in"]["data"]."::date AND ".$ricerca["data_fi"]["data"]."::date";
			else
				$arr_cond[]="report.data::date=".$ricerca["data_in"]["data"]."::date";
		}
	
		if (is_array($arr_cond)) $cond="(".implode(") AND (",$arr_cond).")";
		$tabella=new Tabella_h("admin/report_cdu",'view');
		$nrows=$tabella->set_dati("$cond order by data");
	}	
}
?>
<html>
<head>
	<TITLE>Report delle Pratiche</TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<LINK media="screen" href="src/styles.css" type="text/css" rel="stylesheet">
	<LINK media="print" href="src/styles_print.css" type="text/css" rel="stylesheet">
	<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
	<SCRIPT language=javascript src="src/iframe.js" type="text/javascript"></SCRIPT>
	<script language=javascript>
		function show(e){
			var obj=document.getElementById(e);
			if (obj && obj.style.display=='')
				obj.style.display='none';
		}
	</script>
	<style>
		p{
			FONT: 11px/1.3em Verdana, Geneva, Arial, sans-serif;
			COLOR: #415578
		}
	</style>
</head>
<body onload="show('elab')">
	<div id="elab" style="position:absolute;top:200px;left:200px;display:none"><img src="images/elabor.gif" border=1></img></div>
	<?include "./inc/inc.page_header.php";?>
	<form name="" action="admin.report_cdu.php" method="POST">
		<H2 class="bluebanner"> Ricerca cdu </H2>
		<table class="stiletabella">
			<TR>
				<td width="250" bgColor="#728bb8"><font color="#ffffff"><b>Data di richiesta - Dal - Al</b></TD>
				<?if (!$ricerca["data_in"]["errore"]){?><TD><input type="text" class="textbox" name="data_in" align="right" value="<?=$_POST["data_in"]?>"></TD><?}
				else{?>
				<TD><input type="text" class="errors" name="data_in" value="<?=$_POST["data_in"]?>" align="right"><image src="images/small_help.gif" onclick="alert('<?=$ricerca["data_in"]["errore"]?>')"></TD>
				<?}
				if (!$ricerca["data_fi"]["errore"]){?><TD><input type="text" class="textbox" name="data_fi" value="<?=$_POST["data_fi"]?>" align="right"></TD><?}
				else{?>
					<TD><input type="text" class="errors" name="data_fi" value="<?=$_POST["data_fi"]?>" align="right"><image src="images/small_help.gif" onclick="alert('<?=$ricerca["data_fi"]["errore"]?>')"></TD>
				<?}?>
			</TR>
			
			<TR>
				<TD>&nbsp;</TD>
				<TD align="right"><input type="button" class="hexfield" value="Chiudi" style="margin-top:20px" onclick="window.opener.focus();window.close();"></TD>
				<TD align="right"><input type="submit" class="hexfield" name="azione" value="Avvia" style="margin-top:20px" onclick="javascript:document.getElementById('elab').style.display='';"></TD>
			</TR>
		</table>
	</form>
	<hr>
	<H2 class="bluebanner">Riepilogo dei cdu trovati</H2>
	<?	if ($nrows)
			$tabella->elenco();
		else
			print "<table class=\"stiletabella\"><tr><td><b>Nessun Dato Trovato</b></td></tr></table>";
	?>
</body>
</html>

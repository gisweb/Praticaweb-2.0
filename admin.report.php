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
	$ricerca["data_ins"]=controlla_data($_POST["data_ins"]);
	$ricerca["data_pres_in"]=controlla_data($_POST["data_pres_in"]);
	$ricerca["data_pres_fi"]=controlla_data($_POST["data_pres_fi"]);
	$ricerca["tipo_pratica"]=$_POST["tipo_pratica"];
	//echo "<pre>";print_r($ricerca);
	if (!($ricerca["data_ins"]["errore"] or $ricerca["data_pres_in"]["errore"] or $ricerca["data_pres_fi"]["errore"])){
		list($dd,$mm,$yyyy)=explode("/",str_replace("'","",$ricerca["data_ins"]["data"]));
		$tmsmin=mktime (0,0,0,$mm,$dd,$yyyy);
		$tmsmax=mktime (0,0,0,$mm,$dd+1,$yyyy);
		//include "./db/db.admin.report.php";
		if ($ricerca["data_pres_in"]["data"]){
			if ($ricerca["data_pres_fi"]["data"]) $arr_cond[]="report.data_presentazione::date BETWEEN ".$ricerca["data_pres_in"]["data"]."::date AND ".$ricerca["data_pres_fi"]["data"]."::date";
			else
				$arr_cond[]="report.data_presentazione::date=".$ricerca["data_pres_in"]["data"]."::date";
		}
		if ($ricerca["tipo_pratica"]) {
			if ($ricerca["tipo_pratica"]==1)
				$arr_cond[]="report._tipo_pratica BETWEEN 11000 AND 11999";
			elseif($ricerca["tipo_pratica"]==2)
				$arr_cond[]="report._tipo_pratica BETWEEN 10000 AND 10999";
			elseif($ricerca["tipo_pratica"]>2)
				$arr_cond[]="report._tipo_pratica = ".$ricerca["tipo_pratica"]."";
		}

		if (is_array($arr_cond)) $cond="(".implode(") AND (",$arr_cond).")";
		$tabella=new Tabella_h("admin/report",'view');
		$nrows=$tabella->set_dati("$cond order by substr(report.numero,4,1),substr(report.numero,5,3),substr(report.numero,1,3)");
	}	
}
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql="(SELECT 0 as id, 'Seleziona ===>' as opzione) UNION (SELECT 1 as id, 'Tutti i Permessi di Costruire' as opzione) UNION (SELECT 2 as id, 'Tutte le D.I.A.' as opzione) UNION (SELECT distinct id,nome as opzione FROM pe.e_tipopratica order by opzione);";
$db->sql_query($sql);
print_debug($sql);
$tipo_pratica=$db->sql_fetchrowset();
$sel_tipo_pratica="";
foreach($tipo_pratica as $val){
	$s=($_POST["tipo_pratica"] && $_POST["tipo_pratica"]==$val["id"])?("selected"):("");
	$sel_tipo_pratica.="<option value=\"".$val["id"]."\" $s>".$val["opzione"]."</option>";	
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
	<form name="" action="admin.report.php" method="POST">
		<H2 class="bluebanner"> Ricerca pratiche </H2>
		<table class="stiletabella">
			<TR>
				<td width="250" bgColor="#728bb8"><font color="#ffffff"><b>Data di presentazione - Dal - Al</b></TD>
				<?if (!$ricerca["data_pres_in"]["errore"]){?><TD><input type="text" class="textbox" name="data_pres_in" align="right" value="<?=$_POST["data_pres_in"]?>"></TD><?}
				else{?>
				<TD><input type="text" class="errors" name="data_pres_in" value="<?=$_POST["data_pres_in"]?>" align="right"><image src="images/small_help.gif" onclick="alert('<?=$ricerca["data_pres_in"]["errore"]?>')"></TD>
				<?}
				if (!$ricerca["data_pres_fi"]["errore"]){?><TD><input type="text" class="textbox" name="data_pres_fi" value="<?=$_POST["data_pres_fi"]?>" align="right"></TD><?}
				else{?>
					<TD><input type="text" class="errors" name="data_pres_fi" value="<?=$_POST["data_pres_fi"]?>" align="right"><image src="images/small_help.gif" onclick="alert('<?=$ricerca["data_pres_fi"]["errore"]?>')"></TD>
				<?}?>
			</TR>
			<TR>
				<td width="250" bgColor="#728bb8"><font color="#ffffff"><b>Data di inserimento</b></td>
				<TD>&nbsp;</TD>
				<?if (!$ricerca["data_ins"]["errore"]){?><TD><input type="text" class="textbox" name="data_ins" align="right" value="<?=$_POST["data_ins"]?>"></TD><?}
				else{?>
					<TD><input type="text" class="errors" name="data_ins" value="<?=$_POST["data_ins"]?>" align="right"><image src="images/small_help.gif" onclick="alert('<?=$ricerca["data_ins"]["errore"]?>')"></TD>
				<?}?>
			</TR>
			<TR>
				<td width="250" bgColor="#728bb8"><font color="#ffffff"><b>Tipo di Pratica</b></TD>
				<td colspan="2" align="right" valign="bottom">
					<select class="stiletabella" name="tipo_pratica">
						<?
						echo $sel_tipo_pratica;
						
						?>
					</select>
				</td>
			</TR>
			<TR>
				<TD>&nbsp;</TD>
				<TD align="right"><input type="button" class="hexfield" value="Chiudi" style="margin-top:20px" onclick="window.opener.focus();window.close();"></TD>
				<TD align="right"><input type="submit" class="hexfield" name="azione" value="Avvia" style="margin-top:20px" onclick="javascript:document.getElementById('elab').style.display='';"></TD>
			</TR>
		</table>
	</form>
	<hr>
	<H2 class="bluebanner">Riepilogo delle pratiche trovate</H2>
	<?	if ($nrows)
			$tabella->elenco();
		else
			print "<table class=\"stiletabella\"><tr><td><b>Nessun Dato Trovato</b></td></tr></table>";
	?>
</body>
</html>

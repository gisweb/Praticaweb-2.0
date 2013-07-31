<?
include_once ("login.php");
$tabpath="pe";
$titolo=$_SESSION["TITOLO_$idpratica"];
$idpratica=$_POST["pratica"];
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql="select e_vincoli.nome,e_vincoli.descrizione,vincoli.zona from pe.e_vincoli,pe.vincoli where e_vincoli.nome::text=vincoli.vincolo::text 
and e_vincoli.parametri=1  and vincoli.pratica=$idpratica order by e_vincoli.ordine";
$db->sql_query ($sql);
$vincoli=$db->sql_fetchrowset();
$nzone=$db->sql_numrows();
$numcols=$nzone+3;

for($i=0;$i<$nzone;$i++){
	if($vincolo!=$vincoli[$i]["nome"]){
		$vincolo=$vincoli[$i]["nome"];
		if($n) $colspan[]=$n;//numero di colonne per lo span del vincolo
		$index[]=$i;//primo indice dove trovo la descrizione del vincolo
		$n=1;
	}
	else
		$n++;
		
	// una colonna di parametri per ogni zona	ARRAY[vincolo][zona][parametro]
	$zona=$vincoli[$i]["zona"];
	$sql="select parametro,valore from pe.e_parametri_zone where vincolo='$vincolo' and zona='$zona'";
	$db->sql_query ($sql);
	$par=$db->sql_fetchrowset();
	//echo "$vincolo $zona";
	//print_r($par);
	for($j=0;$j<count($par);$j++){
		$idparametro=$par[$j]["parametro"];
		$valore=$par[$j]["valore"];
		//$ar[$par[$j]["parametro"]]=$par[$j]["valore"];		
		$par_zone[$vincolo][$zona][$idparametro]=$valore;
	}
}

$colspan[]=$n;

$sql="select * from pe.parametri_progetto where pratica=$idpratica";
$db->sql_query ($sql);
$parametri=$db->sql_fetchrowset();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<title>Raffronto<?=$titolo?></title>
</head>
<body>

<?include "./inc/inc.page_header.php";?>

<table cellPadding=2  cellspacing=1 width="100%" class="stiletabella" border=0>
<FORM name="progetto" method="post" action="praticaweb.php">

	<tr bgColor=#728bb8>
	<td rowspan="2" ></td>
        <td width="200" rowspan="2"  valign="middle" align="center"><font face="Verdana" color="#ffffff" size="1"><b>Parametro</b></font></td>
		<td rowspan="2" width="1"></td>
        <td width="100" rowspan="2"  valign="middle" align="center"><font face="Verdana" color="#ffffff" size="1"><b>Dato di Progetto</b></font></td>
	<td width="20" rowspan="2"  align="center"><font face="Verdana" color="#ffffff" size="1"><b>Conf.</b></font></td>		
	<?for($i=0;$i<count($index);$i++){?>	
		<td height="15" colspan="<?=$colspan[$i]?>" align="center">
			<font face="Verdana" color="#ffffff" size="1"><b><?=$vincoli[$index[$i]]["descrizione"]?></b></font>
		</td>
		<?}?>
	</tr>
	<tr bgColor=#728bb8>
	<?for($i=0;$i<$nzone;$i++){?>	
		  <td valign="top" align="center"><font face="Verdana" color="#ffffff" size="1"><b>Zona: <?=$vincoli[$i]["zona"]?></b></font></td>
	<?}
	echo("</tr>");
	
	// ###### Elenco parametri #########
	for($i=0;$i<count($parametri);$i++){
		$idparametro=$parametri[$i]["idpar"];
		($parametri[$i]["conforme"]==1)?($checked="checked"):($checked="");?>
		
	<tr>
		<td width="10" align="center">
			<!--<a target="new" href="norme.htm"><img src="images/info.gif" border="0"></a>-->
		</td>
		<td width="220"><?=$parametri[$i]["parametro"]?></td>
		<td><img src="images/gray_light.gif" height="15" width="1"></td>
		<td width="200"><?=$parametri[$i]["valore"]?></td>	
		<td align="center"><input type="checkbox" name="conforme[<?=$idparametro?>]"  <?=$checked?>></td>
		<?for($j=0;$j<$nzone;$j++){
			$vincolo=$vincoli[$j]["nome"];
			$zona=$vincoli[$j]["zona"];
			$valore_parametro=$par_zone[$vincolo][$zona][$idparametro];
			if(!$valore_parametro) $valore_parametro="----";
			print ("<td>$valore_parametro</td>");
			} ?>		
    </tr>
	<tr><td colspan="9"><img src="images/gray_light.gif" height="1" width="100%"></td></tr>
	
<?}?>

</table>
<br>
			<table>
			<tr>
				<td><input name="pratica" type="hidden" value="<?=$idpratica?>">
				<input name="active_form" type="hidden" value="pe.tabella_progetto.php"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>
		</table>	
		</FORM>		
	</body>
	</html>
<?php
include_once ("login.php");
$titolo=$_SESSION["TITOLO_$idpratica"];
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$curr_part=$_REQUEST["part"];
$ar_particella=explode(",",$curr_part); 
if($ar_particella[0])
	$sql_update_mappale="sezione='".$ar_particella[0]. "',";
$sql_update_mappale="foglio='" . $ar_particella[1] . "' , mappale='" . $ar_particella[2] . "'";

$tabpath=cdu;
$conn=utils::getDb();
$azione=$_POST["azione"]; 
if ($_POST["azione"]){ 
	$idrow=$_POST["idriga"];
	$active_form=$_REQUEST["active_form"]; 
	if($_SESSION["ADD_NEW"]!==$_POST){
			unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
		if (isset($array_dati["errors"])) //sono al ritorno errore
			$Errors=$array_dati["errors"];
		else 
			include_once "./db/db.cdu.vincoli.php";
		if ($modo=="new"){ 
			$newid=$_SESSION["ADD_NEW"];
			$sql="update cdu.mappali set $sql_update_mappale where id=$newid";
			
			$conn->exec($sql);
		}
		$_SESSION["ADD_NEW"]=$_POST;	
	}
}

?>
<html>
<head>

<title>Vincoli - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadJS();
    utils::loadCss();
?>
<SCRIPT language="javascript" src="src/http_request.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/x_core.js" type="text/javascript"></SCRIPT>
<script language=javascript>

function confirmSubmit()
{
	document.getElementById("azione").value="Salva";
	return true ;
}

function elimina(id){
	var agree=confirm('Sicuro di voler eliminare definitivamente la riga selezionata?');
	if (agree){
		$("#btn_azione").val("Elimina");
		$("#idriga").val(id);
		$('#vincoli').submit();
	}
}
function link(id){
	window.location="pe.scheda_normativa.php?id="+id;
}

</script>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php
if (($modo=="edit") or ($modo=="new")){
	include_once "./lib/tabella_h.class.php";
	include_once "./lib/tabella_v.class.php";
	$tabellav=new tabella_v("$tabpath/zone_mappale",'new');
	$tabellah=new tabella_h("$tabpath/zone_mappale",'edit');
	$tabellav->set_errors($errors);
	if($ar_particella[0]){
		$sparticella="Sezione " . $ar_particella[0]. " ";
		$sql="sezione='".$ar_particella[0]. "' and ";
	}
	$sparticella.="Foglio ".  $ar_particella[1]. " Mappale " .  $ar_particella[2];
	$sql="foglio='" . $ar_particella[1] . "' and mappale='" . $ar_particella[2] . "'";
	include "./inc/inc.page_header.php";?>
<form method=post name="vincoli" id="vincoli" action="cdu.vincoli.php">
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<TR> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Modifica elenco vincoli associati alla particella <?=$sparticella?></H2>
			<!-- fine intestazione-->
			</td>
		  </TR>
		  <TR> 
			<td> 
				<!-- contenuto-->

				<input type="hidden" name="idriga" id="idriga" value="0">
				<input type="hidden" name="part" id="part" value="<?=$curr_part?>">				
				<input name="active_form" type="hidden" value="cdu.vincoli.php">
				<input name="cdu" type="hidden" value="1"></td>
				<input type="hidden" name="mode" value="new">

				<?php
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				  $tabellav->edita();?>
				
				<!-- fine contenuto-->			
			</td>
		  </TR>
	</table>
<p>&nbsp;</p>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">	
		
	<?php   
		$sth=$conn->prepare ("select * from vincoli.vincolo order by ordine;");
		$sth->execute();
		$elenco_vincoli = $sth->fetchAll(PDO::FETCH_ASSOC); 
		foreach($elenco_vincoli as $row){
			$vincolo=$row["nome_vincolo"];
			$nome_vincolo=$row["descrizione"];	
			$num_zone=$tabellah->set_dati("pratica=$idpratica and vincolo='$vincolo' and $sql");
		?>
		  <TR> 
			<td> 
			<?php
				if ($num_zone) {
					print ("<b>$nome_vincolo</b>");
					$tabellah->elenco();
				}?>
			</td>
		  </TR>
<?php }// end for?>
	</TABLE>
</form>		
		<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup
	}//end if

else{
	
//########## MODALITA' VISTA DATI #####################


//SCHEMA DB VECCHIO
//elenco dei piani
/*
$sql_piani="select nome,descrizione from pe.e_vincoli where cdu=1 order by ordine;";
print_debug($sql_piani,"tabella");
//verifico l'esitenza dei vincoli per la pratica corrente
$sql_vincoli="select (coalesce(cdu.mappali.sezione,'') || ','::text || cdu.mappali.foglio || ','::text || cdu.mappali.mappale) as particella,mappali.vincolo,mappali.zona,mappali.perc_area,e_vincoli.descrizione from pe.e_vincoli, cdu.mappali where
mappali.vincolo=e_vincoli.nome and pe.e_vincoli.cdu=1 and pratica=$idpratica order by cdu.mappali.perc_area desc, cdu.mappali.sezione,cdu.mappali.foglio,cdu.mappali.mappale;";
print_debug("Vincoli\n".$sql_vincoli);
//aggiungo i mappali che non risultano legati a vincoli
$sql_mappali="select (coalesce(cdu.mappali.sezione,'') || ','::text || cdu.mappali.foglio || ','::text || cdu.mappali.mappale)  as particella from cdu.mappali where pratica=$idpratica and vincolo is null;";
print_debug($sql_mappali,"tabella");
*/
//SCHEMA DB NUOVO
//elenco dei piani
//verifico l'esitenza dei vincoli per la pratica corrente
$sqlElencoTavole="SELECT A.nome_vincolo as vincolo,A.descrizione as desc_vincolo,B.nome_tavola as tavola,coalesce(B.descrizione,B.descrizione) as desc_tavola FROM vincoli.vincolo A inner join vincoli.tavola B using(nome_vincolo) WHERE cdu=1 order by A.ordine,B.ordine";
//echo "<p>$sqlElencoTavole</p>";
$sth=$conn->prepare($sqlElencoTavole); 
$sth->execute();
$ris=$sth->fetchAll(PDO::FETCH_ASSOC);
for($i=0;$i<count($ris);$i++){
    $tavole[$ris[$i]["vincolo"]]["label"]=$ris[$i]["desc_vincolo"];
    $tavole[$ris[$i]["vincolo"]]["tavole"][$ris[$i]["tavola"]]=$ris[$i]["desc_tavola"];
}
$sqlMappali = "SELECT DISTINCT A.sezione as sez,nome as sezione,foglio,mappale FROM cdu.mappali A left join nct.sezioni B USING(sezione) WHERE pratica=$idpratica order by 2,3,4";
$db->sql_query ($sqlMappali); 
$mappali=$db->sql_fetchrowset();
$nmappali=count($mappali);
$sqlVincoli=<<<EOT
WITH vincoli AS(
SELECT A.nome_vincolo,A.descrizione as desc_vincolo,B.nome_tavola,coalesce(B.descrizione,B.descrizione) as desc_tavola,C.nome_zona,C.descrizione as desc_zona,C.sigla FROM vincoli.vincolo A inner join vincoli.tavola B using(nome_vincolo) inner join vincoli.zona C using(nome_tavola,nome_vincolo)
),
mappali as (
select A.pratica,B.nome as sezione,foglio,mappale,vincolo as nome_vincolo,tavola as nome_tavola,zona as nome_zona,perc_area from cdu.mappali A left join nct.sezioni B using(sezione) WHERE pratica=$idpratica
)
select nome_vincolo,nome_tavola,desc_vincolo,desc_tavola,coalesce(sezione,'-') as sezione,foglio,mappale,array_to_string(array_agg(zona),'<br>') as zona from 
(
	select distinct nome_vincolo,nome_tavola,desc_vincolo,desc_tavola,sezione,foglio,mappale,sigla || coalesce('('||perc_area||'%)','') as zona 
	from 
		vincoli inner join mappali using(nome_vincolo,nome_tavola,nome_zona) 
	WHERE pratica=$idpratica order by 5,6,7,1,2
) X
group by 1,2,3,4,5,6,7	 order by 6,7,1,2
EOT;

$db->sql_query ($sqlVincoli); 
$ris=$db->sql_fetchrowset();
for($i=0;$i<count($ris);$i++){
    $d=$ris[$i];
    $vincoli[$d["nome_vincolo"]][$d["nome_tavola"]][$d["sezione"]][$d["foglio"]][$d["mappale"]]=$d["zona"];
}

//print_array($vincoli);
$array_mappali=array();
$array_zone=array();

//verifico se esiste il vincolo nelle tavole
	for ($r=0; $r < $nvincoli; $r++){
        $idparticella=$vincoli[$r]["particella"];
        $piano=$vincoli[$r]["tavola"];  
        $zona=$array_zone[$idparticella][$piano];
        if ($zona)
            $zona.="<br>".$vincoli[$r]["zona"]." (".$vincoli[$r]["perc_area"]." %)";
        else
            $zona=$vincoli[$r]["zona"]." (".$vincoli[$r]["perc_area"]." %)";
        $array_zone[$idparticella][$piano]=$zona; 
	}



$array_mappali=array_keys ($array_zone);
$maxTavole=0;
foreach($tavole as $k=>$v) $maxTavole = ($maxTavole <count($v["tavole"]))?(count($v["tavole"])):($maxTavole);

$req="mode=edit&pratica=$idpratica";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadJS();
    utils::loadCss();
?>
<title>Raffronto<?=$titolo?></title>
<style>

    td.titolo-vincoli{
        background-color:#E5ECF9;
        font-family:Verdana;
        font-weight: bold;
        color:#728bb8;
        font-size: 11px;
        cursor:pointer;
    }
    td.header-vincoli{
        background-color: #728bb8;
        font-family:Verdana;
        font-weight: bold;
        color:#ffffff;
        font-size: 11px;
    }
    td.label-vincoli{
		width:10%;
        background-color: #728bb8;
        font-family:Verdana;
        font-weight: bold;
        color:#ffffff;
        font-size: 10px;
        width:12%;
    }
    td.value-vincoli{
        text-align: center;
        font-size: 10px;
        padding:5px;
    }
</style>
<script>
    $(document).ready(function(){
        $('td[data-url]').bind('click',function(event){
            event.preventDefault();
            var data=$(this).data();
            window.parent.location=data['url'];
        });
    });
</script>
</head>
<body>

<?php
//include "./inc/page_header.inc";
/*********************************************************************************************************/    
//                              MODIFICA TEMPORANEA PER GIRARE VINCOLI E PARTICELLE
/*********************************************************************************************************/
if(TRUE){
?>
<H2 class="blueBanner">Tabella dei vincoli per mappale:</H2>
<FORM name="cdu" method="post" action="praticaweb.php">
<table cellPadding=2  cellspacing=2 width="100%" class="stiletabella" border=1>

<tr>
    <?php 
	$rowTitolo[]='<td class="header-vincoli">&nbsp;</td>';
    for($i=0;$i<count($mappali);$i++){
        $url=sprintf("cdu.vincoli.php?pratica=%d&mode=edit&part=%s,%s,%s",$idpratica,(string)$mappali[$i]["sez"],(string)$mappali[$i]["foglio"],(string)$mappali[$i]["mappale"]);
        $rowTitolo[]=sprintf('<td title="Clicca qui per modificare i vincoli della particella" rowspan="2" class="titolo-vincoli" data-url="%s"><span class="ui-icon ui-icon-pencil" style="float:right;"></span>Sez: %s<br/>Fg: %s<br/>Map:%s</td>',$url,$mappali[$i]["sezione"],(string)$mappali[$i]["foglio"],(string)$mappali[$i]["mappale"]);
    }
	echo implode("",$rowTitolo);
	?>	
</tr>
<tr>
	<td class="header-vincoli">Vincoli</td>
</tr>
<?php
foreach($tavole as $vkey=>$tav){
    
    $row=sprintf('<tr><td colspan="%d" class="header-vincoli">%s</td></tr>',(!$nmappali)?(2):($nmappali+1),$tav["label"]);
    print $row;
    
    foreach($tav["tavole"] as $tkey=>$label){
        $cols=Array();
        $cols[]=sprintf('<td class="label-vincoli">%s</td>',$label);
		if(!$nmappali){
			$cols[]=sprintf('<td class="value-vincoli">%s</td>','---');
		}
		else{
			for($i=0;$i<$nmappali;$i++){
				$map=$mappali[$i];
				//print_array($map);
				$zona=$vincoli[$vkey][$tkey][($map["sezione"]?$map["sezione"]:'-')][$map["foglio"]][$map["mappale"]];
				$cols[]=sprintf('<td class="value-vincoli">%s</td>',($zona)?($zona):('---'));
			}
		}
        printf('<tr>%s</tr>',implode("",$cols));
    }
}
    ?>
</table>
</FORM>		
			
<?php
/*********************************************************************************************************/    
//                              FINE MODIFICA
/*********************************************************************************************************/
}
else{

    ?>
<H2 class="blueBanner">Tabella dei vincoli per mappale:</H2>

<table cellPadding=2  cellspacing=2 width="100%" class="stiletabella" border=1>
<FORM name="cdu" method="post" action="praticaweb.php">
<tr bgColor=#728bb8>
    <td colspan=2 width="140" rowspan="2"  valign="middle" align="center">
		<font face="Verdana" color="#ffffff" size="1"><b>Vincoli</b></font>
	</td>
	<td colspan="<?=$npiani?>" align="center"><font face="Verdana" color="#ffffff" size="1"><b>Vincoli</b></font></td>		
</tr>
<tr bgColor=#728bb8>
	<?php for($i=0;$i<$npiani;$i++){?>	
		<td height="15"  align="center">
			<font face="Verdana" color="#ffffff" size="1"><b><?=$piani[$i]["descrizione"]?></b></font>
		</td>
	<?php }?>
</tr>

<?php

for($i=0;$i<count($array_mappali);$i++){ 
	$idparticella=$array_mappali[$i]; 
	$ar_particella=explode(',',$idparticella);
	if ($ar_particella[0]) 
		$particella='Sez. '. $ar_particella[0].' F. '. $ar_particella[1].' M. '. $ar_particella[2];
	else
		$particella=' F. '. $ar_particella[1].' M. '. $ar_particella[2];
	$url="cdu.vincoli.php?pratica=$idpratica&mode=edit&part=$idparticella";?>
<tr>
	<td><a href="<?=$url?>" target="_parent"><img src="images/propri.gif" border=0></a></td>
	<td height="15"  align="center">
		<font face="Verdana"  size="1"><b><?=$particella?></b></font>
	</td>
	<?php for($j=0;$j<$npiani;$j++){
	
		$piano=$piani[$j]["nome_tavola"]; 
		$zona=$array_zone[$idparticella][$piano]; 
	
			if(!$zona) $zona="---";
		?>	
		<td height="15"  align="left">
			<font face="Verdana" size="1"><b><?=$zona?></b></font>
	</td>
	<?php }?>
</tr>
<?php }?>

<?php //Aggiungo i mappali senza vincoli
	for($i=0;$i<count($mappali);$i++){
		$idparticella=$mappali[$i]['particella'];
		$ar_particella=explode(',',$mappali[$i]["particella"]); 
		if ($ar_particella[0]) 
			$particella='Sez. '. $ar_particella[0].' F. '. $ar_particella[1].' M. '. $ar_particella[2];
		else
			$particella=' F. '. $ar_particella[1].' M. '. $ar_particella[2];
		$url="cdu.vincoli.php?pratica=$idpratica&mode=edit&part=$idparticella"; 
		?>
		<tr>
	<td><a href="<?=$url?>" target="_parent"><img src="images/propri.gif" border=0></a></td>
	<td height="15"  align="center">
		<font face="Verdana"  size="1"><b><?=$particella?></b></font>
		</b></font>
	</td>
	<?php for($j=0;$j<$npiani;$j++){?>
		<td height="15"  align="left">
			<font face="Verdana" size="1"><b>---</b></font>
	</td>
	<?php }?>
</tr>
<?php }?>
</table>
</FORM>		
			
<?php
}
	include_once "./lib/tabella_v.class.php";
	$tabellav=new tabella_v("$tabpath/stampa.tab");
	$tabellav->set_dati("id>0");
	//$tabellav->edita();
	print($tabellav->elenco_stampe("cdu.vincoli"));
}?>
	</body>
	</html>

<?php
include_once ("login.php");
$mode=$_REQUEST["mode"];
$db=new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME,false);
if (!$db->db_connect_id) die("Impossibile Connettersi al Database");
$totali_pr=$_POST["totali_pr"];
if ($mode=="view"){			//MODALITA' DI RICERCA
	$_SESSION["tot_pagine"]=0;
	$sel_size="widt:150px;";
	
	$sql="SELECT DISTINCT date_part('year',data_presentazione) as anno FROM pe.avvioproc ORDER BY 1;";
	$db->sql_query($sql);
	$anni=$db->sql_fetchlist("anno");
	foreach($anni as $val)
		$html["anno"].="<option value=\"$val\">$val</option>";
	$html["anno"]="<select name=\"anno\" class=\"textbox\"  style=\"$sel_size\"><option value=\"-1\" selected>Seleziona ===></option><option value=\"0\">Tutti</option>".$html["anno"]."</select>";
	for($i=5;$i<=30;$i=$i+5) $html["limit"].="<option value=\"$i\">$i</option>";
	$html["limit"]="<select name=\"limit\" class=\"textbox\"  style=\"$sel_size\"><option value=\"-1\" selected>Seleziona ===></option>".$html["limit"]."</select>";
}
else{
	$offset=($_REQUEST["offset"])?($_REQUEST["offset"]):("0");
	$limit=($_REQUEST["limit"])?($_REQUEST["limit"]):("100");
	$anno=$_REQUEST["anno"];
	$tipo=($_REQUEST["tipo_pratica"] )?($_REQUEST["tipo_pratica"]):("0");
	$generaFile = ($_POST["generafile"])?(1):(0);
	include_once "./lib/anagr_tributaria.php";
	
	

	switch($tipo){
		case 0:
			$arr_filter_pc[]=($anno > 0)?("date_part('year',lavori.il)=$anno"):("date_part('year',lavori.il)>=2005 ");
			$arr_filter_pc[]="avvioproc.tipo IN (SELECT DISTINCT id FROM pe.e_tipopratica WHERE anagr_trib = 1) ";
//                        $arr_filter_pc[] = "avvioproc.pratica IN (SELECT DISTINCT pratica FROM pe.soggetti WHERE esecutore=1) ";
			$arr_filter_dia[]=($anno > 0)?("date_part('year',avvioproc.data_presentazione)=$anno"):("date_part('year',avvioproc.data_presentazione)>=2005 ");
			$arr_filter_dia[]="avvioproc.tipo IN (SELECT DISTINCT id FROM pe.e_tipopratica WHERE anagr_trib = 2) ";
//			$arr_filter_dia[] = "avvioproc.pratica IN (SELECT DISTINCT pratica FROM pe.soggetti WHERE esecutore=1) ";
			break;
		case 1:
			$arr_filter_pc[]=($anno > 0)?("date_part('year',lavori.il)=$anno"):("date_part('year',lavori.il)>=1950 ");
			$arr_filter_pc[]="avvioproc.tipo IN (SELECT DISTINCT id FROM pe.e_tipopratica WHERE anagr_trib = 1) ";
//                        $arr_filter_pc[] = "avvioproc.pratica IN (SELECT DISTINCT pratica FROM pe.soggetti WHERE esecutore=1) ";
			break;
		case 2:
			$arr_filter_dia[]=($anno > 0)?("date_part('year',avvioproc.data_presentazione)=$anno"):("date_part('year',avvioproc.data_presentazione)>=1950 ");
			$arr_filter_dia[]="avvioproc.tipo IN (SELECT DISTINCT id FROM pe.e_tipopratica WHERE anagr_trib = 2) ";
//                        $arr_filter_dia[] = "avvioproc.pratica IN (SELECT DISTINCT pratica FROM pe.soggetti WHERE esecutore=1) ";
			break;
		default:
			break;
	}
	$filter_pc=implode(" AND ",$arr_filter_pc);
	if($filter_pc) $filter_pc = "AND $filter_pc";
	$filter_dia=implode(" AND ",$arr_filter_dia);
	if($filter_dia) $filter_dia = "AND $filter_dia";
	//if (!$_SESSION["tot_pagine"]){
	$sql_dia="SELECT DISTINCT avvioproc.id FROM pe.infodia right join pe.avvioproc on (infodia.pratica=avvioproc.pratica) WHERE coalesce(diniego,0)=0 $filter_dia ";
	$sql_pc="SELECT DISTINCT avvioproc.id FROM pe.titolo left join pe.avvioproc on (titolo.pratica=avvioproc.pratica) left join pe.lavori on (lavori.pratica=avvioproc.pratica) WHERE NOT data_rilascio IS NULL $filter_pc ";
	$sql=($tipo==0)?("($sql_dia) UNION ($sql_pc)"):(($tipo==1)?($sql_pc):($sql_dia));
	//if($_SESSION["USER_ID"]<4) echo "<p>$sql</p>";
	if(!$db->sql_query($sql)) $aa=1;
	$tota=$db->sql_fetchlist("id");
	$totali_pr=count($tota);
	$sql_dia="(SELECT DISTINCT avvioproc.id,avvioproc.pratica,avvioproc.numero,avvioproc.data_presentazione FROM pe.infodia right join pe.avvioproc on (infodia.pratica=avvioproc.pratica) WHERE coalesce(diniego,0)=0  $filter_dia order by data_presentazione,id)";
	$sql_pc="(SELECT DISTINCT avvioproc.id,avvioproc.pratica,avvioproc.numero,avvioproc.data_presentazione FROM pe.titolo left join pe.avvioproc on (titolo.pratica=avvioproc.pratica) left join pe.lavori on (lavori.pratica=avvioproc.pratica) WHERE NOT data_rilascio IS NULL $filter_pc order by data_presentazione,id)";
	if ($generaFile == 1 or $generaFile == 2){
		$sql=($tipo==0)?("(($sql_dia) UNION ($sql_pc)) ORDER BY id;"):(($tipo==1)?($sql_pc):($sql_dia));
	}
	else{
		$sql=($tipo==0)?("(($sql_dia) UNION ($sql_pc)) ORDER BY id LIMIT $limit OFFSET ".($offset*$limit).";"):(($tipo==1)?($sql_pc):($sql_dia));
	}
	

	if($_SESSION["USER_ID"]<4) echo "<p>$sql</p>";
        
	if(!$db->sql_query($sql)) $aa=1;//print_debug($sql);
	
	$ris=$db->sql_fetchrowset();
	$err=0;
	passthru("rm ".STAMPE."anagrafe_tributaria.txt");
	
	// TROVO INFO SUL RECORD DI TESTA
	$sql="SELECT * FROM anagrafe_tributaria.find_testa($anno);";
	
	if (!$db->sql_query($sql)) $aa=1;
	$testa=$db->sql_fetchrowset();
	$testa=implode("",$testa[0]);
	// SCRITTURA DEL RECORD DI TESTA
	$handle=fopen(STAMPE."anagrafe_tributaria.txt",'a+');
	if(!$handle) echo "Impossibile aprire il file ".$dir."anagrafe_tributaria";
	fwrite($handle,$testa);
	fclose($handle);

	for($i=0;$i<count($ris);$i++){		//CICLO SU TUUTE LE PRATICHE TROVATE
		list($id,$pratica,$num_pr,$data_pres)=array_values($ris[$i]);
		$sql="SELECT * FROM anagrafe_tributaria.e_record order by ordine;";
		if (!$db->sql_query($sql)) $aa=1;
		$rec=$db->sql_fetchrowset();
		
		foreach($rec as $v){
			$sql="SELECT * FROM anagrafe_tributaria.e_tipi_record WHERE record='".$v["tipo"]."' order by ordine;";
			
			if (!$db->sql_query($sql)) $aa=1;
			$fld_int=$db->sql_fetchrowset();
			foreach($fld_int as $el){
				$intestazioni[$v["nome"]][$el["nome"]]=Array("visibile"=>$el["visibile"],"label"=>$el["label"],"tipo_dato"=>"","validazione"=>$el["tipo_validazione"],"active_form"=>$el["active_form"]);  //Da sostituire $el["nome"] con $el["label"] quando le avrÃ² messe
			}
			$fld=implode(",",array_keys($intestazioni[$v["nome"]]));
			$arr_sql[$v["nome"]]="SELECT $fld FROM anagrafe_tributaria.".$v["funzione"]."($pratica);";
                        
		}		
		foreach($arr_sql as $key=>$sql){
			if (!$db->sql_query($sql)) $aa=1;
			//if($_SESSION["USER_ID"]<5) echo "<p>$sql</p>";
			$r[$key]=$db->sql_fetchrowset();
		}
		$p=valida_recordset($r,$intestazioni,$pratica);
		list($html_code,$errore)=array_values($p);
		if($errore && !$generaFile){
			$result[]="<tr><td class=\"pratica\"><a class=\"pratica\" href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pratica','Praticaweb',0,0,'yes')\">".(($limit*$offset)+($i+1)).") Pratica nÂ° $num_pr del $data_pres</a></td></tr><tr><td width=\"100%\">$html_code</td></tr>";
			$num_err++;
		}
		elseif($generaFile==1){
			$a = scrivi_file($r);
			//print "<p>$i : $a</p>";
		}
		elseif($genefaFile == 2 && !$errore){
			$a = scrivi_file($r);
		}
		$r=Array();
		//if ($a < 0) echo "Errore nella scrittura del file";
		//echo "<p>Scrittura record ".(string)$i." con risultato $a<p>";
	}
	$sql="SELECT * FROM anagrafe_tributaria.find_coda($anno);";	// TROVO INFO SUL RECORD DI CODA
	if (!$db->sql_query($sql)) $aa=1;//print_debug($sql);
	$coda=$db->sql_fetchrowset();
	$coda=implode("",$coda[0]);
	// SCRITTURA DEL RECORD DI CODA
	$handle=fopen(STAMPE."anagrafe_tributaria.txt",'a+');
	if(!$handle) echo "Impossibile aprire il file ".$dir."ana_trib";
	fwrite($handle,$coda);
	fclose($handle);
	
	
	//$resu=compara_file(STAMPE."anagrafe_tributaria.txt",STAMPE."prova");

	
	$str_errore="<p style=\"color:red;font-size:14px;\">Attenzione, correggere le pratiche errate.<br>Pratiche trovate: $totali_pr.<br>Pratiche errate nella pagina: $num_err.</p>";
	//if($err) echo "<p>Impossibile scrivere sul file ".STAMPE_DIR."ana_trib.txt</p>$h";
}
$pagineTot = ceil($totali_pr/$limit);
$next = $offset+1;
$prev = $offset-1;
$disableNext = ($next > $pagineTot)?("disabled"):("");
$disablePrev = ($prev < 0)?("disabled"):("");

for($i=0;$i<$pagineTot;$i++){
	$selected=(($offset)==$i)?("selected"):("");
	$opt[]="<option value=\"$i\" $selected>".($i+1)."</option>";
}
$options=implode("\t\t\t\n",$opt);
$form_nav = <<<EOT
<form name="frm_nav" id="frm_nav" method="POST" action="anag_trib.ricerca.php">
<div> Pagina $next di $pagineTot</div>
	<input type="button" class="hexfield" value="Torna alla ricerca" style="width:150px !important;margin-top:10px;margin-left:10px;" onclick="javascript:window.location='anag_trib.ricerca.php?mode=view'">
	<input type="button" class="hexfield" value="Vai alla Pagina" style="width:150px !important;margin-top:10px;margin-left:10px;" data-plugin="paginate">
	<select name="pagina" id="pagina" class="textbox">
		$options
	</select>
	<input type="button" class="hexfield" value="Genera File senza errori" style="width:175px !important;margin-top:10px;margin-left:10px;" data-plugin="generate" data-value="2">
	<input type="button" class="hexfield" value="Genera File" style="width:100px !important;margin-top:10px;margin-left:10px;" data-plugin="generate" data-value="1">
	<input type="hidden" value="" name="offset" id="offset">
	<input type="hidden" value="$limit" name="limit" id="limit">
	<input type="hidden" value="$anno" name="anno" id="offset">
	<input type="hidden" value="$tipo_pratica" name="tipo_pratica" id="offset">
	<input type="hidden" value="$totali_pr" name="totali_pr" id="totali_pr">
	<input type="hidden" value="" name="generafile" id="generafile">
</form>
EOT;
?>
<HTML>
<HEAD>
    <TITLE><?=$titolo?></TITLE>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
	utils::loadJS();
	utils::loadCss(Array('anagrafe'));
    ?>		

</HEAD>
<BODY >
<?include "./inc/inc.page_header.php";?>
<script language="javascript">
	$(document).ready(function(){
		$("[data-plugin='paginate']").each(function(k,v){
			$(v).bind('click',function(event){
				event.preventDefault();
				var val = $("#pagina").val();
				$("#offset").val(val);
				$("#message_box").html("<h4>Attendere prego.Elaborazione in corso.</h4>");
				$("#message_box").dialog({"position":{ my: "center", at: "top", of: window }});
				$("#frm_nav").submit();
			});
		$("[data-plugin='generate']").each(function(k,v){
			$(v).bind('click',function(event){
				event.preventDefault();
				var v = $(this).attr('data-value');
				if (confirm('Attenzione la generazione del file potrebbe impiegare alcuni minuti. Vuoi continuare?' + v)) {
                    $("#generafile").val(v);
					$("#message_box").html("<h4>Attendere prego.Generazione del file in corso</h4>");
					$("#message_box").dialog({"position":{ my: "center", at: "top", of: window }});
					$("#frm_nav").submit();
                }
				
			});
		});
		});
	});
</script>
<br>
<?if($mode=="view"){			//MODALITA' DI RICERCA?>	
<form name="frm_ricerca" id="ricerca" method="POST" action = "anag_trib.ricerca.php">
<table width="80%" class="stiletabella">
	<tr>
		<td width="50%">
			<table width="99%" class="stiletabella">
				<tr>
					<td width="40%" bgColor="#728bb8">Tipo di Pratica</td>
					<td>
						<select style="<?=$sel_size?>" class="textbox" name="tipo_pratica">
							
							<option value="0">Tutte</option>
							<option value="1">Permessi di Costruire</option>
							<option value="2">D.I.A.</option>
						</select>
					</td>
				</tr>
			</table>
		</td>
		<td width="25%" valign="top">
			<table width="99%" class="stiletabella">
				<tr rowspan="2">
					<td bgColor="#728bb8">Anno</td>
					<td><?=$html["anno"];?></td>
				</tr>
			</table>
		</td>
		<td width="25%" valign="top">
			<table width="99%" class="stiletabella">
				<tr rowspan="2">
					<td bgColor="#728bb8">Record per pagina</td>
					<td>
						<SELECT name="limit" class="textbox">
							<option value="25">25</option>
							<option value="50">50</option>
							<option value="100" selected>100</option>
							<option value="200">200</option>
						</SELECT>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top:10px;"><hr>
		<input type="button" value="Chiudi" style="margin-right:40px;" class="hexfield" onclick="chiudi()">
		<input type="submit" value="Avvia Ricerca" name="azione" class="hexfield" style="width:120px;"></td>
	</tr>
	<input type="hidden" value="search" name="mode">
</table>
</form>
<?php
}
else{		//MODALITA DI VISTA RISULTATI
	if ($generaFile==1 || $generaFile == 2){
		echo "<p><a href=header.php target=\"new\">Download del file</a></p>\n";
		$btn=<<<EOT
	<input type="button" class="hexfield" value="Torna alla ricerca" style="width:150px !important;margin-top:10px;margin-left:10px;" onclick="javascript:window.location='anag_trib.ricerca.php?mode=view'">
EOT;
		echo $btn;
	}
	elseif($num_err){
		echo $str_errore;
		echo $form_nav;
	}
	elseif(!$totali_pr){
		echo "<p style=\"color:red\">Nessuna Pratica Trovata.<br>Se Permesso di Costruire Controllare la data di Rilascio Titolo, se D.I.A. controllare la data di inizio validitÃ </p>\n".$btn;
	}
	else {
		//echo "<p><a href=header.php target=\"new\">Download del file</a></p>\n";
		echo "<p style='margin-left:10px;'><b>Nessuna pratica con errore</b></p>";
		echo $form_nav;

		//echo "<a href=\"stampe/anagrafe_tributaria.txt\" target=\"new\">File da Salvare</a>";
	}
	echo "<div id=\"message_box\"></div>";
	echo "<table width=\"99%\">".implode("",$result)."</table>";
	
?>


<?php
}
?>
</BODY>
</HTML>
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
	//$html["limit"]="<select name=\"limit\" class=\"textbox\"  style=\"$sel_size\"><option value=\"-1\" selected>Seleziona ===></option>".$html["limit"]."</select>";
}
else{
	$offset=$_POST["offset"];
	$limit=$_POST["limit"];
	$anno=$_POST["anno"];
	$tipo=$_POST["tipo_pratica"];
	include_once "./lib/anagr_tributaria.php";
	
	

	switch($tipo){
		case 0:
			$arr_filter_pc[]=($anno > 0)?("anno_riferimento=$anno"):("anno_riferimento>=2005 ");
			$arr_filter_pc[]="tipo_richiesta=0";
			$arr_filter_dia[]=($anno > 0)?("anno_riferimento=$anno"):("anno_riferimento>=2005 ");
			$arr_filter_dia[]="tipo_richiesta=1";
			break;
		case 1:
			$arr_filter_pc[]=($anno > 0)?("anno_riferimento=$anno"):("anno_riferimento>=2005 ");
			$arr_filter_pc[]="tipo_richiesta=0";
			break;
		case 2:
			$arr_filter_dia[]=($anno > 0)?("anno_riferimento=$anno"):("anno_riferimento>=2005 ");
			$arr_filter_dia[]="tipo_richiesta=1";
			break;
		default:
			break;
	}
	$filter_pc=implode(" AND ",$arr_filter_pc);
	if($filter_pc) $filter_pc = "AND $filter_pc";
	$filter_dia=implode(" AND ",$arr_filter_dia);
	if($tipo==0) $filter_dia = "AND $filter_dia";
	//if (!$_SESSION["tot_pagine"]){
        $sql_dia="SELECT count(*) as tot FROM anagrafe_tributaria.pratiche WHERE $filter_dia ";
        $sql_pc="SELECT count(*) as tot FROM anagrafe_tributaria.pratiche WHERE $filter_pc ";
        $sql=($tipo==0)?($sql_dia." UNION ".$sql_pc):(($tipo==1)?($sql_pc):($sql_dia));
		//if($_SESSION["USER_ID"]<4) echo "<p>$sql</p>";
        if(!$db->sql_query($sql)) print_debug($sql);
        $tota=$db->sql_fetchlist("tot");
        for($k=0;$k<count($tota);$k++) $tot+=$tota[$k];
        //$tot=$db->sql_fetchfield("tot");
        $totali_pr=$tot;
	//	$resto=($tot%$limit);
	//	$_SESSION["tot_pagine"]=($resto==0)?($tot/$limit):(floor($tot/$limit)+1);
	//}
	//$sql="SELECT avvioproc.id,avvioproc.pratica,avvioproc.numero,avvioproc.data_presentazione FROM pe.titolo left join pe.avvioproc on (titolo.pratica=avvioproc.pratica) WHERE numero='027P006' LIMIT $limit OFFSET ".($offset*$limit).";";
	//$sql_dia="SELECT avvioproc.id,avvioproc.pratica,avvioproc.numero,avvioproc.data_presentazione FROM pe.infodia left join pe.avvioproc on (infodia.pratica=avvioproc.pratica) WHERE diniego=0 AND sospesa=0 $filter_dia LIMIT $limit OFFSET ".($offset*$limit).";";
	//$sql_pc="SELECT avvioproc.id,avvioproc.pratica,avvioproc.numero,avvioproc.data_presentazione FROM pe.titolo left join pe.avvioproc on (titolo.pratica=avvioproc.pratica) WHERE NOT data_rilascio IS NULL $filter_pc LIMIT $limit OFFSET ".($offset*$limit).";";
	$sql_dia="(SELECT pratica,protocollo as numero,data_presentazione FROM anagrafe_tributaria.pratiche WHERE $filter_dia order by data_presentazione,pratica)";
	$sql_pc="(SELECT pratica,protocollo as numero,data_presentazione FROM anagrafe_tributaria.pratiche WHERE $filter_pc order by data_presentazione,pratica)";
	$sql=($tipo==0)?($sql_dia." UNION ".$sql_pc):(($tipo==1)?($sql_pc):($sql_dia));
	if($_SESSION["USER_ID"]<2) echo "<p>$sql</p>";
        
	if(!$db->sql_query($sql)) print_debug($sql);
	
	$ris=$db->sql_fetchrowset();
	$err=0;
	//passthru("rm ".STAMPE."anagrafe_tributaria.txt");
	
	// TROVO INFO SUL RECORD DI TESTA
	$sql="SELECT * FROM anagrafe_tributaria.find_testa($anno);";
	
	if (!$db->sql_query($sql)) print_debug($sql);
	$testa=$db->sql_fetchrowset();
	$testa=implode("",$testa[0]);
	// SCRITTURA DEL RECORD DI TESTA
	$handle=fopen(STAMPE_DIR."anagrafe_tributaria.txt",'a+');
	if(!$handle) echo "Impossibile aprire il file ".$dir."anagrafe_tributaria";
	fwrite($handle,$testa);
	fclose($handle);
	
	for($i=0;$i<count($ris);$i++){		//CICLO SU TUUTE LE PRATICHE TROVATE
		list($pratica,$num_pr,$data_pres)=array_values($ris[$i]);
		$sql="SELECT * FROM anagrafe_tributaria.e_record order by ordine;";
		if (!$db->sql_query($sql)) print_debug($sql);
		$rec=$db->sql_fetchrowset();
		foreach($rec as $v){
			$sql="SELECT * FROM anagrafe_tributaria.e_tipi_record WHERE record='".$v["tipo"]."' order by ordine;";
			
			if (!$db->sql_query($sql)) print_debug($sql);
			$fld_int=$db->sql_fetchrowset();
			foreach($fld_int as $el){
				$intestazioni[$v["nome"]][$el["nome"]]=Array("visibile"=>$el["visibile"],"label"=>$el["label"],"tipo_dato"=>"","validazione"=>$el["tipo_validazione"],"active_form"=>$el["active_form"]);  //Da sostituire $el["nome"] con $el["label"] quando le avrÃ² messe
			}
			$fld=implode(",",array_keys($intestazioni[$v["nome"]]));
			$arr_sql[$v["nome"]]="SELECT $fld FROM anagrafe_tributaria.".$v["funzione"]."($pratica);";
                        
		}		
		foreach($arr_sql as $key=>$sql){
			if (!$db->sql_query($sql)) utils::debug(DEBUG_DIR."anagrafe.debug",$key."  ===> \n\t\t\t".$sql,'w+');
			//if($_SESSION["USER_ID"]<5) echo "<p>$sql</p>";
			$r[$key]=$db->sql_fetchrowset();
		}
		$p=valida_recordset($r,$intestazioni,$pratica);
		list($html_code,$errore)=array_values($p);
		if($errore){
			$result[]="<tr><td class=\"pratica\"><a class=\"pratica\" href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pratica','Praticaweb',0,0,'yes')\">".(($limit*$offset)+($i+1)).") Pratica n° $num_pr del $data_pres</a></td></tr><tr><td width=\"100%\">$html_code</td></tr>";
			$num_err++;
			scrivi_file($r);
		}
		else
			scrivi_file($r);
		$r=Array();
	}
	$sql="SELECT * FROM anagrafe_tributaria.find_coda($anno);";	// TROVO INFO SUL RECORD DI CODA
	if (!$db->sql_query($sql)) print_debug($sql);
	$coda=$db->sql_fetchrowset();
	$coda=implode("",$coda[0]);
	// SCRITTURA DEL RECORD DI CODA
	$handle=fopen(STAMPE_DIR."anagrafe_tributaria.txt",'a+');
	if(!$handle) echo "Impossibile aprire il file ".$dir."ana_trib";
	fwrite($handle,$coda);
	fclose($handle);
	
	
	$resu=compara_file(STAMPE_DIR."anagrafe_tributaria.txt",STAMPE_DIR."prova");

	$str_errore="<p style=\"color:red;font-size:14px;\">Attenzione, correggere le pratiche errate.<br>Pratiche trovate: $totali_pr.<br>Pratiche errate in questa pagina: $num_err.</p>";
	//if($err) echo "<p>Impossibile scrivere sul file ".STAMPE_DIR."ana_trib.txt</p>$h";
}

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
	function chiudi(){
		self.opener.focus();
		self.close();
	}
	function vai_a(){
		document.getElementById("mode").value='search';
		return true;
	}
	function valida(){
		var o=document.getElementById('offset');
		var t=document.getElementById('tipo');
		var at=document.getElementById('anno');
		
		if (o.options[o.selectedIndex].value >= 0 && t.options[t.selectedIndex].value>=0 && a.options[a.selectedIndex].value>=0)
			return true;
		else{
			alert('Inserisci tutti i valori.');
			return false;
		}
			
	}
</script>
<br>
<?if($mode=="view"){			//MODALITA' DI RICERCA?>	
<form name="frm_ricerca" id="ricerca" method="POST">
<table width="80%" class="stiletabella">
	<tr>
		<td width="50%">
			<table width="99%" class="stiletabella">
				<tr>
					<td width="40%" bgColor="#728bb8">Tipo di Pratica</td>
					<td>
						<select style="<?=$sel_size?>" class="textbox" name="tipo_pratica">
							<option value="-1" selected>Seleziona ===></option>
							<option value="0">Tutte</option>
							<option value="1">Permessi di Costruire</option>
							<option value="2">D.I.A.</option>
						</select>
					</td>
				</tr>
			</table>
		</td>
		<td width="50%" valign="top">
			<table width="99%" class="stiletabella">
				<tr rowspan="2">
					<td bgColor="#728bb8">Anno</td>
					<td><?=$html["anno"];?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top:10px;"><hr>
		<input type="button" value="Chiudi" style="margin-right:40px;" class="hexfield" onclick="chiudi()">
		<input type="submit" value="Avvia Ricerca" name="azione" class="hexfield" style="width:120px;" onclick="return valida();"></td>
	</tr>
	<input type="hidden" value="search" name="mode">
	<input type="hidden" value="0" name="offset">
</table>
</form>
<?php 
}
else{		//MODALITA DI VISTA RISULTATI
	$btn="<p><input type=\"button\" class=\"hexfield\" value=\"Indietro\" style=\"margin-top:10px;margin-left:10px;\" onclick=\"javascript:window.location='anag_trib.ricerca.php?mode=view'\"></p>";
	if($num_err){
		echo $str_errore;
		echo $btn;
		
	}
	elseif(!$totali_pr){
		echo "<p style=\"color:red\">Nessuna Pratica Trovata.<br>Se Permesso di Costruire Controllare la data di Rilascio Titolo, se D.I.A. controllare la data di inizio validitÃ </p>\n".$btn;
	}
	else {
		echo "<p><a href=header.php target=\"new\">Download del file</a></p>\n$btn";
		//echo "<a href=\"stampe/anagrafe_tributaria.txt\" target=\"new\">File da Salvare</a>";
	}
	
	echo "<table width=\"99%\">".implode("",$result)."</table>";
	for($i=0;$i<$_SESSION["tot_pagine"];$i++){
		if ($i==$offset)
			$sel="selected";
		else
			$sel="";
		$pag[]="<option value=\"$i\" $sel>".($i+1)."</option>";
	}
	
?>
<form name="frm_nav" id="frm_nav" method="POST" action="anag_trib.ricerca.php">
	<input type="hidden" value="view" name="mode" id="mode">
	<input type="hidden" value="<?=$totali_pr?>" name="totali_pr" id="totali_pr">
</form>

<?php
}
?>
</BODY>
</HTML>
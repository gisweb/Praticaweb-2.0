<?
/*Costruzione del Filtro di ricerca*/
if ($ricerca["data_ins"]["data"]) $arr_cond[]="tmsins<$tmsmax and tmsins>$tmsmin";
if ($ricerca["data_pres_in"]["data"]){
	if ($ricerca["data_pres_fi"]["data"]) $arr_cond[]="data_presentazione BETWEEN ".$ricerca["data_pres_in"]["data"]." AND ".$ricerca["data_pres_fi"]["data"];
	else
		$arr_cond[]="data_presentazione=".$ricerca["data_pres_in"]["data"];
}
if ($ricerca["tipo_pratica"]) {
	if ($ricerca["tipo_pratica"]==1)
		$arr_cond[]="tipo BETWEEN 11000 AND 11999";
	elseif($ricerca["tipo_pratica"]==2)
		$arr_cond[]="tipo BETWEEN 10000 AND 10999";
	elseif($ricerca["tipo_pratica"]>2)
		$arr_cond[]="tipo = ".$ricerca["tipo_pratica"]."";
}

if (is_array($arr_cond)) $cond="WHERE ".implode(" AND ",$arr_cond);

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
/*Ricerca dei valori*/
$sql="SELECT pratica,numero,data_presentazione FROM pe.avvioproc $cond order by data_presentazione,numero;";
//echo "<p>$sql</p>";
$db->sql_query($sql);
$risultato=$db->sql_fetchrowset();
//include "./lib/stati.class.php";

for($i=0;$i<count($risultato);$i++){
	
	$pratica=$risultato[$i]["pratica"];
	//$st=new Stati($pratica);
	//$st->get_stato();
	//$stato=$st->stato["descrizione"];
	$sql_ins="SELECT tmsins FROM pe.avvioproc WHERE pratica=$pratica;";
	$db->sql_query($sql_ins);
	$tms=$db->sql_fetchfield("tmsins");
	if ($tms) $data_ins[]=date("d/m/y",$tms);
	else
		$data_ins[]="non disponibile";
	/*Ricerca Destinazione d'uso*/
	$sql_dest_uso="SELECT destuso1,destuso2 FROM pe.progetto WHERE pratica=$pratica;";
	$db->sql_query($sql_dest_uso);
	$ris_destuso[]=$db->sql_fetchrowset();
	/*Ricerca info Richiedenti*/
	
	$sql_rich="SELECT app||' '||nominativo as nome FROM stp.richiedenti WHERE pratica=$pratica;";
	//echo "$sql_rich<br>";
	$db->sql_query($sql_rich);
	$ris_rich[]=$db->sql_fetchrowset();
	//print_r($ris_rich);
	/*Ricerca info ubicazione + mappali*/
	$sql_ubi="SELECT distinct indirizzo FROM stp.ubicazione WHERE pratica=$pratica;";
	$sql_cterr="SELECT DISTINCT * FROM stp.lista_catasto_terreni($pratica);";
	//$sql_curb="SELECT * FROM stp.lista_catasto_urbano($pratica);";
	
	$db->sql_query($sql_ubi);
	$ris_ubi[]=$db->sql_fetchrowset();
	$db->sql_query($sql_cterr);
	$ris_cterr[]=$db->sql_fetchrowset();
	/*$db->sql_query($sql_curb);
	$ris_curb[]=$db->sql_fetchrowset();*/
	/* INFO SU LAVORI*/
	//$sql_lavori="SELECT coalesce(il::varchar,'')||'<span style=\"color:red\"> Scadenza: &nbsp;'||coalesce(scade_il::varchar,'')||'</span>' as il,coalesce(fl::varchar,'')||'<span style=\"color:red\"> Scadenza: &nbsp;'||coalesce(scade_fl::varchar,'')||'</span>' as fl from pe.lavori where pratica=$pratica";
	$sql_lavori="SELECT coalesce(il::varchar,'') as il,coalesce(fl::varchar,'') as fl from pe.lavori where pratica=$pratica";
	if(!$db->sql_query($sql_lavori))
		print_debug($sql_lavori);
	print_debug($sql_lavori);
	$risultato[$i]["in_lav"]=$db->sql_fetchfield('il');
	$risultato[$i]["fi_lav"]=$db->sql_fetchfield('fl');
	/*Ricerca info su pare Comm. Edilizia*/
	$sql_titolo="SELECT data_rilascio FROM pe.titolo WHERE pratica=$pratica order by data_rilascio;";
	//echo "$sql_pareri<br>";
	$db->sql_query($sql_titolo);
	$ris_titolo[]=$db->sql_fetchrowset();
}


?>

<?
include_once "./login.php";
//Converte i Caratteri accentati
function conv($str){
	/*$str=str_replace("Ã ","a'",$str);
	$str=str_replace("Ãš","e'",$str);
	$str=str_replace("Ã¬","i'",$str);
	$str=str_replace("Ã²","o'",$str);
	$str=str_replace("Ã¹","u'",$str);*/
	return $str;
}

// FUNZIONE CHE ESTRAE I TAG DA UNA STRINGA E RESTITUISCE IL CONTENUTO IN FORMA DI ARRAY
function get_tag($str,$tag_in,$tag_fi,$mode=0){
	$offset=0;
	$flag=true;
	$out=array();
	while ($flag){
		$start=strpos($str,$tag_in,$offset);
		$end=strpos($str,$tag_fi,$offset);
		if ($offset!==$end && ($start || $start===0) ) {
			$out[]=substr($str,$start+2,$end-$start-2);
			$offset=$end+1;
		}
		else
			$flag=$false;
	}
	if ($tag_in=="<*" or $tag_in=="<?")	{
		if ($out) foreach($out as $p) {
			if ($mode==0){
				$p=ereg_replace("[{}]{1}[a-zA-Z0-9{} \\]*[ ]{1}","",$p);
				$tmp[]=ereg_replace("[^a-zA-Z0-9_.]","",$p);
			}
			else
				$tmp[]=$p;
		}
		$out=$tmp;		
	}
	return $out;
}
// FUNZIONE CHE RESTITUISCE LA DIMENSIONE MAX DI UNA MATRICE BIDIMENSIONALE
function max_dim($tab,$flds){
	$max=0;
	//foreach($flds as $val) if (count($tab["$val"])>$max) $max=count($tab["$val"]);
	for ($i=0;$i<count($flds);$i++) if (count($tab[$flds[$i]])>$max) $max=count($tab[$flds[$i]]);
	return $max;
}
function insert_char($str,$pos,$i){
	$str=strtoupper($str);
	if ($pos==="fine"){
		switch ($str) {
			case "NUOVO_PARAGRAFO" :
				return "\par ";
				break;
			case "A_CAPO" :
				return "\line ";
				break;
			case "VIRGOLA" :
				return ", ";
				break;
			case "PUNTO_E_VIRGOLA" :
				return "; ";
				break;
			case "DUE_PUNTI" :
				return ": ";
				break;
			case "TRATTINO" :
				return " - ";
				break;
			case "SPAZIO" :
				return chr(32);
				break;
			case "LISTA" :
			case "LISTA_NUMERATA" :
				return "\line".chr(13);
				break;
			default :
				return "";
				break;
		}
	}
	else{
		switch ($str) {
			case "LISTA_NUMERATA" :
				$i=$i+1;
				return "$i) ";
				break;
			case "LISTA" :
				return " - ";
				break;
			default :
				return "";
				break;
		}
	}
}
// FUNZIONE CHE CICLA SUL NÂ° MAX DI ELEMENTI NE DB E COSTRUISCE UNA STRINGA CON N-OCCORRENZE
function sostituisci_campi($tags,$ris){
	$out="";
	$end=get_tag($tags,"</","/>");				// TAG del delimitatore della lista di campi
	$field=get_tag($tags,"<*","*>",1);			// TAG dei campi
	$field_obbl=get_tag($tags,"<?","?>",1);		// TAG dei campi obbligatori
	$n_cicli_field=max_dim($ris,$field);				//Serve per cercare il max nÂ° elementi nelle relazione uno a molti
	$n_cicli_field_obbl=max_dim($ris,$field_obbl);			//Serve per cercare il max nÂ° elementi nelle relazione uno a molti
	if ($n_cicli_field>=$n_cicli_field_obbl) $n_cicli=$n_cicli_field;
	else
		$n_cicli=$n_cicli_field_obbl;
	for($i=0;$i<$n_cicli;$i++){
		$out.=insert_char($end[0],"inizio",$i);
		$tmp=$tags;
		for($j=0;$j<count($field);$j++){
			//$fld=$field[$j];
			//TEST per togliere caratteri di formattazione
				$fld=ereg_replace("[{}]{1}[a-zA-Z0-9{} \\]*[ ]{1}","",$field[$j]);
				$fld=ereg_replace("[^a-zA-Z0-9_.]","",$fld);
				$tmp=str_replace("<*".$field[$j]."*>",$ris["$fld"][$i],$tmp);
			//FINE TEST
			
			//$tmp=str_replace("<*".$fld."*>",$ris["$fld"][$i],$tmp);
		}
		$flag=1;
		for($j=0;$j<count($field_obbl);$j++){
			$fld=$field_obbl[$j];
			if (!$ris["$fld"][$i]) $flag=0;
			$tmp=str_replace("<?".$fld."?>",$ris["$fld"][$i],$tmp);
		}
		if ($flag){
			$out.=$tmp;
			$end_char=insert_char($end[0],"fine",$i);
			$out.=$end_char;
		}
	}
		$out=substr($out,0,-strlen($end_char));
	return $out;
}
// FUNZIONE CHE RESTITUISCE I RISULTATI DELLE QUERY DI SELEZIONE
function crea_tabella($matr,$pr,$schema,$err){
	include_once "login.php";
	$result=Array();
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database $dbtype");
	
	$tab_name=@array_keys($schema);			//ARRAY CON I NOMI DELLE VISTE A DISPOSIZONE PER LE STAMPE

	foreach($matr as $key=>$val){
		$tmp="";
		if (@in_array($key,$tab_name)){		//CONTROLLO CHE LA VISTA  ESISTA NELLO SCHEMA STP
			foreach($val as $v) {
				if (in_array($v,$schema[$key]))		//CONROLLO CHE IL CAMPO ESISTA NELLA VISTA
					$tmp.=$v.",";
				else{
					$err[0][]="campo $v non esistente nella vista $key";
					//echo "campo $v non esistente nella vista $key<br>";
				}
			}
			$tmp=substr($tmp,0,strlen($tmp)-1);
			$sql="SELECT $tmp FROM stp.$key WHERE pratica=$pr";	//Eseguo la query di selezione dei campi del modello
			if (!$db->sql_query($sql)) {
				//exit("ERRORE nella Query $sql");
				//echo "Errore nella creazione del documento ! Controlla i tag $key<br>$sql<br>";
			}
			else{
				$result[$key] = $db->sql_fetchrowset();
				//Ciclo sui campi delle tabelle
				foreach($val as $v) {
					$s=$key.".".$v;
					$risultato[$s]=array();
					//Ciclo sui valori dei campi
					if (count($result[$key])>0)	foreach($result[$key] as $k=>$p)	array_push($risultato[$s],$p[$v]);
				}
			}
		}
		elseif(substr($key,0,8)=="funzione"){
			foreach($val as $v) {//Ciclo sui nomi delle funzioni
				$sql="SELECT * FROM stp.".$v."($pr);";
				//echo "<br>$sql<br>";
				if (!$db->sql_query($sql)) {
					//echo "Errore nella creazione del documento ! Controlla i tag $key<br>$sql<br>";
				}
				else{
					$s=$key.".".$v;
					$result[$s] = $db->sql_fetchrowset();
					$risultato[$s]=array();
					//Ciclo sui valori dei campi
					if (count($result[$s])>0)	foreach($result[$s] as $k=>$p)	array_push($risultato[$s],$p[$v]);
				}
			}
		}
		elseif (substr($key,0,4)=="data"){
			$risultato["data.data"][0]=date("d-m-Y");
		}
		else{
			//echo $key."<br>";
			$err[0][]="Vista $key non esistente";
			//echo "Vista $key non esistente<br>";
		}
	}
	if ($err) $risultato["errori"]=$err[0];
	return $risultato;
}

$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database $dbtype");

//---------------------------------------------  ACQUISISCO IL FILE ---------------------------------------------------
$file=MODELLI.$modello;
$outfile=STAMPE.$nome_file;
$handle = fopen($file, "r");
$text = fread($handle, filesize($file));
fclose($handle);
//---------------------------------------------  RIMUOVO I CARATTERI DI CARRIAGE RETURN E LINE FEED  --------------------------------------------
$text=str_replace(chr(13),"",$text);
$text=str_replace(chr(10),"",$text);
//---------------------------------------------  ACQUISISCO I TAG DEI CAMPI DA SOSTITUIRE ---------------------------------------------------
$cicli=get_tag($text,"<#","#>");		// TAG dei cicli
$campi=get_tag($text,"<*","*>");		// TAG dei campi
$finali=get_tag($text,"</","/>");		// TAG dei caratteri terminali
$campi_obbl=get_tag($text,"<?","?>");	// TAG dei campi obbligatori

//---------------------------------------------  COSTRUISCO LA MATRICE CON I NOMI DELLE VISTE E DEI CAMPI  DELLO SCHEMA STP  ---------------
$sql="SELECT viewname FROM pg_catalog.pg_views WHERE schemaname='stp';";
$db->sql_query($sql);
$stp_view=$db->sql_fetchrowset();
foreach($stp_view as $n){
	$name=$n["viewname"];
	$sql="SELECT column_name FROM information_schema.columns WHERE table_name='$name' and table_schema='stp';";
	//echo "$sql<br>";
	$db->sql_query($sql);
	$tmp=$db->sql_fetchrowset();
	if ($tmp)  foreach ($tmp as $v) $stp[$name][]=$v["column_name"];
	
}
//---------------------------------------------  COSTRUISCO LA MATRICE CON I RISULTATI DELLE QUERY ---------------------------------------------------
$ris=Array();
if ($campi){
	for($i=0;$i<count($campi);$i++){
		list($tab,$campo)=explode(".",$campi[$i]);
		if (!array_key_exists($tab,$ris)) $ris[$tab]=Array();
		if (!in_array($campo,$ris[$tab])) $ris[$tab][]=trim($campo);

	}
	for($i=0;$i<count($campi_obbl);$i++){
		list($tab_obbl,$campo_obbl)=explode(".",$campi_obbl[$i]);
		if (!array_key_exists($tab_obbl,$ris)) $ris[$tab_obbl]=Array();
		if (!in_array($campo_obbl,$ris[$tab_obbl])) $ris[$tab_obbl][]=trim($campo_obbl);
	}
	$tabella=crea_tabella($ris,$idpratica,$stp,$tab_err);
	//echo "<pre>";print_r($tabella);
	$tab_err[1]=$tabella["errori"];
	$tmp=$text;
	//---------------------------------------------  SOSTITUISCO AL TESTO DEL MODELLO I CAMPI UNO A MOLTI ---------------------------------------------------
	print_debug($tabella);
	if ($cicli) foreach($cicli as $txt){
		$out=sostituisci_campi($txt,$tabella);
		//$out=conv($out);
		//$tmp=str_replace("<#".$txt."#>",utf8_decode($out),$tmp);//utf8_encode(html_entity_decode($pippo)) // TAG della lista di campi
		$tmp=str_replace("<#".$txt."#>",$out,$tmp); print_debug($txt,null,"rtf");
	}
	//---------------------------------------------  SOSTITUISCO I AL TESTO DEL MODELLO I CAMPI UNO A UNO ---------------------------------------------------
	if ($campi) foreach($campi as $txt) {
		$aux=conv($tabella["$txt"][0]);
		//$tmp=str_replace("<*".$txt."*>",utf8_decode($aux),$tmp);
		$tmp=str_replace("<*".$txt."*>",$aux,$tmp);
	}
	//---------------------------------------------  SOSTITUISCO I AL TESTO DEL MODELLO I CARATTERI TERMINALI ---------------------------------------------------
	if ($finali) foreach($finali as $txt) $tmp=str_replace("</".$txt."/>","",$tmp);

	//---------------------------------------------  SCRIVO  IL NUOVO FILE ---------------------------------------------------
	if (file_exists($outfile)) @unlink($outfile);
	$handle = fopen($outfile, "x+");
	fwrite($handle,$tmp);
	fclose($handle);
}


?>

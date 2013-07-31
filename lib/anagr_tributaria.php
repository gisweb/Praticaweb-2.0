<?
$err_CF_PI=Array(
	"1"=>"La lunghezza del codice fiscale non &egrave;\ncorretta: il codice fiscale dovrebbe essere lungo\nesattamente 16 caratteri.",
	"2"=>"Il codice fiscale contiene dei caratteri non validi:\ni soli caratteri validi sono le lettere e le cifre.",
	"3"=>"Il codice fiscale non &egrave; corretto:\nil codice di controllo non corrisponde.",
	"4"=>"La lunghezza della partita IVA non &egrave;\ncorretta: la partita IVA dovrebbe essere lunga\nesattamente 11 caratteri.\n",
	"5"=>"La partita IVA contiene dei caratteri non ammessi:\nla partita IVA dovrebbe contenere solo cifre.\n",
	"6"=>"La partita IVA non &egrave; valida:\nil codice di controllo non corrisponde."
);

function checkdata($data){	
	if(!ereg("^[0-9]{8}$", $data)){
		return false;
	}
	else{
		//$arrayData = explode("/", $data);
		$Giorno = substr($data,0,2);
		$Mese = substr($data,2,2);
		$Anno = substr($data,4,4);
		if(!checkdate($Mese, $Giorno, $Anno)){
			return false;
		}
		else{
			return true;
		}	
	}
}
function controllaCF($cf)
{ 
    $cf=trim($cf);
    if( $cf == '' )  return '';
    if( strlen($cf) != 16 )
        return 1;
    $cf = strtoupper($cf);
    if( ! ereg("^[A-Z0-9]+$", $cf) ){
        return 2;
    }
    $s = 0;
    for( $i = 1; $i <= 13; $i += 2 ){
        $c = $cf[$i];
        if( '0' <= $c && $c <= '9' )
            $s += ord($c) - ord('0');
        else
            $s += ord($c) - ord('A');
    }
    for( $i = 0; $i <= 14; $i += 2 ){
        $c = $cf[$i];
        switch( $c ){
        case '0':  $s += 1;  break;
        case '1':  $s += 0;  break;
        case '2':  $s += 5;  break;
        case '3':  $s += 7;  break;
        case '4':  $s += 9;  break;
        case '5':  $s += 13;  break;
        case '6':  $s += 15;  break;
        case '7':  $s += 17;  break;
        case '8':  $s += 19;  break;
        case '9':  $s += 21;  break;
        case 'A':  $s += 1;  break;
        case 'B':  $s += 0;  break;
        case 'C':  $s += 5;  break;
        case 'D':  $s += 7;  break;
        case 'E':  $s += 9;  break;
        case 'F':  $s += 13;  break;
        case 'G':  $s += 15;  break;
        case 'H':  $s += 17;  break;
        case 'I':  $s += 19;  break;
        case 'J':  $s += 21;  break;
        case 'K':  $s += 2;  break;
        case 'L':  $s += 4;  break;
        case 'M':  $s += 18;  break;
        case 'N':  $s += 20;  break;
        case 'O':  $s += 11;  break;
        case 'P':  $s += 3;  break;
        case 'Q':  $s += 6;  break;
        case 'R':  $s += 8;  break;
        case 'S':  $s += 12;  break;
        case 'T':  $s += 14;  break;
        case 'U':  $s += 16;  break;
        case 'V':  $s += 10;  break;
        case 'W':  $s += 22;  break;
        case 'X':  $s += 25;  break;
        case 'Y':  $s += 24;  break;
        case 'Z':  $s += 23;  break;
        }
    }
    if( chr($s%26 + ord('A')) != $cf[15] )
        return 3;
    return 0;
}

function controllaPIVA($pi)
{
	$pi=trim($pi);
    if( $pi == '' )  return 4;
    if( strlen($pi) != 11 )
        return 4;
    if( ! ereg("^[0-9]+$", $pi) )
        return 5;
    $s = 0;
    for( $i = 0; $i <= 9; $i += 2 )
        $s += ord($pi[$i]) - ord('0');
    for( $i = 1; $i <= 9; $i += 2 ){
        $c = 2*( ord($pi[$i]) - ord('0') );
        if( $c > 9 )  $c = $c - 9;
        $s += $c;
    }
    if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') )
        return 6;
    return 0;
}


function valida_recordset($d,$intest,$pratica){
	$errore=0;
	foreach($d as $key=>$value){		//Ciclo sui tipi di Record
		foreach($intest[$key] as $val){
			if ($val["visibile"]) $cols++;
		}
		$html[]="<table class=\"record\">";
		$html[]=crea_intest($key,$intest[$key]);
		if (count($value))		// Controllo che il recordset non sia vuoto
			foreach($value as $v){		// Ciclo sugli elementi del recodset 
				foreach ($v as $k=>$val){		// Ciclo sui valori del record
					if ($intest[$key][$k]["visibile"])
						$r[$key][$k]=valida_dato($k,$val,$intest[$key][$k],$v,$key);
				}
				$ris=crea_riga($r[$key],$pratica);
				if($ris["err"]) $errore=1;
				$html[]=$ris["html_code"];
			}
		else{
			$errore=($key=="imprese")?($errore):(1);
			if($key!="imprese"){
				$color="color:red;";
				$mex="Record Obbligatorio.";
			}
			else{
				$color="";
				$mex="";
			}
			$html[]="<tr class=\"dato\"><td colspan=\"$cols\" class=\"dato\" style=\"font-size:12px;$color\"><b>$mex Nessun Dato Presente</b></td></tr>";
			
			
		}
		$html[]="</table>";
	}
	return Array("html_code"=>implode($html),"errore"=>$errore);
}
function crea_intest($tab,$campi){
	$caption="<caption class=\"titolo\">Record di $tab</caption>";
	
	foreach($campi as $key=>$val){
		if ($val["visibile"]) $td[]="<td class=\"intestazione\">".$val["label"]."</td>";
	}
	return $caption."<tr>".implode("",$td)."</tr>";
}
function crea_riga($arr,$pr){
	$err=0;
	foreach($arr as $key=>$val){
		if ($val["valido"]){
			$class="dato";
			$js="";
		}
		else{
			$err=1;
			$class="errore-anagrafe";
			$par=Array();
			foreach($val["param"] as $p) $par[]="&active_form_param[]=".$p;
			$param=(count($par))?(@implode("",$par)):("");
			$js="onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pr&active_form=".$val["active_form"]."$param','Praticaweb',0,0,'yes')\"";
		}
		
		$td[]="<td class=\"$class\" $js>".trim($val["valore"])."</td>";
	}
	return Array("html_code"=>"<tr class=\"dato\">".implode("",$td)."</tr>","err"=>$err);
}
function valida_dato($field,$value,$valid,$row,$tmp){
//echo "<pre>$tmp<br>";print_r($valid);echo "<br>";print_r($row);echo "</pre>";
	if ($tmp=="professionisti"){
		$ruolo="progettista";
		$id=$row["id_professionista"];
	}
	elseif($tmp=="imprese"){
		$ruolo="esecutore";
		$id=$row["id_impresa"];
	}
	else{
		$ruolo="richiedente";
		$id=($tmp=="beneficiari")?($row["id_beneficiario"]):($row["id_soggetto"]);			
	}
	
	switch($valid["validazione"]){
		case 1:		//OBBLIGATORIO
			
			if (!trim($value))
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			else
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			
			break;
		case 2:		//OBBLIGATORIO PERSONA FISICA
			if (!trim($row["denominazione"])){
				if (!trim($value))
					$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
				else
					$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			}
			else
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			break;
		case 3:		//OBBLIGATORIO PERSONA GIURIDICA
			if (trim($row["denominazione"])){
				if (!trim($value))
					$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
				else
					$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			}
			else
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			break;
		case 4:
			if (trim($value) && !(controllaCF(trim($value)) && controllaPIVA(trim($value))))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 5: 
			if (trim($value) && !(controllaCF($value) && controllaPIVA($value)))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else{
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"ext_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			}
			break;
		case 6:
			if($value=="M" || $value=="F" || trim($row["denominazione"]))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 7:
			if (checkdata($value))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 8:
			if (checkdata($value)||(trim($row["denominazione"])))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 18:
			if(ereg("^[A-Z]{1}[0-9]{3}$", $value)||(trim($row["denominazione"])))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 19:
			if(ereg("^[A-Z]{1}[0-9]{3}$", $value)||(!trim($row["denominazione"])))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);	
			break;
		case 9:
			if($value<=4 && $value>=1)
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 10:
			if($value<=2 && $value>=0)
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 11:
			if($value<=6 && $value>=1)
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			
			break;		
		case 12:
			if($value=="F" || $value=="T")
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			
			break;
		case 13:
			if($value<=7 && $value>=1)
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			
			break;
		case 14:
			if ($value && !(controllaCF($value) && controllaPIVA($value)))
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=$id","ruolo=$ruolo"),"id"=>$row["id_soggetto"],"pratica"=>$row["id_pratica"]);
			break;
		case 15:
			if($value && $value > 1900 && $value < 2100)
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			else
				$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"id"=>"","pratica"=>$row["id_pratica"]);
			break;
		case 20:
			if ($row["tipo_richiesta"]==1){
				if (!$value) $value="00000000";
				$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			}
			else{
				if(!($value && checkdata($value)))
					$out=Array("valido"=>0,"campo"=>$field,"valore"=>$value,"active_form"=>$valid["active_form"],"param"=>Array("id=".$row["id"]),"id"=>$row["id"],"pratica"=>$row["id_pratica"]);
				else
					$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			}
			break;
		default:
			$out=Array("valido"=>1,"campo"=>$field,"valore"=>$value);
			break;
	}
	return $out;
}

function scrivi_file($arr,$dir=STAMPE_DIR){
$sep_record=Array("testa"=>"Record Testa\n","richiesta"=>"Record Richiesta\n","beneficiari"=>"Record Beneficiari\n","dati_catastali"=>"Record Dati Catastali\n","professionisti"=>"Record Professionisti\n","imprese"=>"Record Imprese\n","coda"=>"Record Coda\n");
$app=Array("testa"=>Array("tipo_record","cod_ide_f","cod_num_f","cod_fiscale","cognome","nome","sesso","data_nascita","cod_cat_comune","denominazione","cod_cat_sede","anno_rif","filler","ctr_char","fine_riga"),"richiesta"=>Array("tipo_record","cod_fiscale","cognome","nome","sesso","data_nascita","cod_cat_comune","denominazione","cod_cat_sede","qualifica","tipo_richiesta","tipo_intervento","num_prot","tipologia_richiesta","data_presentazione","data_inizio_lavori","data_fine_lavori","indirizzo","filler","ctr_char","fine_riga"),"beneficiari"=>Array("tipo_record","cod_fiscale_rich","num_prot","cod_fiscale_bene","cognome","nome","sesso","data_nascita","cod_cat_comune","denominazione","cod_cat_sede","qualifica","filler","ctr_char","fine_riga"),"dati_catastali"=>Array("tipo_record","cod_fiscale_rich","num_prot","tipo_unita","sezione","foglio","particella","est_particella","tipo_particella","subalterno","filler","ctr_char","fine_riga"),"professionisti"=>Array("tipo_record","cod_fiscale_rich","num_prot","cod_fiscale_prof","albo","prov_albo","num_iscrizione","qualifica","filler","ctr_char","fine_riga"),"imprese"=>Array("tipo_record","cod_fiscale_rich","num_prot","piva_impresa","denominazione","cod_cat_sede","filler","ctr_char","fine_riga"),"coda"=>Array("tipo_record","cod_ide_f","cod_num_f","cod_fiscale","cognome","nome","sesso","data_nascita","cod_cat_comune","denominazione","cod_cat_sede","anno_rif","filler","ctr_char","fine_riga"));
//echo "<p><pre>";print_r ($arr);echo "</pre></p><hr>";
	$handle=fopen($dir."anagrafe_tributaria.txt",'a+');
	if(!$handle) echo "Impossibile aprire il file ".$dir."ana_trib";
	foreach($arr as $key=>$val){ //CICLO SUI TIPI DI RECORD (testa, richiesta, beneficiari, ecc)
		if ($val){
			$k=$app[$key];
			for ($i=0;$i<count($val);$i++){
					$v=$val[$i];
				for($j=0;$j<count($k);$j++){
					$tmp.=$v[$k[$j]];
				}
			}
		}
	}
	fwrite($handle,$tmp);
	fclose($handle);
	return $error;
}
function compara_file($filename1,$filename2) {
	if(!file_exists($filename1)){
		$msg="Il file ".basename($filename1)." non esiste.";
		return array("code"=>-1,"message"=>$msg);
	}
	elseif(!file_exists($filename2)){
		$msg= "Il file ".basename($filename2)." non esiste.";
		return array("code"=>-1,"message"=>$msg);
	}
	
	$a=filesize($filename1);
	$b=filesize($filename2);
	
	if($a!==$b){
		$msg="file di dimensioni diverse.$a -- $b";
		return array("code"=>-1,"message"=>$msg);
	}
	else{
		$msg="File di dimensioni uguali.";
		$string1=file_get_contents($filename1);
		$string2=file_get_contents($filename2);
		if($string1==$string2){
			$msg.="file uguali";
			return array("code"=>1,"message"=>$msg);
		}
		else{
			$msg.="file diversi";
			return array("code"=>-1,"message"=>$msg);
		}
	}
}
?>
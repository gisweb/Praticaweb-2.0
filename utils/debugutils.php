<?php
function print_debug($t="",$db=NULL,$file=NULL){

		if (!defined("DEBUG_DIR")) {
			define("DEBUG_DIR",'./');
		}
        $uid=($_SESSION["USER_ID"])?($_SESSION["USER_ID"]."_"):("");
        $data=date('j-m-y');
        $ora=date("H:i:s");
        if (!$file) $nomefile=DEBUG_DIR.$uid."standard.debug";
        else
            $nomefile=DEBUG_DIR.$uid.$file.".debug";
        $size=filesize($nomefile);

        $f=($size>1000000)?(fopen($nomefile,"w+")):(fopen($nomefile,"a+"));
        if (!$f) die("<p>Impossibile aprire il file $nomefile</p>");

        if (is_array($t)||is_object($t)){
            ob_start();
            print_r($t);
            $out=ob_get_contents ();
            ob_end_clean();
            if (!fwrite($f,"\n$data\t$ora\t --- STAMPA DI UN ARRAY ---\n\t$out")) echo "<p>Impossibile scrivere sul file $nomefile </p>";
            fclose($f);
        }
        else{
            if (!fwrite($f,"\n$data\t$ora\n\t".$t)) echo "<p>Impossibile scrivere sul file $nomefile </p>";
            else
                fclose($f);
        }

}

//FUNZIONE CHE CERCA RICORSIVAMENTE UN TESTO NEI FILE DI UNA DIRECTORY

function trova_testo($testo,$dirname){
	$ast=str_repeat("*",10);
	ob_start();
	echo "\n$ast\tRicerca di $testo nei File della Directory $dirname\t$ast\n";
	if ($dir = @opendir($dirname)) {
		while (($file = readdir($dir)) !== false) { 
			if (!is_dir($file)) {
				$filename=$dirname."/".$file;
				$f=fopen($filename,"r+");
				if ($f){
					$text=fread($f,filesize($filename));
					if (strpos(strtolower($text),$testo)) $ris[dirname($file)][]="Trovato in $file";
					fclose($f);
				}
				else
					trova_testo($testo,$dirname."/".$file);
			}
			elseif($file!="." and $file!=".."){
				trova_testo($testo,$dirname."/".$file);
			}
		}  
		closedir($dir);
	}
	else
		$ris[$dirname]="$dirname non � una directory";
	print_r($ris);
	echo "\n$ast$ast FINE RICERCA TESTO IN $dirname $ast$ast\n";
	$output=ob_get_contents();
	print_debug($output,"","trova_testo");
}
function exec_command($cmd){
	$ast=str_repeat("*",10);
	ob_start();
	system($cmd,$out);
	$ris=ob_get_contents();
	ob_end_clean();
	print_debug("$ast\t ESECUZIONE COMANDO $cmd con RETURN CODE $out\t$ast\n");
	//print_debug($arr);
	print_debug("$ast$ast\tRISULTATO EXEC\t$ast$ast\n$ris\n$ast$ast FINE ESECUZIONE COMANDO $ast$ast\n");
	
}
function print_array($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function adm_print_array($arr){
	if ($_SESSION["USER_ID"]==1){
		echo "<pre>";
        	print_r($arr);
	        echo "</pre>";
        }
}

function vnsprintf( $format, array $data)
{
    preg_match_all( '/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $offset = 0;
    $keys = array_keys($data);
    foreach ( $match as &$value )
    {
        if ( ( $key = array_search( $value[1][0], $keys) ) !== FALSE || ( is_numeric( $value[1][0]) && ( $key = array_search( (int)$value[1][0], $keys) ) !== FALSE ) ) {
            $len = strlen( $value[1][0]);
            $format = substr_replace( $format, 1 + $key, $offset + $value[1][1], $len);
            $offset -= $len - strlen( $key);
        }
    }
    return vsprintf( $format, $data);
}
?>

<?php
function getDirectory($path = '.', $ignore = '',$regexp_ignore=Array()) { 
	//echo $path."\n";
    $dirTree = array (); 
    $dirTreeTemp = array (); 
	
    $ignore[] = '.'; 
    $ignore[] = '..'; 

    $dh = @opendir($path); 

    while (false !== ($file = readdir($dh))) { 
        if (!in_array($file, $ignore)) { 
            if (!is_dir("$path/$file")) { 
                $reIgnore=0;
				//for($i=0;$i<count($regexp_ignore);$i++){
				if (preg_match($regexp_ignore[0], $file)){ 
					$reIgnore=1;
					//print "$regexp_ignore[0],$path ,$file - ".(string)preg_match($regexp_ignore[0], $file)."\n";
				}
				//} 
				
                if ($reIgnore==0) $dirTree["$path"][] = $file; 
                 
            } else { 
                 
                $dirTreeTemp = getDirectory("$path/$file", $ignore,$regexp_ignore); 
                if (is_array($dirTreeTemp))$dirTree = array_merge($dirTree, $dirTreeTemp); 
            } 
        } 
    } 
    closedir($dh); 
     
    return $dirTree; 
}

function getFileDirectory($path = '.', $ignore = '',$regexp='|(.+)|') { 
	//echo $path."\n";
    $dirTree = array (); 
    $dirTreeTemp = array (); 
	
    $ignore[] = '.'; 
    $ignore[] = '..'; 

    $dh = @opendir($path); 
	
    while (false !== ($file = readdir($dh))) { 
        if (!in_array($file, $ignore)) { 
            if (!is_dir("$path/$file")) { 
                $reIgnore=0;
				//for($i=0;$i<count($regexp_ignore);$i++){
				if(preg_match($regexp,$file)){
					
					$dirTree["$path"][] = $file; 
				}
                 
            } else { 
                 
                $dirTreeTemp = getFileDirectory("$path/$file", $ignore,$regexp); 
                if (is_array($dirTreeTemp))$dirTree = array_merge($dirTree, $dirTreeTemp); 
            } 
        } 
    } 
    closedir($dh); 
     
    return $dirTree; 
}


session_start();
$_SESSION['USER_ID']=1;

require_once '../login.php';
require_once '../lib/pratica.class.php';
$dbconn->sql_query("select case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end as numero,pratica from pe.avvioproc WHERE substring(numero from 1 for 1) <> '8' and not numero ilike '%/%/%'  and (case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end ilike '199_/%' or case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end ilike '200_/%' or numero ilike 'D%/%')order by 1");
$ris=$dbconn->sql_fetchrowset();
foreach($ris as $val){
	$pratica[$val['numero']]=$val['pratica'];
}
$anno="2004";
$baseDir=DATA_DIR.'praticaweb'.DIRECTORY_SEPARATOR.'documenti'.DIRECTORY_SEPARATOR.'concessioni'.DIRECTORY_SEPARATOR.$anno;
$ignore = array('Thumbs.db','pspbrwse.jbf','019.tif +.tif'); 
/*$fName="$baseDir/2005/168-2005/034.txt";
print "$fName\n";
print filesize($fName)."\n";
$f=fopen($fName,'r');
$text=fread($f,filesize($fName));
print utf8_decode($text);
return;*/
//$dirTree = getDirectory($baseDir, $ignore, Array('|(.+) 00[0-9].tif$|'));
$dirTree = getFileDirectory($baseDir, $ignore, '|(.+).txt$|');
$cont=0;
$tot=count($dirTree);

foreach ($dirTree  as $key=>$val){
	unset($numero);
	unset($numero_letto);
	//$cmd="cd $key";
	//system($cmd);
	$currDir=basename($key);
	$tmp=explode('-',$currDir);
	$numero=trim($tmp[1])."/".trim($tmp[0]);
	for($i=0;$i<count($val);$i++){ 
		$cont++;
		$info=  pathinfo($val[$i]);
		$ff=$info['filename'];

		$fin=$key.DIRECTORY_SEPARATOR.$val[$i];
		$f=fopen($fin,'r');
		$text=fread($f,filesize($fin));
		if (preg_match('|([0-9]{4})/([0-9]{4})([A-z]{0,2})[ \\n\\r]|',$text,$result)){
			$numero_letto=trim($result[0]);
			//print "$cont) di $tot - Pratica $currDir :  $numero_letto\n";
			//print strlen($numero_letto)." -- ".strlen($numero)."\n";
			if ($numero != $numero_letto){
				//print utf8_encode(trim($numero_letto))."  --  ".  utf8_encode(trim($numero))."\n";
				//print mb_detect_encoding($numero)."\n";
				//print mb_detect_encoding($numero_letto)."\n";
			}
			//print "\n";
		}
		else{
			//$cmd="tesseract $key".DIRECTORY_SEPARATOR.$ff.".tif $key".DIRECTORY_SEPARATOR.$ff." -l ita";
			//system($cmd);
			//print "$cont) di $tot - Pratica $currDir :  $numero Da directory\n";
		}
		if (in_array($numero,array_keys($pratica))){
			$res[$numero][]=Array("pratica"=>$pratica[$numero],"file"=>"$ff.tif","from"=>$key.DIRECTORY_SEPARATOR.$ff.".tif");
		}
		elseif(in_array($numero_letto,array_keys($pratica))){
			$res[$numero_letto][]=Array("pratica"=>$pratica[$numero_letto],"file"=>"$ff.tif");
		}
		$elencofile[]="$ff.tif";
		
		
			
	}
	
	/*list($numero,$anno)=explode('-',basename($key));
	$n="$anno/$numero";
	if (!in_array($n,$ris)) $pratiche[]=$n;*/
	//if (count($val)>1) $moreFile[]=Array($key=>$val); 
}

ksort($res);

foreach($res as $key=>$val){
	for($j=0;$j<count($val);$j++){
		$v=$val[$j];
		//print_r($v);
		switch(strtolower(basename($v['file']))){
			case "24":
			case "024":
			case "0024":
				$filename="Autorizzazione.tif";
				break;
			case "024r":
				$filename="Rinnovo_Autorizzazione.tif";
				break;
			case "026":
				$filename="Diniego.tif";
				break;
			case "034":
			case "0034":
			case "034-2":
				$filename="Concessione_in_Sanatoria.tif";
				break;
			case "035":
				$filename="Autorizzazione_in_Sanatoria.tif";
				break;
			case "019an":
				$filename="Annullamento_Concessione.tif";
				break;
			case "019v":
			case "019v+":
				$filename="Voltura_Concessione.tif";
				break;
			case "019r":
			case "019ri":
			case "019rin":
				$filename="Rinnovo_Concessione.tif";
				break;
			default:
				$filename="Concessione.tif";
				break;
				
		}
		$p=new pratica($v['pratica']);
		$testo="Caricato documento <a target=\"new\" href=\"".$p->url_documenti."$filename\">".basename($filename)."</a>";
		$sql="INSERT INTO pe.iter(pratica,nota,nota_edit) VALUES(".$v['pratica'].",'$testo','$testo');";
		if(!$dbconn->sql_query($sql)){
			echo $sql."\n";
		}
		else
			copy($v['from'],$p->documenti.$filename);
	}
}
//print_r($res);

//print_r(array_unique($elencofile));
return;
?>

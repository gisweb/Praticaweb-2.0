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

session_start();
$_SESSION['USER_ID']=1;

require_once '../login.php';

//$dbconn->sql_query("select case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end as numero from pe.avvioproc WHERE substring(numero from 1 for 1) <> '8' and not numero ilike '%/%/%'  and (case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end ilike '199_/%' or case when substring(numero from 1 for 1)='9' then '19'||numero else replace(upper(numero),'DIA','') end ilike '200_/%' or numero ilike 'D%/%')order by 1");
//$ris=$dbconn->sql_fetchlist('numero');
$arrAnni=Array("1999","2000","2001","2002","2003","2004","2005");
foreach($arrAnni as $anno){
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
	$dirTree = getDirectory($baseDir, $ignore, Array('|(.+).txt$|'));
	$cont=0;
	$tot=0;
	foreach ($dirTree  as $key=>$val){for($i=0;$i<count($val);$i++) $tot++;}
	foreach ($dirTree  as $key=>$val){
		//$cmd="cd $key";
		//system($cmd);
		$currDir=basename($key);
		for($i=0;$i<count($val);$i++){
			$tStart=time();
			$cont++;
			$info=  pathinfo($val[$i]);
			$ff=$info['filename'];
			$fin=$key.DIRECTORY_SEPARATOR.$val[$i];
			$fout=$key.DIRECTORY_SEPARATOR.$ff.".txt";
			
			system("convert $fin -colorspace Gray -depth 8 -resample 200x200 ".$ff."_1.tif");
			
			$cmd="cuneiform -o $fout ".$ff."_1.tif";
			ob_start();
			$result=system($cmd);
			$a=ob_end_clean();
	
			if(!file_exists($fout)){
				$cmd="tesseract $fin $key".DIRECTORY_SEPARATOR.$ff." -l ita";
				ob_start();
				$result=system($cmd);
				$a=ob_end_clean();
				if(!file_exists($fout)){
					$tEnd=time();
					$dt=$tEnd-$tStart;
					echo "$cont) di $tot in $dt s Errore nel comando $cmd\n";
					$err[]=$key;
				}
				else{
					$tEnd=time();
				$dt=$tEnd-$tStart;
					echo "$cont) di $tot in $dt s Pratica ".basename($key)." Processata con tesseract \n";
				}
			}
			else{
				$tEnd=time();
				$dt=$tEnd-$tStart;
				echo "$cont) di $tot in $dt s Pratica ".basename($key)." Processata con cuneiform\n";
			}
			
		}
		print_debug($err,null,"ERRORI_$anno");
		/*list($numero,$anno)=explode('-',basename($key));
		$n="$anno/$numero";
		if (!in_array($n,$ris)) $pratiche[]=$n;*/
		//if (count($val)>1) $moreFile[]=Array($key=>$val); 
	}
}
return;
sort($pratiche);
print_debug($pratiche,null,'DIRTREE');
print_debug($moreFile,null,'DIRTREEPLUS');
?>

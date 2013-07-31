<?php
	$content=array("Ciao","culo");
	$m=array("<-M1->","<-M2->");
	
	$playlist="../public/_modelli/".$_GET['file'];
	$fs = fopen ($playlist,'r');
	$line=array();
	$k=0;
	
	while (($buffer = fgets($fs, 200000))!= false)
							{
							$line[$k] = $buffer;
							$line[$k]=str_replace($m,$content,$line[$k]);
							$k++;
							}
			fclose ($fs);
			$time=mktime();
			$file="../public/_modelli/".$time.".rtf";//$_GET['file'];
			$f_open =fopen ($file,'w+');
			//echo count($line);
		
			for ($k=0;$k<count($line);$k++)
						{
						$line[$k]=$line[$k].'\n';
						echo 'Conto --> '.$k.' - '.$line[$k].'<br>';;
						fwrite($f_open,$line[$k]);
						}
	fclose ($f_open);

	
			
?>
<?
function gw_date_format($stringa_data){
	$sep=(strpos($stringa_data,'-'))?('-'):('/');
	if ($stringa_data){
		$ar=explode($sep,$stringa_data);
		$stringa_data=$ar[2]."-".$ar[1]."-".$ar[0];
	}
	return $stringa_data; 
}
?>

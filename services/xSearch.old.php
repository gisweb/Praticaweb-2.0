<?php
require_once '../login.php'	;

foreach($_REQUEST as $key=>$val){
	if(strpos($key,'mDataProp_')!==FALSE) {
		$idx=str_replace('mDataProp_','',$key);
		$aColumns[$idx]=$val;
	}
}

if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' ){
    $sLimit = "LIMIT ".pg_escape_string( $_REQUEST['iDisplayLength'] )." OFFSET ".pg_escape_string( $_REQUEST['iDisplayStart'] );
}
if ( isset( $_REQUEST['iSortCol_0'] ) ){
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ ){
		if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" ){
			$sOrder .= $aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]." ".pg_escape_string( $_REQUEST['sSortDir_'.$i] ) .", ";
		}
	}
	 
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == "ORDER BY" )
	{
		$sOrder = "";
	}
}

$flds=implode(",",$aColumns);
switch($_REQUEST['view']){
	case 'pratica':
		$sTable="pe.avvio_procedimento";
		
		break;
}
$sql="SELECT $flds FROM $sTable;";
$dbconn->sql_query($sql);
$iTotal=$dbconn->sql_numrows();
//$sql=trim("SELECT 'gradeA' as \"DT_RowClass\",$flds FROM $sTable $sWhere $sOrder $sLimit");
$sql=trim("SELECT $flds FROM $sTable $sWhere $sOrder $sLimit");
$dbconn->sql_query($sql);
$ris=$dbconn->sql_fetchrowset();

$iFilteredTotal=$iTotal;
$result = array(
	"sEcho" => intval($_REQUEST['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $ris
);
echo json_encode( $result );
//for($i=0;$i<count($ris);$i++){}

?>
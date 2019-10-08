<?php
require_once "../login.php";

$db=appUtils::getDB();

$action=(isset($_REQUEST['action']))?($_REQUEST['action']):('search');
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1)*$rows;
$order = isset($_POST['sort']) ? ("ORDER BY ".$_POST['sort']) : "";
$orderType = isset($_POST['order']) ? ($_POST['order']) : "";
$filter = "";
$ris = null;
$risall = null;

$usr=$_SESSION['USER_ID'];
$data=$_REQUEST['data'];
$op=($_REQUEST["op"])?($_REQUEST["op"]):'AND';
$queryOP=($op=='AND')?('INTERSECT'):('UNION');
$dtot=0;
$total=0;

// Costruzione dei filtri
foreach($data as $key=>$value){
	if (strpos($value[0], " AND ") == true ) $value[0] = str_replace(" AND ", " AND ".$key, $value[0]);
	$q[]=$key.implode(" $op ", $value);
};
$filter = implode(" AND ", $q);
if ($filter) $filter = " WHERE ".$filter;
		
switch($action){
    case "grouped":
		
		$sqlall="SELECT * from ragioneria.estrattoconto $filter";
		$sql=$sqlall." $order $orderType LIMIT $rows OFFSET $offset;";
		
		break;
	
	 case "provvisori":
		
		$sqlall="SELECT * from ragioneria.estrattocontoprovv as estrattoconto $filter";
		$sql=$sqlall." $order $orderType LIMIT $rows OFFSET $offset;";
		
		break;
		
	case "search":
		
		$sqlall="select * from (";
		$sqlall.="SELECT tipo, sum(sum) as sum, descrizione, modalita  from ragioneria.estrattoconto $filter";
		$sqlall.=" group by tipo, descrizione, modalita) as estrattoconto";
		$sql=$sqlall." $order $orderType LIMIT $rows OFFSET $offset;";
		
		break;
}
	$risall=$db->fetchAll($sqlall);
	$ris=$db->fetchAll($sql);
	
	//Conteggio count e totali
	$listId=Array();
	
	
	if ($risall) {
	for($i=0;$i<count($risall);$i++) {
			 $listId[]=$i;
			 $dtot = $dtot + $risall[$i]['sum'];
			}
	$total=count($listId);
	}
	
	//Costruzione del footer
	$sql="SELECT 'Totale' as descrizione, $dtot as sum ";
	$footer=$db->fetchAll($sql);
	
	

	$result=Array("total"=>$total,"rows"=>$ris,"filter"=>$filter,"sql"=>$sql,"elenco_id"=>$listId, "ret"=>$filterand, "footer"=>$footer);
		

header('Content-Type: application/json');
print json_encode($result);
return;

?>
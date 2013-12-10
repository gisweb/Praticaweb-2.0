<?php
require_once "../login.php";

$db=appUtils::getDB();
$tipo=$_REQUEST["ricerca"];
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1)*$rows;
$order = isset($_POST['sort']) ? ("ORDER BY ".$_POST['sort']) : "";
$orderType = isset($_POST['order']) ? ($_POST['order']) : "";
switch($tipo){
    case "civici":
        $sql="SELECT DISTINCT via,coalesce(civico,'') as civico, via||coalesce('-'||civico,'') as viacivico FROM pe.indirizzi WHERE coalesce(via,'')<>''";
        $ris=$db->fetchAll($sql);
        $total=count($ris);
        $sql="SELECT DISTINCT via,coalesce(civico,'') as civico, via||coalesce('-'||civico,'') as viacivico FROM pe.indirizzi WHERE coalesce(via,'')<>'' $order $orderType LIMIT $rows OFFSET $offset ";
        $ris=$db->fetchAll($sql);
        break;
    default:
        $sql="SELECT count(*) as total FROM stp.single_pratica;";
        $total=$db->fetchColumn($sql,Array(),0);
        $sql="SELECT * FROM stp.single_pratica $order $orderType LIMIT $rows OFFSET $offset ";
        $ris=$db->fetchAll($sql);
        
        break;
}
$result=Array('total'=>$total,'rows'=>$ris);
print json_encode($result);
?>
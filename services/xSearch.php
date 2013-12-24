<?php
include_once "../login.php";
$query=Array();
require_once APPS_DIR.'utils/searchQuery.php';
error_reporting(E_ERROR);



$action=(isset($_REQUEST['action']))?($_REQUEST['action']):('search');
$searchtype=$_REQUEST['searchType'];
$value=addslashes($_REQUEST['term']);
$usr=$_SESSION['USER_ID'];
$data=$_REQUEST['data'];
$op=($_REQUEST["op"])?($_REQUEST["op"]):'AND';
$queryOP=($op=='AND')?('INTERSECT'):('UNION');
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1)*$rows;
$order = isset($_POST['sort']) ? ("ORDER BY ".$_POST['sort']) : "ORDER BY data_prot";
$orderType = isset($_POST['order']) ? ($_POST['order']) : "DESC";
$groupBy=isset($_POST['field']) ? ($_POST['field']) : "civico";
$result=Array();
$db=appUtils::getDB();
switch($action){
    case "scadenze":
        utils::debug("scadenze",$query["scadenze"]);
        foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
            $f1=implode(" $op ",$value);
        }
        $listId=Array();
        $filter=implode(" $queryOP ",$q);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        $total=count($listId);
        $sql=sprintf($query["scadenze"],$f1,$filter,$order,$orderType,$rows,$offset);
        utils::debug("scadenze",$sql);
        $res=$db->fetchAll($sql);
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter,"elenco_id"=>$listId);
        
        break;
    case "group":
        foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
            $f1=implode(" $op ",$value);
        }
        $listId=Array();
        $filter=implode(" $queryOP ",$q);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        switch ($groupBy){
            case "particella-urbano":
                $searchQuery=$query["urbano"];
                break;
            case "particella-terreni":
                $searchQuery=$query["terreni"];
                break;
            default:
                $searchQuery=$query["civico"];
                break;
                
        }
        $sql=sprintf($searchQuery,$f1,$filter);

        utils::debug("search",$sql);
        $res=$db->fetchAll($sql);
        $result=appUtils::groupData($groupBy,$res);
        utils::debug("test",$result);
        break;
    default:
        foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
        }
        $listId=Array();
        $filter=implode(" $queryOP ",$q);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        $total=count($listId);
        $sql=sprintf($query["default"],$filter,$order,$orderType,$rows,$offset);
        utils::debug("search",$sql);
        $res=$db->fetchAll($sql);
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter,"elenco_id"=>$listId);
        break;
}
header('Content-Type: application/json');
print json_encode($result);
return;


?>
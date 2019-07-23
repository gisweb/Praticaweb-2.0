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
$order = isset($_POST['sort']) ? ("ORDER BY ".$_POST['sort']) : "ORDER BY data_presentazione";
$orderType = isset($_POST['order']) ? ($_POST['order']) : "DESC";
$groupBy=isset($_POST['field']) ? ($_POST['field']) : "civico";
$result=Array();
$db=appUtils::getDB();
switch($action){
    case "list-draw":
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $offset = ($page-1)*$rows;
        $order = isset($_POST['sort']) ? ("ORDER BY ".$_POST['sort']) : "ORDER BY data_sorteggio";
        $orderType = isset($_POST['order']) ? ($_POST['order']) : "DESC";
        $sql="SELECT count(*) as totali FROM pe.verifiche A INNER JOIN pe.avvioproc B USING(pratica) LEFT JOIN pe.e_verifiche E ON(A.tipo=E.id) INNER JOIN pe.e_tipopratica C ON (B.tipo=C.id) LEFT JOIN admin.users F ON (A.resp_proc_verifica=F.userid) LEFT JOIN pe.e_categoriapratica D ON(B.categoria=D.id);";
        $res=$db->fetchAll($sql);
        $total=$res[0]["totali"];
        $sql=sprintf("SELECT DISTINCT pratica,numero,data_sorteggio,data_avvio,F.nome as resp_proc,C.nome as tipo_pratica,E.nome as tipo,coalesce(G.nome,'Da verificare') as esito FROM pe.verifiche A INNER JOIN pe.avvioproc B USING(pratica) LEFT JOIN pe.e_verifiche E ON(A.tipo=E.id) INNER JOIN pe.e_tipopratica C ON (B.tipo=C.id) LEFT JOIN admin.users F ON (A.resp_proc_verifica=F.userid) LEFT JOIN pe.e_categoriapratica D ON(B.categoria=D.id) LEFT JOIN pe.e_esiti G ON(G.id=A.esito) %s %s LIMIT %s OFFSET %s;",$order,$orderType,$rows,$offset);
        $res=$db->fetchAll($sql);
        utils::debug(DEBUG_DIR."draw.debug",$sql);
        $result=Array("total"=>$total,"rows"=>$res,"elenco_id"=>$listId);
        break;
    case "scadenze":
        utils::debug("scadenze",$query["scadenze"]);
        foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
            if($key=='pe.scadenze')
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
        utils::debug(DEBUG_DIR."scadenze.debug",$sql);
        $res=$db->fetchAll($sql);
        $sql=sprintf($query["scadenze"],$f1,$filter,$order,$orderType,100000,0);
        $r1=$db->fetchAll($sql);
        $total=count($r1);
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

        //utils::debug(DEBUG_DIR."search.debug",$sql);
        $res=$db->fetchAll($sql);
        $result=appUtils::groupData($groupBy,$res);
        //utils::debug(DEBUG_DIR."groupby.debug",$result);
        break;
    case "search-online":
        $queryName="search-online";
        $order="ORDER BY data_ordinamento";
        $sql =<<<EOT
select id,pratica,prot_integ::varchar as protocollo,data_integ as data_protocollo,'Integrazione'::varchar as tipo,2 as ordine from pe.integrazioni where online=1
UNION ALL
select id,pratica,protocollo_il::varchar as protocollo,data_prot_il as data_protocollo,'Inizio Lavori'::varchar as tipo,3 as ordine from pe.lavori where il_online=1
UNION ALL
select id,pratica,protocollo_fl::varchar as protocollo,data_prot_fl as data_protocollo,'Fine Lavori'::varchar as tipo,4 as ordine from pe.lavori where fl_online=1
UNION ALL
SELECT id,pratica,protocollo::varchar,data_prot as data_protocollo,'Istanza'::varchar as tipo,1 as ordine from pe.avvioproc WHERE online=1
EOT;

		foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
        }
		utils::debug(DEBUG_DIR."filter.debug",$q);
        $listId=Array();
        $filter=implode(" $queryOP ",$q);
		if ($filter) $filter = "WHERE pratica in ($filter)"; 
        $tmp=$db->fetchAll($sql);
        $total=count($tmp);
        $sql=sprintf($query[$queryName],$filter,$order,$orderType,$rows,$offset);
        utils::debug(DEBUG_DIR."search-online.debug",$filter);
        $res=$db->fetchAll($sql);
		for($i=0;$i<count($res);$i++) $elencoId[] = $res[$i]["pratica"];
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter,"sql"=>$sql,"elenco_id"=>$elencoId);

        break;
    case "search-pagamenti":
        
        foreach($data as $key=>$value){
            $vv = Array();
            for($i=0;$i<count($value);$i++){
                $vv[]=sprintf("%s %s",$key,$value[$i]);
            }
            $vvv=implode(" $op ",$vv);
            $q[]=$vvv;
        }
        $filter=implode(" $queryOP ",$q);
        if (!$filter) $filter = "true";
        if($_REQUEST["sort"]){
            $sort = sprintf("ORDER BY %s %s",$_REQUEST["sort"],$_REQUEST["order"]);
        }
        else{
            $sort = "ORDER BY data_pagamento DESC,pratica DESC";
        }
        $sql =<<<EOT
WITH search_pagamenti AS (                
SELECT 
avvioproc.pratica,avvioproc.numero,avvioproc.protocollo,avvioproc.data_prot,avvioproc.tipo as tipo_id,avvioproc.categoria as categoria_id,
importo,data_pagamento,causale,e_codici_pagamento.nome as tipo_pagamento,codice_univoco,identificativofiscale,anagrafica,
e_tipopratica.nome as tipo,e_categoriapratica.nome as categoria
FROM
ragioneria.importi_versati INNER JOIN pe.avvioproc USING (pratica)
INNER JOIN ragioneria.e_codici_pagamento ON(importi_versati.tipo=e_codici_pagamento.codice)
INNER JOIN ragioneria.e_metodi_pagamento ON(importi_versati.metodo=e_metodi_pagamento.codice)
LEFT JOIN pe.e_tipopratica ON (avvioproc.tipo=e_tipopratica.id)
LEFT JOIN pe.e_categoriapratica ON (avvioproc.categoria=e_categoriapratica.id)
)
SELECT * FROM search_pagamenti    
WHERE $filter
$sort

EOT;
        $res=$db->fetchAll($sql);
        $sql = str_replace(PHP_EOL, ' ', $sql);
        $result=Array("total"=>count($res),"rows"=>$res,"filter"=>$filter,"sql"=>$sql,"elenco_id"=>Array());
        break;
    default:
        $app = $_REQUEST["application"];
        switch($app){
            case "ce":
                $queryName="search-ce";
                $order="ORDER BY data_convocazione";
                break;
            case "cdu":
                $queryName="search-cdu";
                break;
            case "vigi":
                $queryName="search-vigi";
                break;
            case "agi":
                $queryName="search-agi";
                break;
            case "storage":
                $queryName="storage";
                $order="ORDER BY data_invio";
                break;
            default:
                $queryName="default";
                $order = "ORDER BY 2";
                break;
        }
        foreach($data as $key=>$value){
            $q[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
        }
		utils::debug(DEBUG_DIR."filter.debug",$q);
        $listId=Array();
        $filter=implode(" $queryOP ",$q);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        $total=count($listId);
        $sql=sprintf($query[$queryName],$filter,$order,$orderType,$rows,$offset);
        utils::debug(DEBUG_DIR."search.debug",$sql);
        $res=$db->fetchAll($sql);
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter,"sql"=>$sql,"elenco_id"=>$listId);
}
header('Content-Type: application/json');
print json_encode($result);
return;


?>

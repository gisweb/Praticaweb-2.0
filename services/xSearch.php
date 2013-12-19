<?php
include_once "../login.php";
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
$result=Array();
$db=appUtils::getDB();
switch($action){
    default:
        foreach($data as $key=>$value){
            $query[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
        }
        $filter=implode(" $queryOP ",$query);
        $total=count($db->fetchAll($filter));
        $sql=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.elenco_cu,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_ct
   FROM ( SELECT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM pe.cterreni a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) G USING(pratica) LEFT JOIN
( SELECT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_cu
   FROM ( SELECT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM pe.curbano a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE('int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)
WHERE pratica IN ($filter) 
$order $orderType LIMIT $rows OFFSET $offset                 
EOT;
        utils::debug("search",$sql);
        $res=$db->fetchAll($sql);
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter);
        break;
}

print json_encode($result);
return;


?>
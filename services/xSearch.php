<?php
include_once "../login.php";
error_reporting(E_ERROR);
/*funzione di Raggruppamento dei dati*/
function groupData($mode,$res){
    $result=Array();
    switch($mode){
        case "civico":
            for($i=0;$i<count($res);$i++){
                $rec=$res[$i];
                $codvia=preg_replace("/[^A-Za-z0-9]/", '', strtolower($rec["via"]));
                $via=$rec["via"];
                $civico=preg_replace("([\\/]+)","-",$rec["civico"]);
                $civico=preg_replace("([\.]+)","",$civico);
                $descrizione=sprintf("Pratica n° %s del %s",$rec["numero"],$rec["data_presentazione"]);
                $ct=$rec["elenco_ct"];
                $cu=$rec["elenco_cu"];
                $linkToPratica=$rec["pratica"];
                $r[$codvia][$civico][$rec["pratica"]]=Array("via"=>$via,"civico"=>$civico,"info"=>Array("id"=>$rec["pratica"],"name"=>$descrizione,"descrizione"=>$descrizione,"ct"=>$ct,"cu"=>$cu,"civico"=>"","via"=>"","pratica"=>$linkToPratica,"oggetto"=>$rec["oggetto"]));

            }
            foreach($r as $codvia=>$values){
                $civici=Array();
                foreach($values as $civ=>$vals){
                    $pratiche=Array();
                    foreach($vals as $pr=>$data){
                        $pratiche[]=$data["info"];
                        $via=$data["via"];
                    }
                    $civico=Array("id"=>"$codvia-$civ","name"=>$civ,"civico"=>"$civ","via"=>"$via","descrizione"=>"","ct"=>"","cu"=>"","children"=>$pratiche,"oggetto"=>"","pratica"=>"","state"=>"closed");
                    $civici[]=$civico;
                }
                $via=Array("id"=>"$codvia","civico"=>"","name"=>$via,"via"=>"$via","oggetto"=>"","descrizione"=>"","ct"=>"","cu"=>"","pratica"=>"","state"=>"closed","children"=>$civici);
                $result[]=$via;
            }
            
            break;
        case "particella-terreni":
        case "particella-urbano":
            for($i=0;$i<count($res);$i++){
                $rec=$res[$i];
                $sez=preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($rec["sezione"]));
                $fg=preg_replace("/[^A-Za-z0-9 ]/", '',$rec["foglio"]);
                $mp=preg_replace("/[^A-Za-z0-9 ]/", '',$rec["mappale"]);
                $descrizione=sprintf("Pratica n° %s del %s",$rec["numero"],$rec["data_presentazione"]);
                $ubicazione=$rec["ubicazione"];
                $cu=$rec["elenco_cu"];
                $r[$sez][$fg][$mp][$rec["pratica"]]=Array(
                    "sezione"=>$sez,
                    "foglio"=>$fg,
                    "mappale"=>$mp,
                    "info"=>Array(
                        "id"=>$rec["pratica"],
                        "name"=>$descrizione,
                        "descrizione"=>$descrizione,
                        "ubicazione"=>$ubicazione,
                        "cu"=>$cu,
                        "pratica"=>$rec["pratica"],
                        "oggetto"=>$rec["oggetto"]
                    )
                );

            }
            $sezioni=Array();
            foreach($r as $sez=>$values){
                $fogli=Array();
                foreach($values as $fgs=>$v){
                    $mappali=Array();
                    foreach($v as $maps=>$vals){
                        
                        $pratiche=Array();
                        foreach($vals as $pr=>$data){
                            $pratiche[]=$data["info"];
                            $mappale=$data["mappale"];
                        }
                        $mappale=Array("id"=>sprintf("%s-%s-%s",$sez,$fgs,$maps),"name"=>"Mappale $maps","descrizione"=>"","ubicazione"=>"","cu"=>"","children"=>$pratiche,"oggetto"=>"","pratica"=>"","state"=>"closed");
                        $mappali[]=$mappale;
                    }
                    $foglio=Array("id"=>sprintf("%s-%s",$sez,$fgs),"name"=>"Foglio $fgs","descrizione"=>"","ubicazione"=>"","cu"=>"","children"=>$mappali,"oggetto"=>"","pratica"=>"","state"=>"closed");
                    $fogli[]=$foglio;
                }
                $sezione=Array("id"=>sprintf("%s",$sez),"name"=>"Sezione $sez","descrizione"=>"","ubicazione"=>"","cu"=>"","children"=>$fogli,"oggetto"=>"","pratica"=>"","state"=>"closed");
                $result[]=$sezione;
            }
            break;
    }
    return $result;
    
}

/*QUERY per la ricerca*/
$searchQuery=<<<EOT
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
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)
WHERE pratica IN (%s) 
%s %s LIMIT %s OFFSET %s                 
EOT;
/*QUERY per la ricerca e il raggruppamento dei civici*/
$civiciQuery=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.elenco_cu,I.via,I.civico,I.interno
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_ct
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.cterreni) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) G USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_cu
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.curbano) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) H USING(pratica) LEFT JOIN
(SELECT DISTINCT pratica, coalesce(via,'') as via, coalesce(civico,'s.c.') as civico,coalesce(interno,'s.i.') as interno FROM pe.indirizzi WHERE %s) I USING(pratica)
WHERE pratica IN (%s) 
ORDER BY via,civico,data_prot DESC               
EOT;
/*QUERY di ricerca e raggruppamento catasto terreni*/
$terreniQuery=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,H.elenco_cu,G.sezione,G.foglio,G.mappale,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT pratica,coalesce(sezione,'nessuna sezione') as sezione,coalesce(foglio,'') as foglio,coalesce(mappale,'') as mappale FROM pe.cterreni WHERE %s) G USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_cu
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.curbano) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)
WHERE pratica IN (%s) 
ORDER BY sezione,foglio,mappale,data_prot DESC               
EOT;
/*QUERY di ricerca e raggruppamento catasto urbano*/
$urbanoQuery=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.sezione,H.foglio,H.mappale,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_ct
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.cterreni) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) G USING(pratica) LEFT JOIN
( SELECT DISTINCT pratica,coalesce(sezione,'nessuna sezione') as sezione,coalesce(foglio,'') as foglio,coalesce(mappale,'') as mappale FROM pe.curbano WHERE %s) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)WHERE pratica IN (%s) 
ORDER BY sezione,foglio,mappale,data_prot DESC           
EOT;

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
    case "group":
        
        foreach($data as $key=>$value){
            $query[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
            $f1=implode(" $op ",$value);
        }
               
        $listId=Array();
        $filter=implode(" $queryOP ",$query);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        switch ($groupBy){
            case "particella-urbano":
                $searchQuery=$urbanoQuery;
                break;
            case "particella-terreni":
                $searchQuery=$terreniQuery;
                break;
            default:
                $searchQuery=$civiciQuery;
                break;
                
        }
        $sql=sprintf($searchQuery,$f1,$filter);

        utils::debug("search",$sql);
        $res=$db->fetchAll($sql);
        $result=groupData($groupBy,$res);
        utils::debug("test",$result);
        break;
    default:
        foreach($data as $key=>$value){
            $query[]="(SELECT DISTINCT pratica FROM $key WHERE ".implode(" $op ",$value).")";
        }
        $listId=Array();
        $filter=implode(" $queryOP ",$query);
        $tmp=$db->fetchAll($filter);
        for($i=0;$i<count($tmp);$i++) {
            $listId[]=$tmp[$i]['pratica'];
        }
        $total=count($listId);
        $sql=sprintf($searchQuery,$filter,$order,$orderType,$rows,$offset);
        utils::debug("search",$sql);
        $res=$db->fetchAll($sql);
        $result=Array("total"=>$total,"rows"=>$res,"filter"=>$filter,"elenco_id"=>$listId);
        break;
}
header('Content-Type: application/json');
print json_encode($result);
return;


?>
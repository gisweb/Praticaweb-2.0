<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=$dbconn;
$elenco=explode(',',$_REQUEST["elenco"]);
$type=$_REQUEST["report"];
switch($type){
    case "vigi":
        $sql = <<<EOT
WITH pratica AS (
select pratica,oggetto,to_char(coalesce(data_verbale,NULL::date),'dd/mm/YYYY') as data_relazione_tecnica,numero_verbale,data_preliminare_esposto,protocollo_preliminare_esposto,note from vigi.avvioproc A 
),
resp_abuso AS (
select pratica,array_to_string(array_agg(DISTINCT coalesce(ragsoc,coalesce(coalesce(cognome,'')||' '||coalesce(nome,'')))),',') as resp_abuso from vigi.soggetti where resp_abuso=1 and voltura=0 group by pratica
),
violazioni as (
select A.pratica,ARRAY_TO_STRING(ARRAY_AGG(B.descrizione),'\\n ') as violazioni from vigi.infrazioni A inner join vigi.e_violazioni B on A.tipo=B.id left join vigi.ordinanze C on A.id= C.infrazione group by A.pratica
),
ubicazione AS(
SELECT pratica,array_to_string(array_agg(via || coalesce(' '||civico,'')),',')  as ubi FROM vigi.indirizzi group by pratica
),
sospensioni AS (
    SELECT pratica,array_to_string(array_agg(to_char(coalesce(data_sospensione,NULL::date),'dd/mm/YYYY')),'\\n') as data_sosp,array_to_string(array_agg(prot_sospensione),'\\n') as prot_sosp FROM vigi.sospensioni where tipo = 1 group by pratica
),
demolizioni AS (
    SELECT pratica,array_to_string(array_agg(to_char(coalesce(data_demolizione,NULL::date),'dd/mm/YYYY')),'\\n') as data_dem,array_to_string(array_agg(protocollo),'\\n') as prot_dem FROM vigi.ordinanze group by pratica
)
SELECT
    ROW_NUMBER() over() as "Progr.",protocollo_preliminare_esposto as "Numero Com. Pol.Giudiziaria",
    data_preliminare_esposto as "Data Com. Polizia Giudiziaria",data_relazione_tecnica as"Data Relazione Tecnica",
    numero_verbale as "Numero Relazione Tecnica",violazioni as "Violazioni",resp_abuso as "Responsabile Abuso",
    oggetto as "Descrizione",ubi as "Ubicazione",
    prot_sosp as "Protocollo Sospensione Lavori",data_sosp as "Data Sospensione Lavori",
    prot_dem as "Protocollo Demolizioni",data_dem as "Data Demolizione",
    note as "Note"
FROM
pratica left join
resp_abuso using(pratica)  left join
violazioni using(pratica) left join
ubicazione using(pratica) left join
sospensioni using(pratica) left join
demolizioni using (pratica)
where pratica in ($_REQUEST[elenco]);
EOT;
        break;
    case "online":
        $sql = <<<EOT
WITH istanze_online AS (
select id,pratica,prot_integ::varchar as protocollo,data_integ as data_protocollo,'Integrazione'::varchar as tipo,2 as ordine from pe.integrazioni where online=1
UNION ALL
select id,pratica,protocollo_il::varchar as protocollo,data_prot_il as data_protocollo,'Inizio Lavori'::varchar as tipo,3 as ordine from pe.lavori where il_online=1
UNION ALL
select id,pratica,protocollo_fl::varchar as protocollo,data_prot_fl as data_protocollo,'Fine Lavori'::varchar as tipo,4 as ordine from pe.lavori where fl_online=1
UNION ALL
SELECT id,pratica,protocollo::varchar,data_prot as data_protocollo,'Istanza'::varchar as tipo,1 as ordine from pe.avvioproc WHERE online=1)
SELECT
    XX.tipo as tipo_istanza,A.pratica,XX.data_protocollo as data_ordinamento,A.numero,XX.protocollo,XX.data_protocollo as data_prot,A.data_presentazione,A.oggetto,1 as online,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it,M.titolo,M.data_rilascio,A.sportello,Q.opzione as vincolo_paes
    ,coalesce(R.nome,'non assegnata') as responsabile_ia
FROM 
istanze_online XX INNER JOIN 
pe.avvioproc A USING (pratica) LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN
(SELECT pratica,titolo,data_rilascio FROM pe.titolo) M USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) LEFT JOIN
(SELECT pratica,il,fl,protocollo_il,protocollo_fl FROM pe.lavori) P USING(pratica)
LEFT JOIN admin.users O ON(A.resp_it=O.userid)
--LEFT JOIN (SELECT id,pratica,tipo as tipo_verifica,data_avvio as data_avvio_verifica,esito FROM pe.verifiche AP INNER JOIN pe.e_verifiche BP ON(AP.tipo = BP.id)) P USING(pratica) 
LEFT JOIN admin.users R ON(A.resp_ia=R.userid)
LEFT JOIN pe.elenco_opzione_ap Q ON (vincolo_paes=Q.id)
EOT;
       break;   
    case "pagamenti":
$sql = <<<EOT
WITH search_pagamenti AS (                
SELECT 
A.id,B.pratica,B.numero,B.protocollo,B.data_prot,B.tipo as tipo_id,B.categoria as categoria_id,
A.importo,data_pagamento,causale,C.nome as tipo_pagamento,C.capitolo,codice_univoco,identificativofiscale,anagrafica,D.nome as modo_pagamento,
E.nome as tipo,coalesce(F.nome,'') as categoria,G.flusso
FROM
ragioneria.importi_versati A INNER JOIN pe.avvioproc B USING (pratica)
INNER JOIN ragioneria.e_codici_pagamento C ON(A.tipo=C.codice)
INNER JOIN ragioneria.e_metodi_pagamento D ON(A.metodo=D.codice)
LEFT JOIN pe.e_tipopratica E ON (B.tipo=E.id)
LEFT JOIN pe.e_categoriapratica F ON (B.categoria=F.id)
LEFT JOIN ragioneria.flussi G ON(A.codice_univoco=G.iuv)            
)
SELECT 
    numero as "Numero",protocollo as "Protocollo",data_prot as "Data Prot.",format('%s %s',tipo,categoria) as "Tipo Pratica",
    importo as "Importo",data_pagamento as "Data Pagam.",tipo_pagamento as "Tipo Pagam.",capitolo as "Capitolo",causale as "Causale", 
    format('IUV : %s',codice_univoco::text) as "Codice Pagam.",flusso as "Flusso",anagrafica as "Anagrafica",identificativofiscale as "C.F./P.IVA"    
FROM search_pagamenti 
WHERE 
    id in ($_REQUEST[elenco]);
EOT;
        
       break;
    default:
        $sql = <<<EOT
WITH pratica AS (
select pratica,B.nome as "Tipo Pratica",numero as "Numero",protocollo as "Protocollo",to_char(coalesce(data_prot,data_presentazione),'dd/mm/YYYY') as "Data Protocollo",oggetto as "Oggetto",note as "Note",CASE WHEN (vincolo_paes=1) THEN 'SI' ELSE 'NO' END as "Vincolo Paesaggistico" from pe.avvioproc A inner join pe.e_tipopratica B on(A.tipo=B.id)
),
richiedente AS (
select pratica,array_to_string(array_agg(DISTINCT coalesce(ragsoc,coalesce(cognome||' '||nome))),',') as "Richiedente" from pe.soggetti where richiedente=1 and voltura=0 group by pratica
),
progettista AS (
select pratica,array_to_string(array_agg(DISTINCT coalesce(ragsoc,coalesce(cognome||' '||nome))),',') as "Progettista" from pe.soggetti where progettista=1 and voltura=0 group by pratica
),
ct AS (
SELECT pratica,array_to_string(array_agg('Sezione:'||sezione||' Foglio:'||foglio||coalesce(' Mappale:'||mappale,'')||coalesce(' Sub:'||sub,'')),',') "Catasto Terreni" FROM pe.cterreni group by pratica
),
ubicazione AS(
SELECT pratica,array_to_string(array_agg(via || coalesce(' '||civico,'')),',') as "Indirizzo" FROM pe.indirizzi group by pratica
),
titolo AS(
select pratica,titolo as "Titolo",to_char(data_rilascio,'dd/mm/YYYY') as "Data Rilascio" from pe.titolo
)
select * from pratica left join richiedente using(pratica) left join progettista using(pratica) left join ct using(pratica) left join ubicazione using(pratica)  left join titolo using(pratica) where pratica in ($_REQUEST[elenco]);
EOT;
        break;
}


$db->sql_query($sql);
for($i=0;$i<$db->sql_numfields();$i++) $header[0][]=$db->sql_fieldname($i);
$ris=$db->sql_fetchrowset();

$result=array_merge($header,$ris);
//if(!$result) echo "<p>$sql</p>";
require_once APPS_DIR."plugins/PHPExcel.php";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("report.xlsx");
$objPHPExcel->getActiveSheet()->fromArray($result);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");
header('Content-Disposition:  attachment; filename=report.xlsx');
$objWriter->save('php://output');
?>

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
select pratica,array_to_string(array_agg(DISTINCT coalesce(ragsoc,coalesce(cognome||' '||nome))),',') as resp_abuso from vigi.soggetti where resp_abuso=1 and voltura=0 group by pratica
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
    default:
        $sql = <<<EOT
WITH pratica AS (
select pratica,B.nome as "Tipo Pratica",numero as "Numero",protocollo as "Protocollo",to_char(coalesce(data_prot,data_presentazione),'dd/mm/YYYY') as "Data Protocollo",oggetto as "Oggetto",note as "Note" from pe.avvioproc A inner join pe.e_tipopratica B on(A.tipo=B.id)
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

require_once APPS_DIR."plugins/PHPExcel.php";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("report.xlsx");
$objPHPExcel->getActiveSheet()->fromArray($result);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");
header('Content-Disposition:  attachment; filename=report.xlsx');
$objWriter->save('php://output');
?>

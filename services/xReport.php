<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=$dbconn;
$elenco=explode(',',$_REQUEST["elenco"]);
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

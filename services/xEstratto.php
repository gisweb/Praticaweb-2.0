<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=$dbconn;
$elenco=explode(',',$_REQUEST["elenco"]);
$sql = <<<EOT
SELECT * from ragioneria.estrattoconto;
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

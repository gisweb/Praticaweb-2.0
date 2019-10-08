<?php
include_once "../login.php";
error_reporting(E_ERROR);
//if(!$dbconn->connection_id){
	$dbconn=new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$dbconn->db_connect_id)  die( "Impossibile connettersi al database");
//}
if($_REQUEST['id_doc']){
	$idDoc=$_REQUEST['id_doc'];
	$testo=$_REQUEST['testo'];
	$testo=html_entity_decode($testo);
	$sql="SELECT file_doc,definizione,css.nome,print_type,stampe.form,stampe.pratica,c.testo as footer,d.testo as header,d.margin as mtop,c.margin as mbottom FROM stp.stampe left join stp.e_modelli on(stampe.modello=e_modelli.id) left join stp.css on(css_id=css.id) left join stp.e_intestazioni c on(footer=c.id) left join stp.e_intestazioni d on(header=d.id) WHERE stampe.id=$idDoc;";
	$dbconn->sql_query($sql);
	$pratica=$dbconn->sql_fetchfield('pratica');
	$file=$dbconn->sql_fetchfield('file_doc');
	$definizione=$dbconn->sql_fetchfield('definizione');
	$css_name=$dbconn->sql_fetchfield('nome');
	$form=$dbconn->sql_fetchfield('form');
	$footer=$dbconn->sql_fetchfield("footer");
	$header=$dbconn->sql_fetchfield("header");
	$mbottom=$dbconn->sql_fetchfield("mbottom");
	$mtop=$dbconn->sql_fetchfield("mtop");
	$is_cdu=($form=='cdu.vincoli')?(1):(0);
	
	$infoFile=pathinfo($file);
	$nome=$infoFile["filename"];
	$ext=$infoFile["extension"];
	//print mb_detect_encoding($testo,"UTF-8, ISO-8859-1,ISO-8859-15");
	$testo=utf8_encode($testo);
	$pr=new pratica($pratica,$is_cdu);
	$sql="UPDATE stp.stampe SET testohtml='".addslashes($testo)."' WHERE id=$idDoc";
	
	//echo $sql;
	$error=0;
	if(!$dbconn->sql_query($sql)){
		$error=1;
		//print json_encode($dbconn->sql_error());
	}
	$html="<html><head><style>$definizione</style></head><body>$testo</body></html>";
	$html=str_replace("<!-- pagebreak -->","<pagebreak />",stripslashes($html));
	//$html=str_replace('src="images/alghero.png"','src="http://'.$_SERVER["SERVER_NAME"].'/images/alghero.png"',$html);
	
	require_once(APPS_DIR."plugins/mpdf/mpdf.php");
	@unlink($pr->documenti.$nome.".pdf");

	if ($footer){
		$mpdf=new mPDF('','A4',0,'',15,15,15,45,9,9,'P'); 
		$mpdf->SetHTMLFooter($footer,'O',true);
		$mpdf->SetHTMLFooter($footer,'E',true);
	}
	else{
		$mpdf=new mPDF(); 
	}
	//
	$mpdf->WriteHTML($header);
	$mpdf->WriteHTML($html);
	
	$mpdf->Output($pr->documenti.$nome.".pdf");

	if($error) $result=Array('error'=>1,'message'=>$sql);
	else
		$result=Array('error'=>0,'message'=>'Salvataggio effettuato correttamente');
	print(json_encode($result));
	return;
}
elseif($_REQUEST['action']=='createxls'){
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("PraticaWeb")
		->setTitle($dataQuery["title"])
		->setSubject($dataQuery["title"])
		->setDescription("");

	$objPHPExcel->setActiveSheetIndex(0);	
	for($i=0;$i<count($data);$i++){
		for($j=0;$j<count($data[$i]);$j++)
			$objPHPExcel->getActiveSheet(0)->setCellValueByColumnAndRow($j, $i+1,$data[$i][$j]);
	}


	$file=time().'.xls';
	$xlsFile=$imgPath.$file;
	$webFile=$webPath.$file;
	$objPHPExcel->setActiveSheetIndex(0);	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$objWriter->save($xlsFile);
	
}
?>

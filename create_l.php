<?php
require_once 'login.php';

require_once APPS_DIR.'lib/utils/utils.pdf.php';


if (!isset($_REQUEST['pratica'])){
	echo json_encode(Array('error'=>1));
	return;
}
else{
	$pr=new pratica($_REQUEST['pratica']);
	
}	
$idpratica=$_REQUEST['pratica'];

	$m[1] =$_REQUEST['m1'];
	$m[2] =$_REQUEST['m2'];		//Data
	$m[3] =$_REQUEST['m3'];		//Intestatario
	$m[4] =$_REQUEST['m4'];		//Indirizzo
	$m[5] =$_REQUEST['m5'];		//Tipo Intervento
	$m[6] =$_REQUEST['m6'];		//Zona Urbanistica
	$m[7] =$_REQUEST['m7'];		//Corrispettivo Monetario
	$m[8] =$_REQUEST['m8'];		//Superficie Lotto
	$m[9] =$_REQUEST['m9'];		//Vol. Esistente
	$m[10]=$_REQUEST['m10'];	//Vol. Progetto
	$m[11]=$_REQUEST['m11'];	//Cessione Proposta
	$m[13]=$_REQUEST['m13'];	//Cessione C
	$m[14]=$_REQUEST['m14'];	//Monetizzazione M
	$m[15]=$_REQUEST['m15'];	//Rate
	$m[16]=$_REQUEST['m16'];	//Vol. 3/1
	$m[17]=$_REQUEST['m17'];	//Vol. Calcolo
	$m[18]=$_REQUEST['m18'];	//Superficie cessione (Vc X 18 : 100)
	$m[19]=$_REQUEST['m19'];	//Cessione comma f
	$m[20]=$_REQUEST['m20'];

	$time=time();
	for ($i=7;$i<=20;$i++){
        $m[$i]=zeri($m[$i]);
    }
	//echo $m[7];
	if ($m[5]=="1") {	$m[5]="Nuova Costruzione"; }
	else	{	$m[5]="Ampliamento"; }

	$pe=$m[1];
	
	$m[1]=correggi($m[1]);
	
	
	

	
	$int=str_replace(" ","_",$m[3]);
	$file_n=$pr->pratica."-Corrispettivo Monetario.pdf";
	$dest=$pr->documenti.$file_n;
	
		//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
		$pdf = new PDF();
		$pdf->SetFont('Arial','',10);
		$pdf->AddPage();
		
		$title="CORRISPETTIVO MONETARIO";
		$pdf->Cell(0,0,$title,0,0,'C');
		$pdf->SetFont('Arial','B',8);
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Pratica Edilizia:','LT',0,'L');
		$pdf->Cell(30,5,$pe,"TR",0,'L');
		$pdf->Cell(20,5,'',0,0,'L');
		$pdf->Cell(30,5,'Intestatario:','LT',0,'L');
		$pdf->Cell(60,5,$m[3],"TR",1,'RT');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Data:','LB',0,'L');
		$pdf->Cell(30,5,$m[2],"RB",0,'L');
		$pdf->Cell(20,5,'',0,0,'L');
		$pdf->Cell(30,5,'Indirizzo:','LB',0,'L');
		$pdf->Cell(60,5,$m[4],"BR",1,'L');
		$pdf->Ln(10);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Tipo Intervento',1,0,'C');
		$pdf->Cell(40,5,$m[5],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Zona Urbanistica',1,0,'C');
		$pdf->Cell(40,5,$m[6],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Corrispettivo monetario',1,0,'C');
		$pdf->Cell(40,5,$m[7].' '.chr(128).'/mq',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Superficie lotto',1,0,'C');
		$pdf->Cell(40,5,$m[8].' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Volume esistente',1,0,'C');
		$pdf->Cell(40,5,$m[9].' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Volume in progetto:',1,0,'C');
		$pdf->Cell(40,5,$m[10].' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Volume totale:',1,0,'C');
		$tot=zeri($m[10]+$m[9]);
		$pdf->Cell(40,5,$tot.' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Volume 3/1',1,0,'C');
		$pdf->Cell(40,5,$m[16].' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Volume di calcolo Vc',1,0,'C');
		$pdf->Cell(40,5,$m[17].' mc',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Superficie cessione (Vc X 18 : 100)',1,0,'C');
		$pdf->Cell(40,5,$m[18].' mq',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Cessione proposta:',1,0,'C');
		$pdf->Cell(40,5,$m[11].' %',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Cessione C',1,0,'C');
		$pdf->Cell(40,5,$m[13].' mq',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Monetizzazione M (C X 37.70 '.chr(128).'/mq) ',1,0,'C');
		$pdf->Cell(40,5,$m[14].' '.chr(128).'',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Prima rata 50% M',1,0,'C');
		$pdf->Cell(40,5,$m[15].' '.chr(128).'',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Seconda rata 50% M',1,0,'C');
		$pdf->Cell(40,5,$m[15].' '.chr(128).'',1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'Cessione comma f',1,0,'C');
		$pdf->Cell(40,5,$m[19].' mq',1,1,'C');
		
		
		
		
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(50,5,'Il Progettista o Direttore dei Lavori',0,0,'C');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,5,'Visto dell\'Ufficio tecnico comunele',0,1,'C');
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
        $pdf->Output($dest,"F");

	    $file="<a href='".$dest."'>DOWNLOAD ".$file_n."</a>";
	    
		$testo="Creato il documento <img src=\"images/pdf.png\" border=0 onclick=\"window.open(\'$pr->url_documenti$file_n\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'$pr->url_documenti$file_n\');\">Calcolo Corrispettivo Monetario</a>";
        //$testoview="Creato il documento <img src=\"images/pdf.gif\" border=0 onclick=\"window.open(\'$pr->url_documenti$dest\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'$pr->url_documenti$dest\');\">Calcolo Oneri</a>";
	    appUtils::addIter($idpratica,Array("nota"=>$testo,"nota_edit"=>$testo));
        $sql="
DELETE FROM oneri.c_monetario WHERE pratica=$pr->pratica;		
INSERT INTO oneri.c_monetario(pratica,totale_noscomputo,vol_progetto,vol_esistente,vol_indice,vol_differenza,sup_lotto,sup_cessione,sup_cessione_prop,sup_cessione_obbl,chk,uidins,tmsins) VALUES($pr->pratica,$m[14],$m[10],$m[9],$m[16],$m[17],$m[8],($m[11]*$m[18])/100,($m[11]*$m[18])/100,$m[18],1,$pr->userid,".time().");";		
        $pr->db->sql_query($sql);
		$pr->setCM();
		$pr->setRateCM();
        $pr->setFidiCM();
	    print json_encode(Array("url"=>$pr->url_documenti.$file_n,"label"=>$file_n));
		return
	
	
	 




	
?>
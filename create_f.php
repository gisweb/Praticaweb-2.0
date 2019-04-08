<?php
require_once 'login.php';
require_once 'lib/utils/utils.pdf.php';
require_once 'lib/menu.class.php';

if (!isset($_REQUEST['pratica'])){
	echo json_encode(Array('error'=>1));
	return;
}
else{
	$pr=new pratica($_REQUEST['pratica']);
	
}	
$idpratica=$_REQUEST['pratica'];

	$m[1] =$_REQUEST['m1'];
	$m[2] =$_REQUEST['m2'];
	$m[3] =$_REQUEST['m3'];
	$m[4] =$_REQUEST['m4'];
	$m[5] =$_REQUEST['m5'];
	$m[6] =$_REQUEST['m6'];
	$m[7] =$_REQUEST['m7'];
	$m[8] =$_REQUEST['m8'];
	$m[9] =$_REQUEST['m9'];
	$m[10]=$_REQUEST['m10'];
	$m[11]=$_REQUEST['m11'];
	$m[12]=$_REQUEST['m12'];
	$m[13]=$_REQUEST['m13'];
	$m[14]=$_REQUEST['m14'];
	$m[27]=$_REQUEST['m27'];
	$m[28]=$_REQUEST['m28'];
	$m[29]=$_REQUEST['m29'];
	$m[30]=$_REQUEST['m30'];
	$m[42]=$_REQUEST['m42'];
	$m[43]=$_REQUEST['m43'];
	$m[46]=$_REQUEST['m46'];
	$m[47]=$_REQUEST['m47'];
	$m[48]=$_REQUEST['m48'];
	$m[49]=$_REQUEST['m49'];
	$m[50]=$_REQUEST['m50'];
	$m[67]=$_REQUEST['m67'];
	$m[68]=$_REQUEST['m68'];
	$m[72]=$_REQUEST['m72'];
	$m[75]=$_REQUEST['m75'];
	$m[77]=$_REQUEST['m77'];
	$m[79]=$_REQUEST['m79'];
	$m[80]=$_REQUEST['m80'];
	$m[81]=$_REQUEST['m81'];
	$m[82]=$_REQUEST['m82'];
	$m[83]=$_REQUEST['m83'];
	$m[84]=$_REQUEST['m84'];
	$m[85]=$_REQUEST['m85'];
	$m[86]=$_REQUEST['m86'];
	$m[87]=$_REQUEST['m87'];
	$m[88]=$_REQUEST['m88'];
	$m[89]=$_REQUEST['m89'];
	
	//echo 'ciao -'.$m[88].'--'.$m[89].'<br>';
	
	
	
	$time=time();
	
	$pe=$m[1];
	
	$m[1]=correggi($m1);
	
	
	
	$sum=0;
		for ($j=10;$j<=14;$j++)
						{
							
							$sum=$sum +$m[$j];
							$m[$j]=zeri($m[$j]);
						}
	$m[15]=zeri($sum);
	$rap=array();
	$incr[16]="0";
	$incr[17]="5";
	$incr[18]="15";
	$incr[19]="30";
	$incr[20]="50";
	
	
	if ($m[15]!="" && $m[15]!="0.00")
			{
				for ($j=16;$j<=20;$j++)
									{
										$m[$j]=round($m[$j-6]/$m[15],2)		;
										$m[$j]=zeri($m[$j]);
										
										//echo "----> ".$rap[$j].'<br>';
										//$incr_class[$j]=round($incr[$j] * $m[$j],2);
									}
			}						
	else 	{	for ($j=16;$j<=20;$j++)
									{
										$m[$j]="0.00";
										//echo "----> ".$rap[$j].'<br>';
									
									}
			}
	
	$i1=0;
	for ($j=21;$j<=25;$j++)
						{
							
							$m[$j]=round($incr[$j-5] * $m[$j-5],2);
							$m[$j]=zeri($m[$j]);
							$i1=$i1 + $m[$j];					
							$i1=zeri($i1);
							//echo $j.'-'.$m[$j].'-'.$i1.' <br>';
						}

	$m[26]=$i1;
	
	$sum=0;
	for ($j=27;$j<=30;$j++)
						{
							//$m[$j]="100";
							$sum=$sum +$m[$j];
							$m[$j]=zeri($m[$j]);
						}
	$m[31]=zeri($sum);
	$rap="0.00";
	if ($m[15]!="0.00") {	$rap=round($m[31]/$m[15]*100,2);	}
	$m[32]=zeri($rap);
	
	
	$incr[37]="0.00";
	$incr[38]="10.00";
	$incr[39]="20.00";
	$incr[40]="30.00";
	
	for ($j=33;$j<=36;$j++)
						{
							$m[$j]="FALSO";
							$m[$j+4]="0.00";
						}
	
	if ($m[32]<=50)	{
							$m[33]="VERO";$m[37]=$incr[37];
						}
	if ($m[32]>50 && $m[32]<="75" )	{
							$m[34]="VERO";$m[38]=$incr[38];
						}
	if ($m[32]>75 && $m[32]<="100" )	{
							$m[35]="VERO";$m[39]=$incr[39];
						}
	if ($m[32]>100)	{
							$m[36]="VERO";$m[40]=$incr[40];
						}
	
	$sum=0;
	for ($j=37;$j<=40;$j++)
						{
							
							$sum=$sum +$m[$j];
						}
	
	$i2=zeri($sum);
	
	
	$m[41]=$i2;
	
	
	
	$dati_urba=explode(";",$m[42]);
	$m[42]=$dati_urba[0];
	$u1=$dati_urba[1];
	$u2=$dati_urba[2];
	//echo $m[42].'---'.$u1.'---'.$u2.'---'.$m[43].'<br>'.
	
	$sum=0;
	if ($m[88]=="1") {
					$costo1 = zeri(round($u1 * $m[43]));
					$m[88]="SI";
					$vol1=zeri($m[43]);
					}
	else {
			$u1="---"; 
			$m[88]="NO";
			$costo1 = "---";
			$vol1="---";
		 }
		
	if ($m[89]="1") {
					$costo2 = zeri(round($u2 * $m[43]));
					$m[89]="SI";
					$vol2=zeri($m[43]);
					}
	else {	
			$u2="---";
			$m[89]="NO";
			$costo2 = "---";
			$vol2="---";
		}
	
	$m[59]=zeri($costo1+$costo2);
	$sum=0;
	
	//for ($j=46;$j<=50;$j++) { echo $m[$j].'<br>';	}
	
	
	
	
	
	if ($m[46]=="1") {$m[51]="10.00";$m[46]="VERO";}
	else {$m[51]="0.00";$m[46]="FALSO";}
	
	if ($m[47]=="1") {$m[52]="10.00";$m[47]="VERO";}
	else {$m[52]="0.00";$m[47]="FALSO";}
	
	if ($m[48]=="1") {$m[53]="10.00";$m[48]="VERO";}
	else {$m[53]="0.00";$m[48]="FALSO";}
	
	if ($m[49]=="1") {$m[54]="10.00";$m[49]="VERO";}
	else {$m[54]="0.00";$m[49]="FALSO";}
	
	if ($m[50]=="1") {$m[55]="10.00";$m[50]="VERO";}
	else {$m[55]="0.00";$m[50]="FALSO";}
	
	$i3=0;
	
	for ($j=51;$j<=55;$j++) { $i3=$i3 + $m[$j];	}
		
	$m[56]=zeri($i3);
	
	$incr_totale = zeri ($i1 + $i2 + $i3);
	$m[57]=$incr_totale;
	
	
				if ($incr_totale<"5") {$cla="1";$mag="0";}
				if ($incr_totale>="5" && $incr_totale<"10") {$cla="2";$mag="5";}
				if ($incr_totale>="10" && $incr_totale<"15") {$cla="3";$mag="10";}
				if ($incr_totale>="15" && $incr_totale<"20") {$cla="4";$mag="15";}
				if ($incr_totale>="20" && $incr_totale<"25") {$cla="5";$mag="20";}
				if ($incr_totale>="25" && $incr_totale<"30") {$cla="6";$mag="25";}
				if ($incr_totale>="30" && $incr_totale<"35") {$cla="7";$mag="30";}
				if ($incr_totale>="35" && $incr_totale<"40") {$cla="8";$mag="35";}
				if ($incr_totale>="40" && $incr_totale<"45") {$cla="9";$mag="40";}
				if ($incr_totale>="45" && $incr_totale<"50") {$cla="10";$mag="45";}
				if ($incr_totale>="50") {$cla="11";$mag="50";}
	
	
	
	
	
	
	
	
	$m[61]=$cla;
	$m[62]=zeri($mag);
	
	$m[63]=$m[15];
	$m[64]=$m[31];
	$m[65]=zeri(round(($m[31] * 0.6),2));
	$m[66]=zeri($m[63]+$m[65]);
	if ($m[67]=="" && $m[67]=="0.00" ){$m[67]="0.00";} 
	else {$m[67]=zeri($m[67]);} 
	
	if ($m[68]=="" && $m[68]=="0.00" ){$m[68]="0.00";} 
	else {$m[68]=zeri($m[68]);} 
	
	$m[69]=zeri(round(($m[68] * 0.6),2));
	$m[70]=zeri($m[67]+$m[69]);
	
		
	
	if ($m[63]!="" && $m[63]!="0.00" ) {	
												$m[71]= zeri(round((($m[70] / $m[63])*100),2));
										}	
			        
	else 			{$m[71]="0.00";}
	
	
	if ($m[75]=="" && $m[75]=="0.00" ){$m[75]="0.00";$m[76]="0.00";} 
	if ($m[77]=="" && $m[77]=="0.00" ){$m[77]="0.00";$m[78]="0.00";} 
	
	if ($m[79]=="" && $m[79]=="0.00" ){$m[79]="0.00";$m[80]="0.00";} 
	else {$m[80]=zeri(round($m[79] * 0.05,2));
		  $m[79]=zeri($m[79]);
		  } 
	
	
	
	if ($m[71]<=25) {$messaggio=utf8_decode("Poichè k <= 25 il quadro sottostante non deve essere compilato");
	
					}
	
	else			{
					$messaggio=utf8_decode("Poichè k > 25 il quadro sottostante  deve essere compilato");
					$m[76]=zeri(round($m[75] * 0.07,2));
					$m[75]=zeri($m[75]);
					$m[78]=zeri(round($m[77] * 0.04,2));
					$m[77]=zeri($m[77]);
					
					}	
	
	$m[73]=$m[72]+$m[72]* $mag/100;
	$m[73]=zeri(round($m[73],2));
	$m[74]=zeri(round($m[66] * $m[73],2));
	
	$m[86] = zeri($m[81] + $m[82]+ $m[83]+ $m[84]+ $m[85]);
	
	
	for ($j=81;$j<=85;$j++) { $m[$j]=zeri($m[$j]);}
	
	
	$m[87] = zeri(round ($m[74]* $m[86]/100,2));
	
	
	//echo $m[86].'-----'.$m[74].'-----'.$m[87].'<br>';
	
	
	$m[58]=$m[87]+$m[76]+$m[78]; 
	$m[59]=zeri(round($costo1+$costo2,2));
	$m[60]=zeri($m[58]+$m[59]);
	
	
	
	
	$int=str_replace(" ","_",$m[3]);
	$file_n="N_C_".$time."_".$m[1]."_".$int.".PDF";
	$orig="../public/_modelli/tabella_prova.rtf";
	$dest=$pr->documenti.$file_n;
	
	
		//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
		$pdf = new PDF();
		// Column headings
		//$header = array('Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
		// Data loading
		//$data = $pdf->LoadData('countries.txt');
		$pdf->SetFont('Arial','',10);
		$pdf->AddPage();
		$title="TABELLA PER LA DETERMINAZIONE DEGLI ONERI DI CONCESSIONE DI CUI ALLA L. 28/01/1977 N.10";
		$pdf->Cell(0,0,$title,0,0,'C');
		////////CAMBIO RIGA
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
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',6);
		$title="TABELLA 1 -";
		$title1="Incremento per superficie utile abitabile (art. 5)";
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(17.5,5,$title,0,0,'L');
		$pdf->Cell(30,5,$title1,0,0,'L');
		$pdf->Ln(5);
		
		//MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->MultiCell(24,6,'Classe di superficie (mq)',1,'C');
		
		$pdf->setxy(44,40);
		$pdf->MultiCell(24,12,'Alloggi (n)',1,'C');
		$pdf->setxy(68,40);
		$pdf->MultiCell(24,6,'Superficie utile abitabile (mq)',1,'C');
		$pdf->setxy(92,40);
		$pdf->MultiCell(24,6,'Rapporto rispetto al totale Su',1,'C');
		$pdf->setxy(116,40);
		$pdf->MultiCell(24,6,'% incremento art. 5',1,'C');
		$pdf->setxy(140,40);
		$pdf->MultiCell(24,4,'% incremento per classi di superfici',1,'C');
		$pdf->SetFont('Arial','',6);
		////DATI TABELLA 1
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'(1)',1,0,'C');
		$pdf->Cell(24,5,'(2)',1,0,'C');
		$pdf->Cell(24,5,'(3)',1,0,'C');
		$pdf->Cell(24,5,'(4)',1,0,'C');
		$pdf->Cell(24,5,'(5)',1,0,'C');
		$pdf->Cell(24,5,'(6)',1,1,'C');
		$pdf->SetFont('Arial','',8);
		//Linea 1
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'Sup <= 95',1,0,'C');
		$pdf->Cell(24,5,$m[5],1,0,'C');
		$pdf->Cell(24,5,$m[10],1,0,'C');
		$pdf->Cell(24,5,$m[16],1,0,'C');
		$pdf->Cell(24,5,'0',1,0,'C');
		$pdf->Cell(24,5,$m[21],1,1,'C');
		
		//Lina 2
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'95< Sup <= 110',1,0,'C');
		$pdf->Cell(24,5,$m[6],1,0,'C');
		$pdf->Cell(24,5,$m[11],1,0,'C');
		$pdf->Cell(24,5,$m[17],1,0,'C');
		$pdf->Cell(24,5,'5',1,0,'C');
		$pdf->Cell(24,5,$m[22],1,1,'C');
		//Lina 3
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'110< Sup <= 130',1,0,'C');
		$pdf->Cell(24,5,$m[7],1,0,'C');
		$pdf->Cell(24,5,$m[12],1,0,'C');
		$pdf->Cell(24,5,$m[18],1,0,'C');
		$pdf->Cell(24,5,'15',1,0,'C');
		$pdf->Cell(24,5,$m[23],1,1,'C');
		//Lina 4
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'130< Sup <= 160',1,0,'C');
		$pdf->Cell(24,5,$m[8],1,0,'C');
		$pdf->Cell(24,5,$m[13],1,0,'C');
		$pdf->Cell(24,5,$m[19],1,0,'C');
		$pdf->Cell(24,5,'30',1,0,'C');
		$pdf->Cell(24,5,$m[24],1,1,'C');
		//Lina 5
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'Sup > 160',1,0,'C');
		$pdf->Cell(24,5,$m[9],1,0,'C');
		$pdf->Cell(24,5,$m[14],1,0,'C');
		$pdf->Cell(24,5,$m[20],1,0,'C');
		$pdf->Cell(24,5,'50',1,0,'C');
		$pdf->Cell(24,5,$m[25],1,1,'C');
		//Lina 6
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(24,5,'',0,0,'C');
		$pdf->Cell(24,5,'Su',0,0,'R');
		$pdf->Cell(24,5,$m[15],1,0,'C');
		$pdf->Cell(24,5,'',0,0,'C');
		$pdf->Cell(24,5,'',0,0,'C');
		$pdf->Cell(24,5,'i1',0,0,'R');
		$pdf->Cell(24,5,$m[26],1,1,'C');
		
				
		$pdf->Ln(5);
		
		$title1="TABELLA 2 - Superfici per servizi accessori relativi alla parte residenziale (art. 2)";
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->setxy(20,92);
		$pdf->MultiCell(80,4,$title1,0,'L');
		
		///////////
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->SetFont('Arial','',6);
		$title="DESTINAZIONI";
		$title1="Superficie netta di servizi e accessori (mq)";
		$pdf->Cell(60,12,$title,1,0,'C');
		$pdf->setxy(80,100);
		$pdf->MultiCell(23,4,$title1,1,'C');
		$title1="TABELLA 3 - Incremento per servizi ed accessori relativi alla parte residenziale (art. 6)";
		$pdf->SetFont('Arial','',8);
		$pdf->setxy(112,100);
		$pdf->MultiCell(51,4,$title1,0,'L');		
		
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(60,5,'(7)',1,0,'C');
		$pdf->Cell(23,5,'(8)',1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,10,'',0,0,'L');
		$title="Cantinole, soffitte, locali motore, ascensore, cabine idriche, lavatoi comuni, centrali termiche ed altri locali a stretto servizio delle residenze";
		$pdf->MultiCell(60,4,$title,1,'C');
		$pdf->setxy(80,117);
		$pdf->Cell(23,16,$m[27],1,0,'C');
		$pdf->Cell(9,16,'',0,0,'L');
		$pdf->SetFont('Arial','',6);
		$testo=utf8_decode("Intervalli di variabilità del rapporto percentuale (Snr/Su)x100");
		$pdf->MultiCell(17,3.2,$testo,1,'C');
		$pdf->setxy(129,117);
		$testo="Ipotesi che ricorre";
		$pdf->MultiCell(17,8,$testo,1,'C');
		$pdf->SetFont('Arial','',6);
		$testo="% incremento";
		$pdf->setxy(146,117);
		$pdf->MultiCell(17,16,$testo,1,'C');
		$pdf->SetFont('Arial','',8);
		//$pdf->Ln();
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(60,5,'Autorimesse',1,0,'C');
		$pdf->Cell(23,5,$m[28],1,0,'C');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'(9)',1,0,'C');
		$pdf->Cell(17,5,'(10)',1,0,'C');
		$pdf->Cell(17,5,'(11)',1,0,'C');
		$pdf->Cell(24,5,'',0,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Androni d\'ingresso e porticati liberi',1,0,'C');
		$pdf->Cell(23,5,$m[29],1,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'<= 50',1,0,'C');
		$pdf->Cell(17,5,$m[33],1,0,'C');
		$pdf->Cell(17,5,$m[37],1,0,'C');
		$pdf->Cell(24,5,'',0,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');	
		$pdf->Cell(60,5,'Logge e balconi',1,0,'C');
		$pdf->Cell(23,5,$m[30],1,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'> 50 <= 75',1,0,'C');
		$pdf->Cell(17,5,$m[34],1,0,'C');
		$pdf->Cell(17,5,$m[38],1,0,'C');
		$pdf->Cell(24,5,'',0,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Snr',0,0,'R');
		$pdf->Cell(23,5,$m[31],1,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'> 75 <= 100',1,0,'C');
		$pdf->Cell(17,5,$m[35],1,0,'C');
		$pdf->Cell(17,5,$m[39],1,0,'C');
		$pdf->Cell(24,5,'',0,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'(Snr/Su)*100',0,0,'R');
		$pdf->Cell(23,5,$m[32],1,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'> 100',1,0,'C');
		$pdf->Cell(17,5,$m[36],1,0,'C');
		$pdf->Cell(17,5,$m[40],1,0,'C');
		$pdf->Cell(24,5,'',0,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'',0,0,'R');
		$pdf->Cell(23,5,'',0,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$pdf->Cell(17,5,'',0,0,'L');
		$pdf->Cell(17,5,'',0,0,'L');
		$pdf->Cell(17,5,'i2',0,0,'R');
		$pdf->Cell(24,5,$m[41],1,1,'C');
		$pdf->Ln(15);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Zona urbanistica di P.R.G.',1,0,'L');
		$pdf->Cell(23.5,5,$m[42],1,0,'C');
		$pdf->Cell(9,5,'',0,0,'L');
		$title="TABELLA 4 - Incremento per particolari caratteristiche (art. 6)";
		$pdf->MultiCell(50,5,$title,0,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(10,5,'U1',1,0,'C');
		$pdf->Cell(68,5,'DET. ONERI DI URBANIZZAZIONE PRIMARIA',1,0,'C');
		$pdf->Cell(5,5,$m[88],1,0,'C');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'Caratteristica',1,0,'C');
		$pdf->Cell(15,5,'Ipotesi',1,0,'C');
		$pdf->Cell(20,5,'% incremento',1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(40,5,'Volumetria in progetto (mc)',1,0,'C');
		$pdf->Cell(20,5,'Residenziale',1,0,'C');
		$pdf->Cell(23,5,$vol1,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(15,5,'(11)',1,0,'C');
		$pdf->Cell(15,5,'(12)',1,0,'C');
		$pdf->Cell(20,5,'(13)',1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Onere per mc di costruzione '.chr(128).'/mc',1,0,'C');
		$pdf->Cell(23,5,$u1,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'1',1,0,'C');
		$pdf->Cell(15,5,$m[46],1,0,'C');
		$pdf->Cell(20,5,$m[51],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(60,5,'Oneri di urbanizzazione relativi '.chr(128).'',1,0,'C');
		$pdf->Cell(23,5,$costo1,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'2',1,0,'C');
		$pdf->Cell(15,5,$m[47],1,0,'C');
		$pdf->Cell(20,5,$m[52],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(10,5,'U2',1,0,'C');
		$pdf->Cell(68,5,'DET. ONERI DI URBANIZZAZIONE SECONDARIA',1,0,'C');
		$pdf->Cell(5,5,$m[89],1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(15,5,'3',1,0,'C');
		$pdf->Cell(15,5,$m[48],1,0,'C');
		$pdf->Cell(20,5,$m[53],1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(40,5,'Volumetria in progetto (mc)',1,0,'C');
		$pdf->Cell(20,5,'Residenziale',1,0,'C');
		$pdf->Cell(23,5,$vol2,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'4',1,0,'C');
		$pdf->Cell(15,5,$m[49],1,0,'C');
		$pdf->Cell(20,5,$m[54],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Onere per mc di costruzione '.chr(128).'/mc',1,0,'C');
		$pdf->Cell(23,5,$u2,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'5',1,0,'C');
		$pdf->Cell(15,5,$m[50],1,0,'C');
		$pdf->Cell(20,5,$m[55],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Oneri di urbanizzazione relativi '.chr(128).'',1,0,'C');
		$pdf->Cell(23,5,$costo2,1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(20,5,'i3',0,0,'R');
		$pdf->Cell(24,5,$m[56],1,0,'C');
		$pdf->Ln(15);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(83,5,'RIEPILOGO ONERI ART. 3 L. 10/77',1,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(20,5,'i1 + i2 + i3',0,0,'R');
		$pdf->Cell(24,5,$m[57],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Costo di costruzione '.chr(128).'',1,0,'C');
		$pdf->Cell(23,5,$m[58],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Costo di urbanizzazione '.chr(128).'',1,0,'C');
		$pdf->Cell(23,5,$m[59],1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(20,5,'Classe edif.',1,0,'R');
		$pdf->Cell(24,5,'% maggiorazione',1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'Sommano '.chr(128).'',1,0,'C');
		$pdf->Cell(23,5,$m[60],1,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(20,5,'(15)',1,0,'C');
		$pdf->Cell(24,5,'(16)',1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(60,5,'',0,0,'C');
		$pdf->Cell(23,5,'',0,0,'C');
		$pdf->Cell(9.5,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(15,5,'',0,0,'C');
		$pdf->Cell(20,5,$m[61],1,0,'C');
		$pdf->Cell(24,5,$m[62],1,1,'C');
		
		$pdf->AddPage();
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(15,5,'Tabella 5 - ',0,0,'L');
		$pdf->Cell(60,5,' Superfici residenziali e relativi accessori',0,0,'L');
		$pdf->Cell(15,5,'',0,0,'L');
		$pdf->Cell(15,5,'Tabella 6 - ',0,0,'L');
		$pdf->MultiCell(60,5,utf8_encode(' Superfici per attività turistiche, direzionali, commerciali e relativi accessori'),0,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(25,5,'Sigla',1,0,'C');
		$pdf->Cell(30,5,'Denominazione',1,0,'C');
		$pdf->Cell(25,5,'Superficie (mq)',1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(25,5,'Sigla',1,0,'C');
		$pdf->Cell(30,5,'Denominazione',1,0,'C');
		$pdf->Cell(25,5,'Superficie (mq)',1,1,'C');
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(25,5,'(17)',1,0,'C');
		$pdf->Cell(30,5,'(18)',1,0,'C');
		$pdf->Cell(25,5,'(19)',1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(25,5,'(20)',1,0,'C');
		$pdf->Cell(30,5,'(21)',1,0,'C');
		$pdf->Cell(25,5,'(22)',1,1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'1',1,0,'C');
		$pdf->Cell(15,5,'Su',1,0,'C');
		$pdf->Cell(30,5,'Sup. utile abitabile',1,0,'C');
		$pdf->Cell(25,5,$m[63],1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'1',1,0,'C');
		$pdf->Cell(15,5,'Sn (art.9)',1,0,'C');
		$pdf->Cell(30,5,'Sup. netta non resid.',1,0,'C');
		$pdf->Cell(25,5,$m[67],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'2',1,0,'C');
		$pdf->Cell(15,5,'Snr',1,0,'C');
		$pdf->Cell(30,5,'Sup. netta non res.',1,0,'C');
		$pdf->Cell(25,5,$m[64],1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'2',1,0,'C');
		$pdf->Cell(15,5,'Sa (art.9)',1,0,'C');
		$pdf->Cell(30,5,'Superficie accessori',1,0,'C');
		$pdf->Cell(25,5,$m[68],1,1,'C');
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'3',1,0,'C');
		$pdf->Cell(15,5,'60% Snr',1,0,'C');
		$pdf->Cell(30,5,'Superficie ragg.',1,0,'C');
		$pdf->Cell(25,5,$m[65],1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'3',1,0,'C');
		$pdf->Cell(15,5,'60% Sa',1,0,'C');
		$pdf->Cell(30,5,'Superficie ragg.',1,0,'C');
		$pdf->Cell(25,5,$m[69],1,1,'C');
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'4',1,0,'C');
		$pdf->Cell(15,5,'Sc',1,0,'C');
		$pdf->Cell(30,5,'Sup. complessiva',1,0,'C');
		$pdf->Cell(25,5,$m[66],1,0,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(10,5,'4',1,0,'C');
		$pdf->Cell(15,5,'St',1,0,'C');
		$pdf->Cell(30,5,'Sup. tot. non res.',1,0,'C');
		$pdf->Cell(25,5,$m[70],1,1,'C');
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->MultiCell(150,5,utf8_decode('Rapporto K fra la superficie destinata ad attività turistiche, commerciali, direzionali e relativi accessori ed Su:'),0,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(25,5,'K = (Su/St) X 100',0,0,'L');
		$pdf->Cell(25,5,$m[71],1,1,'C');
		$pdf->Ln(10);
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(100,5,'Costo massimo a mq dell\'edilizia agevolata '.chr(128).' / mq',0,0,'L');
		$pdf->Cell(25,5,$m[72],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(100,5,'Costo a mq di costruzione maggiorato '.chr(128).' / mq',0,0,'L');
		$pdf->Cell(25,5,$m[73],1,1,'C');
	    $pdf->Ln(5);	
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(100,5,'Costo D di costruzione '.chr(128).'',0,0,'L');
		$pdf->Cell(25,5,$m[74],1,1,'C');
		
		$pdf->Ln(10);
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(100,5,$messaggio,0,1,'L');
		$pdf->Ln(2);
		$pdf->rect(20,119.5,170,10); 
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(55,5,'Costo di costruzione (documentato) '.chr(128).':',0,0,'L');
		$pdf->Cell(25,5,$m[75],1,0,'C');
		$pdf->Cell(60,5,'Contributo = Cc X 0,07',0,0,'R');
		$pdf->Cell(25,5,$m[76],1,1,'C');
		$pdf->rect(20,129.5,170,10); 
		$pdf->Ln(5);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(55,5,'Costo di costruzione (documentato) '.chr(128).':',0,0,'L');
		$pdf->Cell(25,5,$m[77],1,0,'C');
		$pdf->Cell(60,5,'Contributo = Cc X 0,04',0,0,'R');
		$pdf->Cell(25,5,$m[78],1,1,'C');
		
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(100,5,'Da compilare solo per interventi su edifici esistenti',0,1,'L');
		$pdf->Ln(2);
		$pdf->rect(20,151.5,170,10); 
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(55,5,'Costo di costruzione (documentato) '.chr(128).':',0,0,'L');
		$pdf->Cell(25,5,$m[79],1,0,'C');
		$pdf->Cell(60,5,'Contributo = Cc X 0,05',0,0,'R');
		$pdf->Cell(25,5,$m[80],1,1,'C');
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(150,5,'Determinazione del contributo sul costo di costruzione (Q) per le residenze',0,1,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Quota base',1,0,'C');
		$pdf->Cell(100,5,'',1,0,'L');
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,5,$m[81],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Ubicazione',1,0,'C');
		$pdf->Cell(100,5,'Classe 1',1,0,'L');
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,5,$m[82],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Caratteristica (1)',1,0,'C');
		$pdf->Cell(100,5,'Lusso 2% - Medio 0,5% - Economico popolare 0%',1,0,'L');
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,5,$m[83],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,10,'Ubicazione (2)',1,0,'C');
		$pdf->MultiCell(100,5,'Ville mono-plurifamiliari 2% - Medio 0,5% - Edifici a torre in linea, a schiera e tipologie tradizionali dei centri rurali sardi 0%',1,'L');
		$pdf->setxy(150,189);
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,10,$m[84],1,1,'C');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'Destinazione (3)',1,0,'C');
		$pdf->Cell(100,5,'',1,0,'L');
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,5,$m[85],1,1,'C');
		$pdf->Cell(10,5,'',0,1,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(30,5,'P',1,0,'C');
		$pdf->Cell(100,5,'Somma percentuali',1,0,'L');
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->Cell(25,5,$m[86],1,1,'C');
		$pdf->Cell(10,10,'',0,1,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(80,5,'(Q) Contributo costo di costruzione - D X P - '.chr(128).'',0,0,'L');
		$pdf->Cell(25,5,$m[87],1,0,'C');
		$pdf->Cell(10,20,'',0,1,'L');
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(50,5,'Il Progettista o Direttore dei Lavori',0,0,'C');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,5,'Visto dell\'Ufficio tecnico comunale',0,1,'C');
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
		
		
			
		$pdf->SetFont('Arial','B',10);
		$pdf->AddPage();
		
		$rata_1 = zeri(round((($m[59]* 0.5) + ($m[58]* 0.3)),2));
		$fide   = zeri(round(($m[60] - $rata_1),2));
		$rata_2 = zeri(round((($m[59]*0.25) + ($m[58]*0.3)),2));
		$rata_3 = zeri(round((($m[59]*0.25) + ($m[58]*0.4)),2));
		$m[58] = zeri($m[58]);
		$verifica=zeri(round($rata_1+$rata_2+$rata_3,$rata_1,2));
		
		
		$title="RATEIZZAZIONE PAGAMENTI";
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
		$pdf->Cell(60,5,'ONERI DI URBANIZZAZIONE:',1,0,'C');
		$pdf->Cell(40,5,$m[59],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'ONERI DI COSTRUZIONE:',1,0,'C');
		$pdf->Cell(40,5,$m[58],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'TOTALE:',1,0,'C');
		$pdf->Cell(40,5,$m[60],1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'PRIMA RATA:',1,0,'C');
		$pdf->Cell(40,5,$rata_1,1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'SOMMA RIMANENTE:',1,0,'C');
		$pdf->Cell(40,5,$fide,1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'SECONDA RATA:',1,0,'C');
		$pdf->Cell(40,5,$rata_2,1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'TERZA RATA:',1,0,'C');
		$pdf->Cell(40,5,$rata_3,1,1,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(60,5,'VERIFICA:',1,0,'C');
		$pdf->Cell(40,5,$verifica,1,1,'C');
		$pdf->Ln(10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Cell(50,5,'Il Progettista o Direttore dei Lavori',0,0,'C');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,5,'Visto dell\'Ufficio tecnico comunele',0,1,'C');
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(50,15,'','B',0,'L');
		
		
		
		//$pdf->Cell(17,5,'i2',0,0,'R');
		//$pdf->Cell(24,5,'d',1,0,'C');
		
		/*$pdf->Cell(24,5,'95< Sup <= 110',1,0,'C');
		
		$pdf->Cell(24,5,'130< Sup <= 160',1,0,'C');
		$pdf->Cell(24,5,'Sup > 160',1,0,'C');*/
		
		
		
		//$pdf->setxy(164,40);
		//$pdf->MultiCell(26,5,'Colonna prova',1,'C');
			
		
		
		$pdf->Output($dest,"F");
		$testo="Creato il documento <img src=\"images/pdf.png\" border=0 onclick=\"window.open(\'$pr->url_documenti$file_n\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'$pr->url_documenti$file_n\');\">Calcolo Oneri</a>";
        //$testoview="Creato il documento <img src=\"images/pdf.gif\" border=0 onclick=\"window.open(\'$pr->url_documenti$dest\');\">&nbsp;&nbsp;<a href=\"#\" onclick=\"window.open(\'$pr->url_documenti$dest\');\">Calcolo Oneri</a>";
	    appUtils::addIter($idpratica,Array("nota"=>$testo,"nota_edit"=>$testo));
        $sql="DELETE FROM oneri.oneri_concessori WHERE pratica=$pr->pratica;INSERT INTO oneri.oneri_concessori(pratica,oneri_urbanizzazione,oneri_costruzione,rateizzato,chk,uidins,tmsins) VALUES($pr->pratica,$m[59],$m[60],1,1,$pr->userid,".time().")";
        $pr->db->sql_query($sql);
        $pr->setRateOC();
		$pr->setFidiOC();
        $menu=new Menu('pratica','pe');
        $menu->add_menu($pr->pratica,'120');
        //$menu->add_menu($pr->pratica,130);
	    echo json_encode(Array("url"=>$pr->url_documenti.$file_n,"label"=>$file_n));
		return
	
	

	
	
	
?>
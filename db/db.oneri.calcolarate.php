<?php
//Gestione della rateizzazione calcolo delle rate
//DA RIVEDERE LE GESTIONE DEI DATI STATICI es titolo

//DA PERSONALIZZARE SULLE ESIGENZE DEL COMUNE

//echo "calcolo della rateizzazione";
//print_r($_POST);



$conn = utils::getDb();

$campo_cc=$_POST["scade_cc"];
$campo_oneri=$_POST["scade_oneri"];
$tipo=trim($_POST["tipo"]);
$data_rata1=$_POST["data_rata1"];

if ($campo_cc and $campo_oneri){
	$sql="select titolo.".$campo_cc." as scade_cc, titolo.".$campo_oneri." as scade_oneri,monet,cc,(b1-scb1) as b1,(b2-scb2) as b2 from oneri.totali,pe.titolo where oneri.totali.pratica=pe.titolo.pratica and pe.titolo.pratica=?"; 
	$sth=$conn->prepare($sql);
        $sth->execute(Array($idpratica));
        
}
$oneri=1;
if ($oneri){
	$sql="select * from oneri.e_rata_calcolo where tipo='?' order by rata";
	$sth=$conn->prepare($sql);
        $sth->execute(Array($tipo));
	$rate=$sth->fetchAll(PDO::FETCH_ASSOC);
	$nrec=count($rate);
	//print_r($rate);
	for ($i=0;$i<$nrec;$i++){
		$nrata=$rate[$i]["rata"];
		$titolo=$rate[$i]["titolo"];
		$calcolacc=$rate[$i]["calcola_cc"];	
		$calcolab1=$rate[$i]["calcola_b1"];
		$calcolab2=$rate[$i]["calcola_b2"];
		$scadenza=$rate[$i]["scadenza"];
		
              if($data_rata1)
                  $sql="insert into oneri.rate (pratica,rata,titolo,cc,b1,b2,data_scadenza) select $idpratica,$nrata,'$titolo',$calcolacc,$calcolab1,$calcolab2,'$data_rata1'::date  + INTERVAL '$scadenza months' from oneri.totali where oneri.totali.pratica=$idpratica";   
              else if ($campo_cc)
		    $sql="insert into oneri.rate (pratica,rata,titolo,cc,b1,b2,data_scadenza) select $idpratica,$nrata,'$titolo',$calcolacc,$calcolab1,$calcolab2,$campo_cc  + INTERVAL '$scadenza months' from oneri.totali left join pe.titolo on(oneri.totali.pratica=pe.titolo.pratica) where oneri.totali.pratica=$idpratica";
		print_debug($sql);
		//echo "<p>$sql</p>";
		$sth=$conn->prepare($sql);
                $sth->execute();
		$_SESSION["RATE"]=1;
	}
	
}

$active_form=$_POST["active_form"]."?pratica=$idpratica";




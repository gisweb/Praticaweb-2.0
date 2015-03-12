<?php
/**************************************   Pratica  ***********************************************/
$sql="SELECT A.pratica, numero, B.nome as tipo, C.descrizione as intervento, anno, 
		data_presentazione, protocollo, data_prot, protocollo_int, data_prot_int,  
		D.nome as resp_proc, data_resp, com_resp, data_com_resp, E.nome as resp_it, data_resp_it, F.nome as resp_ia, data_resp_ia,  
		rif_aut_amb, aut_amb, riferimento_to, oggetto, note, rif_pratica, riferimento, 
		diritti_segreteria, riduzione_diritti, pagamento_diritti
  FROM 
  pe.avvioproc A LEFT JOIN pe.e_tipopratica B ON (A.tipo=B.id) LEFT JOIN pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN admin.users D ON (A.resp_proc=D.userid)  LEFT JOIN admin.users E ON (A.resp_it=E.userid)  LEFT JOIN admin.users F ON (A.resp_ia=F.userid) 
  LIMIT 1";
  $ris=$db->fetchAll($sql);
  $customData=$ris[0];
/************************************  Soggetti Interessati ***************************************/
$sql="SELECT DISTINCT
		pratica, coalesce(app,'') as app, coalesce(cognome,'') as cognome, coalesce(nome,'') as nome,coalesce(app||' ','')||coalesce(cognome||' ','')||coalesce(nome,'') as nominativo, 
		coalesce(indirizzo,'') as indirizzo, coalesce(comune,'') as comune, coalesce(prov,'') as prov, coalesce(cap,'') as cap, 
		comunato, provnato, datanato, sesso, codfis,titolo,
		telefono, email, pec, 
		titolod, ragsoc, 
		sede, comuned, provd, capd, 
		piva, ccia, cciaprov, inail, inailprov, inps, inpsprov, cedile, cedileprov, 
		albo, albonumero, alboprov,
		coalesce(voltura,0) as voltura, comunicazioni, note, 
		proprietario,richiedente, concessionario, progettista, direttore, esecutore, 
		sicurezza, collaudatore,geologo, collaudatore_ca, progettista_ca, economia_diretta
		FROM pe.soggetti WHERE comunicazioni = 1 LIMIT 1";
$ris=$db->fetchAll($sql);
for($i=0;$i<count($ris);$i++){
	$soggetto=$ris[$i];
	if ($soggetto["proprietario"] && !$soggetto["voltura"]) $customData["proprietario"][]=$soggetto;
	if ($soggetto["richiedente"] && !$soggetto["voltura"]) $customData["richiedente"][]=$soggetto;
	if ($soggetto["progettista"] && !$soggetto["voltura"]) $customData["progettista"][]=$soggetto;
	if ($soggetto["progettista_ca"] && !$soggetto["voltura"]) $customData["progettista_ca"][]=$soggetto;	
	if ($soggetto["esecutore"] && !$soggetto["voltura"]) $customData["esecutore"][]=$soggetto;
	if ($soggetto["sicurezza"] && !$soggetto["voltura"]) $customData["sicurezza"][]=$soggetto;
	if ($soggetto["geologo"] && !$soggetto["voltura"]) $customData["geologo"][]=$soggetto;
	if ($soggetto["collaudatore"] && !$soggetto["voltura"]) $customData["collaudatore"][]=$soggetto;
	if ($soggetto["collaudatore_ca"] && !$soggetto["voltura"]) $customData["collaudatore_ca"][]=$soggetto;
}
/**************************************   Indirizzi  ***********************************************/
$sql="SELECT pratica, via, civico, interno, scala, piano FROM pe.indirizzi LIMIT 1;";
$ris=$db->fetchAll($sql);
for($i=0;$i<count($ris);$i++){
	$indirizzo=$ris[$i];
	$customData["indirizzo"][]=$indirizzo;
}
/**************************************   Catasto Terreni  ***********************************************/
$sql="SELECT DISTINCT coalesce(B.nome,'') as sezione,foglio,A.sezione as sez FROM pe.cterreni A LEFT JOIN nct.sezioni B USING(sezione) LIMIT 1";
$ris=$db->fetchAll($sql);
for($i=0;$i<count($ris);$i++){
	$sez=$ris[$i]["sez"];
	$fg=$ris[$i]["foglio"];
	$sezione=$ris[$i]["sezione"];
	$sql="SELECT DISTINCT mappale FROM pe.cterreni WHERE coalesce(sezione::varchar,'')=? AND coalesce(foglio::varchar,'')=? LIMIT 1 ";
	$ris=$db->fetchAll($sql,Array($sez,$fg));
	$mappali=Array();
	$arrMap=Array();
	for($j=0;$i<count($ris);$i++){
		$arrMap[]=$ris[$i]["mappale"];
		$customData["particelle_ct"][]=Array("sezione"=>$sezione,"foglio"=>$fg,"mappale"=>$ris[$i]["mappale"]);
	}
	$mappali=implode(", ",$arrMap);
	$customData["particelle_fg"][]=($sez)?(sprintf("Sez. %s Foglio %s Mappali %s",$sezione,$fg,$mappali)):(sprintf("Foglio %s Mappali %s",$fg,$mappali));
}
/**************************************   Catasto Terreni  ***********************************************/
$sql="SELECT DISTINCT coalesce(B.nome,'') as sezione,foglio,A.sezione as sez FROM pe.curbano A LEFT JOIN nct.sezioni B USING(sezione) LIMIT 1";
$ris=$db->fetchAll($sql);
for($i=0;$i<count($ris);$i++){
	$sez=$ris[$i]["sez"];
	$fg=$ris[$i]["foglio"];
	$sezione=$ris[$i]["sezione"];
	$sql="SELECT DISTINCT mappale FROM pe.curbano WHERE coalesce(sezione::varchar,'')=? AND coalesce(foglio::varchar,'')=? LIMIT 1 ";
	$ris=$db->fetchAll($sql,Array($sez,$fg));
	for($j=0;$i<count($ris);$i++){
		$customData["particelle_cu"][]=Array("sezione"=>$sezione,"foglio"=>$fg,"mappale"=>$ris[$i]["mappale"]);
	}
	
}
/**************************************   Vincoli  ***********************************************/


/**************************************   Pareri  ***********************************************/


/**************************************   Allegati  ***********************************************/
$sql="SELECT coalesce(B.descrizione,B.nome) as documento,allegato,mancante,integrato,sostituito
	FROM pe.allegati A INNER JOIN pe.e_documenti B ON(A.documento=B.id) 
	LIMIT 1";
$ris=$db->fetchAll($sql);
$allegati=Array();
$mancanti=Array();
for($i=0;$i<count($ris);$i++){
	$documento=$ris[$i];
	if ($documento["allegato"]) $allegati["documento"][]=$documento["documento"];
	if ($documento["mancante"]) $mancanti["documento"][]=$documento["documento"];
}
$customData["allegati"]=$allegati;
$customData["mancanti"]=$mancanti;
//array_walk_recursive($customData, 'decode');
?>
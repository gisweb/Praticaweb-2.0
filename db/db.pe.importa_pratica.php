<?
//importo i dati da pratica esistente che ha come id refid
//print_r($_POST);

//stesso riferimento
$db->sql_query ("update pe.avvioproc set riferimento=ap.riferimento from pe.avvioproc as ap where avvioproc.pratica=$idpratica and ap.pratica=$refid;");
$db->sql_query ("update pe.avvioproc set riferimento=$refid where pratica=$idpratica;");
//stessi indirizzi e riferimenti catastali		
$sql="insert into pe.indirizzi(pratica,via,civico,interno,scala,piano,id_via,id_civico,uidins,tmsins) select distinct $idpratica ,via,civico,interno,scala,piano,id_via,id_civico,".$_SESSION["USER_ID"].",".time()." from pe.indirizzi where pratica =$refid;";
//if(DEBUG) echo $sql;
$db->sql_query ($sql);

$sql="insert into pe.cterreni(pratica,sezione,foglio,mappale,sub,note,uidins,tmsins) select distinct $idpratica ,sezione,foglio,mappale,sub,note,".$_SESSION["USER_ID"].",".time()." from pe.cterreni where pratica =$refid;";
//if(DEBUG) echo $sql;
$db->sql_query ($sql);

$sql="insert into pe.curbano(pratica,sezione,foglio,mappale,sub,note,uidins,tmsins) select distinct $idpratica ,sezione,foglio,mappale,sub,note,".$_SESSION["USER_ID"].",".time()." from pe.curbano pratica =$refid;";
//if(DEBUG) echo $sql;
$db->sql_query ($sql);	

//stessi nominativi	
$elencocampi="app,cognome,nome,indirizzo,comune,prov,cap,telefono,email,comunato,provnato,datanato,sesso,codfis, titolo,ragsoc,titolod,sede,comuned,provd,capd,piva,ccia,cciaprov,inail,inailprov,inps,inpsprov,cedile,cedileprov,albo,albonumero,alboprov, voltura,comunicazioni,note,proprietario,richiedente,concessionario,progettista,direttore,esecutore,sicurezza,collaudatore";
	
$sql="insert into pe.soggetti (pratica,$elencocampi,uidins,tmsins) select distinct $idpratica,$elencocampi,".$_SESSION["USER_ID"].",".time()." from pe.soggetti where voltura=0 and  pratica =$refid;";		
//if(DEBUG) echo $sql;
$db->sql_query ($sql);	

//stesse zone di vincolo
$sql="insert into pe.vincoli (pratica,tavola,vincolo,zona,uidins,tmsins) select distinct $idpratica,tavola,vincolo,zona,".$_SESSION["USER_ID"].",".time()." from pe.vincoli where pratica =$refid;";		
//if(DEBUG) echo $sql;
$db->sql_query ($sql);

$sql="insert into pe.asservimenti_prat (asservimento,pratica,uidins,tmsins) select distinct asservimento,$idpratica,".$_SESSION["USER_ID"].",".time()." from pe.asservimenti_prat where pratica=$refid ";
//if(DEBUG) echo $sql;
$db->sql_query ($sql);


?>
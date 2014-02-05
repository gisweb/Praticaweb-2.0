<?
require_once("nusoap.php");
//require_once "../../../config/config.php";
define ('PROJECT', 'centrale_operativa'); //admin_aster:admin_aster
define('NAME_SPACE', 'http://'.$_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'].'?wsdl');
define('RETURN_OK','OK');
define('TBL_PAGELLE','base.pagelle');
define('TBL_VIE','stradario.bc_vie');
define('TBL_CIVICI','stradario.bc_civici');
define('SRS_3003','+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68');
define('SRS_4326','+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs');

//Test site
//http://www.soapclient.com/soaptest.html

//WSDL
//http://93.62.223.146/gisclient/30/services/iride/serverPagelle.php?wsdl



$dbData = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);

$server = new soap_server;
$server->debug_flag=false;

$server->configureWSDL('Web_Service_Pagelle', NAME_SPACE);
$server->wsdl->schemaTargetNamespace = NAME_SPACE; 

//Definizione dell'oggetto Pagella
$server->wsdl->addComplexType(
	'Pagella',
	'complexType',
	'struct',
	'all',
	'',
	array( // N.B. in NuSOAP il NAME_SPACE per i tipi base è xsd, in esempi precedenti noi avevamo usato xs
	'codice' =>array('name'=>'codice','type'=>'xsd:string'),
	'nome' =>array('name'=>'nome','type'=>'xsd:string'),
	'stato' =>array('name'=>'stato','type'=>'xsd:int'),
	'lon' =>array('name'=>'lon','type'=>'xsd:decimal'),
	'lat' =>array('name'=>'lat','type'=>'xsd:decimal')
	)
);

//Definizione della collezione di Pagelle
$server->wsdl->addComplexType(
	'Pagelle',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(
		array('ref'=>'SOAP-ENC:arrayType',
		'wsdl:arrayType'=>'tns:Pagella[]')
	),
	'tns:Pagella'
);

//Definizione dell'oggetto Via
$server->wsdl->addComplexType(
	'Via',
	'complexType',
	'struct',
	'all',
	'',
	array( // N.B. in NuSOAP il NAME_SPACE per i tipi base è xsd, in esempi precedenti noi avevamo usato xs
	'codice' =>array('name'=>'codice','type'=>'xsd:int'),
	'nome' =>array('name'=>'nome','type'=>'xsd:string'),	
	'comune' =>array('name'=>'comune','type'=>'xsd:string'),
	'codice_amga' =>array('name'=>'codice_amga','type'=>'xsd:int'),
	'extent' =>array('name'=>'extent','type'=>'xsd:string')
	)
);

//Definizione della collezione di Vie
$server->wsdl->addComplexType(
	'Vie',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(
		array('ref'=>'SOAP-ENC:arrayType',
		'wsdl:arrayType'=>'tns:Via[]')
	),
	'tns:Via'
);

//Definizione dell'oggetto Posizione
$server->wsdl->addComplexType(
	'Posizione',
	'complexType',
	'struct',
	'all',
	'',
	array( 
	'lon' =>array('name'=>'lon','type'=>'xsd:decimal'),	
	'lat' =>array('name'=>'lat','type'=>'xsd:decimal')
	)
);


//Gestione Pagelle:
$server->register( 
	'aggiungiPagella',
	array('codice'=>'xsd:string','nome'=>'xsd:string','stato'=>'xsd:int','lat'=>'xsd:decimal','lon'=>'xsd:decimal','replace'=>'xsd:int'),
	array('return'=>'xsd:string'), 
	NAME_SPACE, 
	NAME_SPACE.'#aggiungiPagella',
	'rpc',
	'encoded',
	'<br>Il metodo aggiunge un oggetto di tipo Pagella.<br>
	<ul>Parametri:
		<li>codice: stringa</li>
		<li>nome: stringa (etichetta in mappa)</li>
		<li>stato: intero (stato della pagella da concordare ora può assumere i valori 1,2,3)</li>
		<li>longitudine:in gradi sessadecimali</li>		
		<li>latitudine: in gradi sessadecimali</li>
		<li>replace: flag (opzionale) se settato a 1 forza la sostituzione di una pagella esistente</li>
	</ul>
	Restituisce l\'eventuale errore nell\'esecuzione della query oppure '.RETURN_OK
);

$server->register( 
	'rimuoviPagella',
	array('codice'=>'xsd:string'),
	array('return'=>'xsd:string'), 
	NAME_SPACE, 
	NAME_SPACE.'#rimuoviPagella',
	'rpc',
	'encoded',
	'<br>Il metodo elimina l\'oggetto Pagella dato il suo codice.<br>
	ATTENZIONE per eliminare tutte la pagelle usare codice = *.<br>
	Restituisce l\'eventuale errore nell\'esecuzione della query oppure '.RETURN_OK
);

$server->register( 
	'aggiungiElencoPagelle',
	array('pagelle'=>'tns:Pagelle'),
	array('return'=>'xsd:string'), 
	NAME_SPACE, 
	NAME_SPACE.'#aggiungiElencoPagelle',
	'rpc',
	'encoded',
	'<br>Aggiunge una lista di oggetti Pagella.<br>
	Restituisce l\'eventuale errore nell\'esecuzione della query oppure '.RETURN_OK
);

$server->register( 
	'esistePagella',
	array('codice'=>'xsd:string'),
	array('return'=>'xsd:string'), 
	NAME_SPACE, 
	NAME_SPACE.'#esistePagella',
	'rpc',
	'encoded',
	'<br>Verifica l\'esistenza di un oggetto di tipo Pagella dato il suo codice.<br>
	Restituisce 0 se non esiste, altrimenti 1'
);

$server->register( 
	'elencoVie',
	array('comune'=>'xsd:string'),
	array('return'=>'tns:Vie'), 
	NAME_SPACE, 
	NAME_SPACE.'#elencoVie',
	'rpc',
	'encoded',
	'<br>Restitisce l\'elenco delle vie (codice, nome, comune, codice amga, extent).<br>
	Accetta il parametro codice del comune da usare come filtro'
);

$server->register( 
	'posizioneVia',
	array('codice'=>'xsd:int'),
	array('return'=>'tns:Posizione'), 
	NAME_SPACE, 
	NAME_SPACE.'#posizioneVia',
	'rpc',
	'encoded',
	'<br>Restituisce le coordinate geografiche di un punto dato il codice della via'
);

$server->register( 
	'posizioneCivico',
	array('codice'=>'xsd:int','civico'=>'xsd:string'),
	array('return'=>'tns:Posizione'), 
	NAME_SPACE, 
	NAME_SPACE.'#posizioneCivico',
	'rpc',
	'encoded',
	'<br>Restituisce le coordinate geografiche di un punto dato il codice  della via e il numero civico(come rappresentato in mappa)'
);


$server->register('testlogin',
	array(),
	array('return' => 'xsd:string'),
	NAME_SPACE,
	NAME_SPACE.'#testlogin',
	'rpc',
	'encoded',
	'<br>Test login: il sistema usa l\'utente amministratore del progetto ' . PROJECT
);


$server->register( 
	'wgs2gb',
	array('posizione'=>'tns:Posizione'),
	array('return'=>'tns:Posizione'), 
	NAME_SPACE, 
	NAME_SPACE.'#wgs2gb',
	'rpc',
	'encoded',
	'<br>Restituisce le coordinate piane x,y in Roma 40 (Gauss-Boaga) dato un punto in coordinate geografiche longitudine, latitudine'
);


$server->register( 
	'gb2wgs',
	array('posizione'=>'tns:Posizione'),
	array('return'=>'tns:Posizione'), 
	NAME_SPACE, 
	NAME_SPACE.'#gb2wgs',
	'rpc',
	'encoded',
	'<br>Restituisce le coordinate geografiche longitudine, latitudine  dato un punto in coordinate piane Roma 40 (Gauss-Boaga)'
);


function aggiungiPagella($codice,$nome,$stato,$lat,$lon,$replace=0){
	
	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	
	if($replace==1) query("delete from ". TBL_PAGELLE ." where codice='$codice';");
	$lon=str_replace(",",".",$lon);
	$lat=str_replace(",",".",$lat);
	$point = "st_pointfromtext('POINT($lon $lat)',4326)";
	$nome=addslashes($nome);
	$sql="insert into ".TBL_PAGELLE."(codice,nome,stato,geom) values ('$codice','$nome',$stato,$point);";
	$aRet=query($sql);
	if($aRet["error"]) 
		return $aRet["error"];
	else
		return RETURN_OK;

}		


function rimuoviPagella($codice){

	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	
	$sql="delete from ".TBL_PAGELLE." where codice = '$codice';";
	if($codice=='*') $sql="delete from ".TBL_PAGELLE;
	$aRet=query($sql);
	if($aRet["error"]) 
		return $aRet["error"];
	else
		return RETURN_OK;


}


function aggiungiElencoPagelle($aPagelle){

	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	
	$sql="BEGIN;\n";
	foreach ($aPagelle as $pagella){
		$codice=$pagella["codice"];
		$nome=addslashes($pagella["nome"]);	
		$stato=$pagella["stato"];
		//$point = "st_pointfromtext('POINT(".$pagella["lon"]." ".$pagella["lat"].")',4326)";	
		$point = "st_pointfromtext('POINT(".str_replace(",",".",$pagella["lon"])." ".str_replace(",",".",$pagella["lat"]).")',4326)";	
		$sql.="insert into ".TBL_PAGELLE."(codice,nome,stato,geom) values ('$codice','$nome',$stato,$point);\n";
	}
	$sql=$sql."COMMIT;";
	$aRet=query($sql);
	if($aRet["error"]) 
		return $aRet["error"];
	else
		return RETURN_OK;

}
		

function esistePagella($codice){

	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	
	$sql="select * from ".TBL_PAGELLE." where codice = '$codice';";
	$aRet=query($sql);
	if($aRet["error"]) 
		return $aRet["error"];
	else{
		return pg_num_rows($aRet["result"]);	
	}
}
		
	
function elencoVie($comune=false){

	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);

	$sql="select codice,text as nome,cod_amga as codice_amga,comune,
		round(x(postgis_transform_geometry(setsrid(makepoint(split_part(split_part(coord,';',1),',',1)::numeric,split_part(split_part(coord,';',1),',',2)::numeric),3003),'+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68','+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs',4326))::numeric,4) ||','||
		round(y(postgis_transform_geometry(setsrid(makepoint(split_part(split_part(coord,';',1),',',1)::numeric,split_part(split_part(coord,';',1),',',2)::numeric),3003),'+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68','+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs',4326))::numeric,4) ||','||
		round(x(postgis_transform_geometry(setsrid(makepoint(split_part(split_part(coord,';',2),',',1)::numeric,split_part(split_part(coord,';',2),',',2)::numeric),3003),'+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68','+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs',4326))::numeric,4) ||','||
		round(y(postgis_transform_geometry(setsrid(makepoint(split_part(split_part(coord,';',2),',',1)::numeric,split_part(split_part(coord,';',2),',',2)::numeric),3003),'+proj=tmerc +lat_0=0 +lon_0=9 +k=0.999600 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs +towgs84=-104.1,-49.1,-9.9,0.971,-2.917,0.714,-11.68','+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs',4326))::numeric,4) as extent
		from ".TBL_VIE;
	
	if($comune) $sql.= " where comune = '$comune';";
	$aRet=query($sql);
	if($aRet["error"]) return new soapval('return', 'xsd:string', $aRet["error"]);

	return pg_fetch_all($aRet["result"]);
	
}

	
function posizioneVia($codVia){
	
	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);

	$sql="select round(x(postgis_transform_geometry(wkb_geometry,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lon, round(y(postgis_transform_geometry(wkb_geometry,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lat from stradario.bc_vie inner join  ".TBL_VIE."_tbl on (".TBL_VIE.".fid=".TBL_VIE."_tbl.fid_parent) where cod_amga = ".$codVia." limit 1;";
	$aRet=query($sql);
	if($aRet["error"]) return new soapval('return', 'xsd:string', $aRet["error"]);
	$s=pg_fetch_assoc($aRet["result"]);
	if(count($s)==0) return new soap_fault("Web_Service_Pagelle", "Errore");
	return $s;
	

}	
			
function posizioneCivico($codVia,$civico){

	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);

	$sql="select round(x(postgis_transform_geometry(wkb_geometry,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lon, round(y(postgis_transform_geometry(wkb_geometry,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lat
	from ".TBL_CIVICI." inner join  ".TBL_CIVICI."_tbl on (".TBL_CIVICI.".fid=".TBL_CIVICI."_tbl.fid_parent) inner join ".TBL_VIE." using(cod_amga) where ".TBL_VIE.".cod_amga = ".$codVia." and ".TBL_CIVICI.".text='$civico' limit 1;";

	$aRet=query($sql);
	if($aRet["error"]) return new soapval('return', 'xsd:string', $aRet["error"]);
	$s=pg_fetch_assoc($aRet["result"]);
	if(count($s)==0) return new soap_fault("Web_Service_Pagelle", "Errore");
	return $s;
	
}		
		
function wgs2gb($posizione){
	
	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	$point = "st_pointfromtext('POINT(".str_replace(",",".",$posizione["lon"])." ".str_replace(",",".",$posizione["lat"]).")',4326)";	
	//$point = "st_pointfromtext('POINT(".$posizione["lon"]." ".$posizione["lat"].")',4326)";	
	$sql="select round(x(postgis_transform_geometry($point,'".SRS_4326."','".SRS_3003."',3003))::numeric,2) as lon, round(y(postgis_transform_geometry($point,'".SRS_4326."','".SRS_3003."',3003))::numeric,2) as lat;";
	$aRet=query($sql);
	$s=pg_fetch_assoc($aRet["result"]);
	return $s;

}	

function gb2wgs($posizione){
	
	$loginString=login();
	if ($loginString!=RETURN_OK) return new soapval('return', 'xsd:string', $loginString);
	$point = "st_pointfromtext('POINT(".str_replace(",",".",$posizione["lon"])." ".str_replace(",",".",$posizione["lat"]).")',3003)";	
	//$point = "st_pointfromtext('POINT(".$posizione["lon"]." ".$posizione["lat"].")',3003)";	
	$sql="select round(x(postgis_transform_geometry($point,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lon, round(y(postgis_transform_geometry($point,'".SRS_3003."','".SRS_4326."',4326))::numeric,4) as lat;";
	$aRet=query($sql);
	$s=pg_fetch_assoc($aRet["result"]);
	return $s;

}		

//da vedere
//return new soap_fault("Server", ", ‘Service temporarily unavailable: could not connect to ADDR_DSN DB’,");
			
			
			




function testlogin() {
	$loginString=login();
	return $loginString ." DB NAME = " .DB_NAME . " DB SCHEMA = ".DB_SCHEMA;
}


function query ($sqlQuery) {
	$conn_string = "host=".DB_HOST." port=".DB_PORT." dbname=".DB_NAME." user=".DB_USER." password=".DB_PWD;
	if(!$dbconn = pg_connect($conn_string)){
		return "NON RIESCO A CONNETERMI AL DB " . DB_NAME;
	}
	$lev=error_reporting (8); //NO WARRING!!
	$result=pg_query ($sqlQuery);
	error_reporting ($lev); //DEFAULT!!
	if (strlen ($r=pg_last_error ($dbconn))) {
		$error=$sqlQuery."\n".$r;
		return array("error"=>$error,"result"=>false);
   }
   return array("error"=>false,"result"=>$result);
}


function login(){

return RETURN_OK;//bypass

	global $server;
	if($baseString = $server->headers["authorization"]){
		$v=explode(" ",$baseString);
		$authString = base64_decode($v[1]);
		$sql="select username from " . DB_SCHEMA . ".project_admin inner join " . DB_SCHEMA . ".users using (username) where username||':'||pwd = '" . $authString . "'";
		$aRet=query($sql);
		if(!is_array($aRet)) return $aRet;
		if($aRet["error"]) return "ERRORE NELLA QUERY";
		if($aRet["result"] && pg_num_rows($aRet["result"])==0) return "L'UTENTE NON PUO' ACCEDERE AL SERVIZIO";
		return RETURN_OK;
	}
	else
		return "MANCANO LE CREDENZIALI DI AUTENTICAZIONE";
	
}

//A questo punto non c'è più nulla da fare per noi... lasciamo fare a NuSOAP ;)
$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?$GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit(); // <-- a cosa serve qui!? Ma non avevamo detto di far fare tutto a NuSOAP!? Meglio esserne certi! Infatti se scappa qualche output (come un errore o un echo) nel bel mezzo dei messaggi 
// SOAP si incappa sicuramente in un errore di comunicazione




?>

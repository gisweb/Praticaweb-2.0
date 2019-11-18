<?php
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."login.php";
require_once LOCAL_LIB."wsprotocollo.class.php";

$ws = new protocollo();
$res = $ws->login();
if($res["success"]==1){
    $dst = $res["dst"];
}
else{
    die("<p><b style='color:red;font-size:15px;'>Impossibile effettuare il login al servizio</b></p>");
}
/*
$xmlDocumentInfo=<<<EOT
         <![CDATA[
<Segnatura xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<Intestazione>
		<Oggetto>Test Protocollazione ed Invio PEC</Oggetto>
		<Identificatore>
			<CodiceAmministrazione>18</CodiceAmministrazione>
			<CodiceAOO>1</CodiceAOO>
			<NumeroRegistrazione>0</NumeroRegistrazione>
			<DataRegistrazione>0</DataRegistrazione>
			<Flusso>U</Flusso>
		</Identificatore>
		<Mittente>
			<Amministrazione>
				<Denominazione>EDILIZIA PRIVATA</Denominazione>
				<CodiceAmministrazione>18</CodiceAmministrazione>
				<IndirizzoTelematico />
				<UnitaOrganizzativa id="18" />
			</Amministrazione>
			<AOO>         
				<CodiceAOO>1</CodiceAOO>
			</AOO>       
			<IndirizzoTelematico /> 
			<Classifica>
				<CodiceAmministrazione>18</CodiceAmministrazione>
				<CodiceAOO>1</CodiceAOO>
				<CodiceTitolario>6728</CodiceTitolario>
			</Classifica>
			
		</Mittente>
        <Destinatario>
			<Persona id="CNPGNN34H20G478C">
				<Nome>Giovanna</Nome>
				<Cognome>CANEPA</Cognome>
				<CodiceFiscale>CNPGNN34H20G478C</CodiceFiscale>
				<IndirizzoTelematico>(mail)s</IndirizzoTelematico>
			</Persona>  
		</Destinatario>

     
		<Classifica>
			<CodiceAmministrazione>18</CodiceAmministrazione>
			<CodiceAOO>1</CodiceAOO>
			<CodiceTitolario>6728</CodiceTitolario>
		</Classifica>
		
	</Intestazione>
	<Descrizione>
		<Documento nome="TEST PROTOCOLLAZIONE.pdf" id="719191">
			<DescrizioneDocumento>Documento Generico</DescrizioneDocumento>
			<TipoDocumento>LETTERA</TipoDocumento>
		</Documento>  
		<Allegati>

		</Allegati>   
	</Descrizione>   
</Segnatura>         
         ]]>      
EOT;
$xml =<<<EOT
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:Protocollazione>
         <!--Optional:-->
         <tem:strUserName>SERVIZIO PROTOCOLLO</tem:strUserName>
         <!--Optional:-->
         <tem:strDST>%s</tem:strDST>
         <!--Optional:-->
         <tem:strDocumentInfo>
%s
	    </tem:strDocumentInfo>
      </tem:Protocollazione>
   </soapenv:Body>
</soapenv:Envelope>
EOT;

$xml = sprintf($xml,$dst,$xmlDocumentInfo);
$action = SERVICE_URL."/Protocollazione";
$url = str_replace('?wsdl','',SERVICE_URL,$xml);

//$res = $ws->curlSoapCall($url,$action,$xml);
//utils::debugAdmin($res);
//die();
*/
//error_reporting(E_ALL);

/*
$pr = new pratica($pratica);
$dbh = utils::getDb();
$sqlSogg = sprintf("SELECT nome,cognome,codfis,coalesce(coalesce(pec,email),'') as mail FROM pe.soggetti WHERE id in (%s) and pratica=?;",$dest);
$sqlAll = "SELECT A.id,nome_file as nome_documento,coalesce(B.nome,'Documento Generico') as descrizione_documento,'LETTERA' as tipo_documento FROM pe.file_allegati A LEFT JOIN pe.e_documenti B ON(A.tipo_allegato=B.id) WHERE pratica=?";


$stmt = $dbh->prepare($sqlSogg);
if($stmt->execute(Array($pratica))){
    $destinatari = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//echo "<pre>";print $destinatari;echo "</pre>";
$stmt = $dbh->prepare($sqlAll);
if($stmt->execute(Array($pratica))){
    $allegati = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i=0;$i<count($allegati);$i++){
        $res = $pr->leggiDocumento($allegati[$i]["id"],'allegato');
        $allegati[$i]["file"] = $res["contenuto"];
    }
}
else{
    print_r($stmt->errorInfo());
}
*/



$pratica= 9637;
$idCom = 6;
$res = appUtils::getComunicazione($idCom);
if ($res["success"]==1)
{
	$com = $res["comunicazione"];
	$destinatari = $com["persone"];
	for($i=0;$i<count($com["attachments"]);$i++){
		$allegati[]=Array(
			"id"=>$com["attachments"][$i]["id"],
			"nome_documento"=>$com["attachments"][$i]["name"],
			"descrizione_documento"=>"Documento Generico",
			"tipo_documento"=>"LETTERA",
			"file"=>$com["attachments"][$i]["file"]
		);
	}
}	

$mittente=Array(
    Array(
        "codice_amministrazione"=>CODICE_AMMINISTRAZIONE,
        "codice_a00"=>CODICE_A00,
        "codice_titolario"=>CODICE_TITOLARIO,
        "codice_uo"=>CODICE_UO,
        "denominazione_amministrazione"=>DENOMINAZIONE
    )
);



$ws = new protocollo();
$r = $ws->protocolla("U",$com["subject"],$mittente,$destinatari,$allegati);



/*$res = $ws->login();
$dst = $res["dst"];
*/
utils::debugAdmin($r);
?>

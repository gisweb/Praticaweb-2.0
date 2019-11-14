<?php
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."login.php";
require_once LOCAL_LIB."wsprotocollo.class.php";

//error_reporting(E_ALL);

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
//
$ws = new protocollo();
$r = $ws->protocolla("U",$com["subject"],$mittente,$destinatari,$allegati);
/*$res = $ws->login();
$dst = $res["dst"];
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

$xml = sprintf($xml,$dst);*/
utils::debugAdmin($r);
?>

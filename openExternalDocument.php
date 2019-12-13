<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "login.php";

$type = $_REQUEST["type"];

if($type=="ADS"){
    require_once DATA_DIR."config.ads.php";
    $soapUrl = WSDL_DOWNLOAD; // asmx URL of WSDL
    $soapUser = SERVICE_USER;  //  username
    $soapPassword = SERVICE_PASSWD; // password
    $idDocumento = $_REQUEST["idDocumento"];
    $idObjFile = $_REQUEST["idObjFile"];
    $filename = $_REQUEST["filename"];
    $xml_post_string =<<<EOT
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.wscxf.dmServer.finmatica.it/">
   <soapenv:Header/>
   <soapenv:Body>
      <ser:downloadAttach>
         <!--Optional:-->
         <idDocumento>$idDocumento</idDocumento>
         <!--Optional:-->
         <idObjFile>$idObjFile</idObjFile>
         <!--Optional:-->
         <fileName>$filename</fileName>
         <!--Optional:-->
         <utenteApplicativo>AGSPRWS</utenteApplicativo>
      </ser:downloadAttach>
   </soapenv:Body>
</soapenv:Envelope>
EOT;

    $headers = array(
        "Content-type: application/xop+xml; charset=UTF-8; type=\"text/xml\"",
        "",
    //                        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: DownloadAttach", 
        "Authorization: Basic ".base64_encode(SERVICE_USER.":".SERVICE_PASSWD),
        "Content-length: ".strlen($xml_post_string),
    ); //SOAPAction: your op URL

    $url = $soapUrl;

    // PHP cURL  for https connection with auth
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //            curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
    //            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch); 
    curl_close($ch);
    
    preg_match("|--uuid:([A-z0-9\-]+)--|",$response,$matches);
    $uuid = $matches[1];
    $end = "--uuid:$uuid--";
    preg_match("|href=\"cid:(.+)\"|",$response,$result);
    $cid = $result[1];
    $start = "Content-ID: <".$cid.">";
    $posStart = strpos($response, $start);
    $end = "--uuid:$uuid--";
    $posEnd = strpos($response, $end);


 
    $regExp = sprintf("/Content-ID: <%s>([\s\S]*?)--uuid/",$cid);
    //$regExp = sprintf("/Content-ID: <%s>(.+)--uuid/",$cid);
    preg_match($regExp, $response, $binary);
    if (!count($binary)){
        $binary= Array("", substr($response,$posStart,$posEnd-$posStart));
    }
    if (!count($binary)){
        $file = trim(substr($response,$posStart,$posEnd-$posStart));
        $message =<<<EOT
<DIV class="ui-state-error ui-corner-all page-message">
    Si &egrave; verificato un errore nel recupero del documento $filename
</DIV>
EOT;
        utils::printMessage($message);
        utils::debug(sprintf(DEBUG_DIR."ERRORE-PRISMA-%s.debug",$idObjFile),$response,'w');
        return;
    }
    else{
        $path_parts = pathinfo($filename);
        if ($path_parts['extension']=='p7m'){
            $mime="application/pkcs7-mime";
        }
        else{
            $mime= mime_content_type($filename);
        }
        header("Content-Disposition: inline; filename=\"$filename\"");
        header("Content-type: $mime");
        print ltrim($binary[1]);
    }
}
else{
    $message =<<<EOT
<DIV class="ui-state-error ui-corner-all page-message">
    Nessun Parametro "type" inserito
</DIV>
EOT;
    utils::printMessage($message);
    return;

}
 

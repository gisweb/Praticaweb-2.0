<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class generalWSProtocollo{
    function __contruct(){
        
    }
    private function subst($txt,$data){
        foreach($data as $k=>$v){
            $txt = str_replace("($k)s",$v,$txt);
        }
        return $txt;
    }
    function caricaXML($nome,$data){
        $result = $this->result;
        $fName = TEMPLATE_DIR.$nome.".xml";
        if (file_exists($fName)){
            $f = fopen($fName,'r');
            $tXml = fread($f,filesize($fName));
            fclose($f);
            $xml = $this->subst($tXml,$data);
            $result["success"] = 1;
            $result["result"] = $xml;
            return Array("success"=>1,"result"=>$xml);
        }
        else{
            $result["success"] = -1;
            $result["message"] = "Il file $fName non è stato trovato";
        }
        return $result;
    }
    function curlSoapCall($service,$action,$soap_request,$headers=Array()){
        $header = array(
          "Content-type: text/xml;charset=\"utf-8\"",
          "Accept-Encoding: gzip,deflate",
          "Cache-Control: no-cache",
          "Pragma: no-cache",
          "SOAPAction: \"$action\"",
          "Content-length: ".strlen($soap_request),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $service );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST,           true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $soap_request);
        

        if(!$result = curl_exec($ch)) {
          $err = 'Curl error: ' . curl_error($ch);
          curl_close($ch);
          return Array("success"=>0,"result"=>$err);
        } else {
          curl_close($ch);
		  //$res = simplexml_load_string($result);
		  //utils::debugAdmin($soap_request);
		  //$data = json_decode(json_encode($res),TRUE);
          return Array("success"=>1,"result"=>$result);
        }
    }

}
?>
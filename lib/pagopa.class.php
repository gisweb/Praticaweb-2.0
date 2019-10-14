<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class pagopa{
    const actionRead = "iol-GetElencoImportiPagamenti";
    const actionSet = "";
    const user = "marco.carbone@gisweb.it";
    const passwd = "pipino";
    static function readPagamenti($pratica){
        $sql = "SELECT * FROM pe.istanze WHERE pratica=?";
        $dbh = utils::getDb();
        $stmt = $dbh->prepare($sql);
        if($stmt->execute(Array($pratica))){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i<count($res);$i++){
                $url = sprintf("%s/%s",$res[$i]["url"],self::actionRead);
                //$url = "https://www.istanze.spezianet.it/iol_sp/04028-2019-dehor/iol-GetElencoImportiPagamenti";


                $headers = array(
                    "Content-type: text/json;charset=\"utf-8\"",
                    //"Accept: text/json",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Authorization: Basic ".base64_encode(self::user.":".self::passwd),
                ); //SOAPAction: your op URL

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
                //            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POST, true);
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                // converting
                $response = curl_exec($ch); 
                curl_close($ch);
                if ($response){
                    $res = json_decode($response,1);
                    if(!$res){
                        $jsonErr = json_last_error();
                        utils::json_error($jsonErr);
                        return Array();
                    }
                    else 
                        return $res;
                }
                else{
                    return Array();
                }
            }
        }
    }
    
    static function setPagamenti($pratica,$codice){
        
    }
}


?>
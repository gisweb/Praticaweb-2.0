<?php
if (!defined('DATA_DIR')) define("DATA_DIR",dirname(dirname(dirname(__FILE__))));
require_once DATA_DIR.DIRECTORY_SEPARATOR."config.protocollo.php";
require_once LOCAL_LIB."app.utils.class.php";
require_once LIB."utils.class.php";
require_once LIB."protocollo.class.php";
require_once LIB."nusoap".DIRECTORY_SEPARATOR."nusoap.php";;

class HProtocollo extends generalWSProtocollo{
    var $mittente = Array(
        "codice_amministrazione"=>CODICE_AMMINISTRAZIONE,
        "codice_a00"=>CODICE_A00,
        "codice_titolario"=>CODICE_TITOLARIO,
        "codice_uo"=>CODICE_UO,
        "denominazione_amministrazione"=>DENOMINAZIONE
    );
	
	var $destinatario = Array(
        "codice_amministrazione"=>CODICE_AMMINISTRAZIONE,
        "codice_a00"=>CODICE_A00,
        "codice_titolario"=>CODICE_TITOLARIO,
        "codice_uo"=>CODICE_UO,
        "denominazione_amministrazione"=>DENOMINAZIONE
    );
	
    function login(){
        $cl = new SoapClient(SERVICE_URL,array("trace" => 1, "exception" => 0));
        $res = $cl->Login(Array("strCodEnte"=>CODICE_AMMINISTRAZIONE,"strUserName"=>SERVICE_USER,"strPassword"=>SERVICE_PASSWD));
        $res = json_decode(json_encode($res),TRUE);
        
        if(array_key_exists("LoginResult",$res)){
            if(array_key_exists("lngErrNumber", $res["LoginResult"]) && !$res["LoginResult"]["lngErrNumber"]){
                return Array("success"=>1,"dst"=>$res["LoginResult"]["strDST"]);
            }
            else{
                return Array("success"=>0,"message"=>$res["LoginResult"]["strErrString"]);
            }
        }
        else{
            return Array("success"=>-1);
        }
        unset($cl);
    }
    
    function cercaFascicolo($prot,$anno){
        $res = $this->login();
        if ($res["success"]===1){
            $dst = $res["dst"];
            $cl = new SoapClient(DIZIONARI_URL,array("trace" => 1, "exception" => 0));
            $result = $cl->searchFascicoli(Array("strUserName"=>SERVICE_USER,"strDST"=>$dst,"codiceAOO"=>Codice_A00,"numeroProtocollo"=>$prot,"annoProtocollo"=>$anno));
            return $result;
        }
    }

    function listaFascicoli(){
        $res = $this->login();
        if ($res["success"]===1){
            $dst = $res["dst"];
            $cl = new SoapClient(DIZIONARI_URL,array("trace" => 1, "exception" => 0));
            $result = $cl->listaFascicoli(Array("strUserName"=>SERVICE_USER,"strDST"=>$dst));
            return $result;
        }
    }
    
    function protocolla($mode='U',$oggetto,$mittente = Array(),$destinatari=Array(),$allegati=Array()){
		
		if($mode=='TEST') return Array("success"=>1,"message"=>"","protocollo"=>rand(22300,22600),"anno"=>'2019',"data"=>date('d/m/Y',time()));
        
		$xmlData = "";
        $res = $this->login();
        if ($res["success"]===1){
            $dst = $res["dst"];
            $this->dst=$res["dst"];
        }
        else{
            return -1;
        }
        $suffix = ($mode=='U')?("OUT"):("IN");
        $this->data["oggetto"] = $oggetto;
		$clientDocs = new SoapClient(
            SERVICE_URL, 
            array(
                'trace' => true, 
                'exceptions' => true,
                'keep_alive' => true,
                'connection_timeout' => 30,
                'cache_wsdl' => WSDL_CACHE_NONE
            )
        );
        if(count($allegati)>0){
            for($i=0;$i<count($allegati);$i++){
				$parm = array();
				$parm[] = new SoapVar(SERVICE_USER, XSD_STRING, null, null, 'strUserName' );
				$parm[] = new SoapVar($dst, XSD_STRING, null, null, 'strDST' );
				$parm[] = new SoapVar($allegati[$i]["nome_documento"], XSD_STRING, null, null, 'strDocument' );
				$parm[] = new SoapVar(base64_encode($allegati[$i]["file"]), XSD_BASE64BINARY, null, null, 'objDocument' );
				$res = $clientDocs->Inserimento(new SoapVar($parm, SOAP_ENC_OBJECT,null,null,'Inserimento'));
			
				$res = json_decode(json_encode($res->InserimentoResult),true);
				//DEBUG DELL'INSERIMENTO DEL FILE
				//utils::debug(DEBUG_DIR."FILE_PROTOCOLLO.debug",$res,'w');				
                if($res["lngDocID"]){
                    $allegato = $allegati[$i];
                    $allegato["id_documento"] = $res["lngDocID"];
                    $resAllegati[] = $allegato;
                }
				else{
                    utils::debug(DEBUG_DIR."ERRORE_FILE_PROTOCOLLO.debug",$res,'w');
					return Array("success"=>0,"message"=>sprintf("Errore Numero %s nell'inserimento del file %s - %s",$res["lngErrNumber"],$allegati[$i]["nome_documento"],$res["strErrString"]));
				}
            }
            utils::debug(DEBUG_DIR."FILE_PROTOCOLLO.debug",$resAllegati,'w');
            $allegato = array_shift($resAllegati);
            $this->data = array_merge($this->data,$allegato);
            for($i=0;$i<count($resAllegati);$i++){
                $res = $this->caricaXML("DOCUMENTO",$resAllegati[$i]);
                if($res["success"]==1){
                    $this->data["altri_documenti"].=$res["result"];
                }
            }
            if (count($resAllegati)){
                $xmlAltriAllegati =<<<EOT
        <Allegati>
%s            
        </Allegati>
EOT;
                $this->data["altri_documenti"] = sprintf($xmlAltriAllegati,$this->data["altri_documenti"]);
            }
            else{
                $this->data["altri_documenti"] = "<Allegati/>";
            }
        }

        for($i=0;$i<count($mittente);$i++){
            $res = $this->caricaXML("MITTENTE-".$suffix,$mittente[$i]);
            if($res["success"]==1){
                $this->data["mittente"].=$res["result"];
            }
        }
        for($i=0;$i<count($destinatari);$i++){
            $res = $this->caricaXML("DESTINATARIO-".$suffix,$destinatari[$i]);
            if($res["success"]==1){
                $this->data["destinatari"].=$res["result"];
            }
        }
        $res = $this->caricaXML("PROT-".$suffix,$this->data);
        if($res["success"]==1){
            $xmlData=$res["result"];
			utils::debug(DEBUG_DIR."XML_PROTOCOLLO.debug",$xmlData,'w');
			$res = $this->login();
            if ($res["success"]===1){
                $dst = $res["dst"];
                $this->dst=$res["dst"];
            }
            else{
                return -1;
            }
			$parm = array();
			$parm[] = new SoapVar(SERVICE_USER, XSD_STRING, null, null, 'ns1:strUserName' );
			$parm[] = new SoapVar($dst, XSD_STRING, null, null, 'ns1:strDST' );
			$parm[] = new SoapVar($xmlData, XSD_ANYXML, null, null, 'ns1:strDocumentInfo' );
			
			$soapVarUser = new SoapVar(SERVICE_USER, XSD_STRING, null, null, 'ns1:strUserName' );
			$soapVarDst = new SoapVar($dst, XSD_STRING, null, null, 'ns1:strDST' );
			$soapVarXml = new SoapVar($xmlData, XSD_ANYXML, null, null, 'ns1:strDocumentInfo' );
			// MODO 1
			try{
				//$res = $clientDocs->Protocollazione(new SoapVar($parm,SOAP_ENC_OBJECT,null,null,'Protocollazione'));
				//$res = $clientDocs->Protocollazione(Array("strUserName"=>$soapVarUser,"strDST"=>$soapVarDst,"strDocumentInfo"=>$soapVarXml));
                $res = $clientDocs->Protocollazione(Array(SERVICE_USER,$dst,$xmlData));
				//$res = $clientDocs->__soapCall('Protocollazione',Array("strUserName"=>$soapVarUser,"strDST"=>$soapVarDst,"strDocumentInfo"=>$soapVarXml));
				//utils::debugAdmin($clientDocs->__getLastRequest());
                //
                //$client = new soapclient(SERVICE_URL);
                //$response = $client->call('Protocollazione', Array(SERVICE_USER,$dst,$xmlData));
				//return;
			}
			catch (Exception $e){
				utils::debugAdmin($clientDocs->__getLastRequest());
				utils::debugAdmin($e);
				return;
			}
			//MODO 2
			/*
			try{
				$postData = Array("Protocollazione"=>Array("strUserName"=>SERVICE_USER,"strDST"=>$dst,"strDocumentInfo"=>$xmlData));
				$res = $clientDocs->Protocollazione($postData);
				utils::debugAdmin($clientDocs->__getLastRequest());
			}
			catch (Exception $e){
				//utils::debugAdmin($e);
				utils::debugAdmin($clientDocs->__getLastRequest());
				return;
			}
			*/
			$res = json_decode(json_encode($res->ProtocollazioneResult),true);
			
			if(!$res["lngNumPG"]){
				return Array("success"=>0,"message"=>sprintf("Errore durante la protocollazione numero %s - %s",$res["lngErrNumber"],$res["strErrString"]));
			}
			else{
				$message = ($res["lngErrNumber"])?(sprintf("Errore durante la protocollazione numero %s - %s",$res["lngErrNumber"],$res["strErrString"])):("");
				return Array("success"=>1,"message"=>"","protocollo"=>$res["lngNumPG"],"anno"=>$res["lngAnnoPG"],"data"=>$res["strDataPG"]);
			}
        }
        else{
			return Array("success"=>0,"message"=>"Errore nella creazione dell'XML per la protocollazione");
		}
		
		return $xmlData;
		
    }
    
}
?>

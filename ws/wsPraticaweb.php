<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function loadLibs(){
    $libs=Array("pratica.class.php","app.utils.class.php","utils.class.php","menu.class.php","mail.class.php");
    foreach($libs as $lib){

        if (file_exists(LOCAL_LIB.$lib)){
            require_once LOCAL_LIB.$lib;
        }
        elseif(file_exists(APPS_DIR."lib".DIRECTORY_SEPARATOR.$lib)) {
            require_once LIB.$lib;
        }
        else die("impossibile caricare la libreria $lib");
    }
};

function findPratica($pratica){
    $sql = "SELECT count(*) as found FROM pe.avvioproc WHERE pratica=?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if($stmt->execute(Array($pratica))){
        $found = $stmt->fetchColumn();
        return $found;
    }
    else 
        return -1;
}

function findDocumento($id,$pratica){
    $sql = "SELECT count(*) as found FROM stp.stampe WHERE pratica=? AND id = ?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if($stmt->execute(Array($pratica,$id))){
        $found = $stmt->fetchColumn();
        return $found;
    }
    else 
        return -1;
}

function findAllegato($id,$pratica){
    $sql = "SELECT count(*) as found FROM pe.file_allegati WHERE pratica=? AND id = ?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if($stmt->execute(Array($pratica,$id))){
        $found = $stmt->fetchColumn();
        return $found;
    }
    else 
        return -1;
}

define('SERVICE_URL','');
$appsDir=  getenv('PWAppsDir');
$dataDir=  getenv('PWDataDir');
if (!$dataDir) die("Manca la variabile d'ambiente PWDataDir nel file di configurazione di Apache.");
if (!$appsDir) die("Manca la variabile d'ambiente PWAppsDir nel file di configurazione di Apache.");
define('DATA_DIR',$dataDir);
define('APPS_DIR',$appsDir);



if (!file_exists(DATA_DIR.'config.php')) die("Nessun file di configurazione trovato!");
require_once DATA_DIR.'config.php';

require_once LIB."/nusoap/nusoap.php";
loadLibs();
$server = new nusoap_server; 
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->configureWSDL('praticaweb', SERVICE_URL);


$server->register('trovaProcedimento',
    Array(
        "protocollo" => "xsd:string",
        "anno" => "xsd:string"
    ),
    Array(
        "success" => "xsd:int",
        "message" => "xsd:string",
        "result" => "xsd:string"
    ),
    'urn:praticaweb',
    'urn:praticaweb#trovaProcedimento',
    'rpc',
    'encoded',
    "Metodo che dato il protocollo e l'anno di una pratica restituisce il suo identificativo unico"
);

$server->register('leggiDocumento',
    Array(
        "pratica"=>"xsd:int",
        "id"=>"xsd:int",
        "tipo" => "xsd:string"
    ),
    Array(
        "success" => "xsd:string",
        "message" => "xsd:string",
        "result"=>"xsd:string"
        
    ),
    'urn:praticaweb',
    'urn:praticaweb#leggiDocumento',
    'rpc',
    'encoded',
    "Metodo che dato l'identificativo unico della pratica e l'id del documento restituisce il suo contenuto in base64"
);

$server->register('scriviDocumento',
    Array(
        "pratica"=>"xsd:int",
        "id"=>"xsd:int",
        "tipo" => "xsd:string",
        "nome" => "xsd:string",
        "documento" => "xsd:string"
    ),
    Array(
        "success" => "xsd:string",
        "message" => "xsd:string",
        "result" => "xsd:string"
    ),
    'urn:praticaweb',
    'urn:praticaweb#leggiDocumento',
    'rpc',
    'encoded',
    "Metodo che scrive un documento su praticaweb, in input vuole:<ul><li>\"pratica\" : identificativo unico della pratica</li><li>\"id\" : identificativo unico del documento (Se vuoto aggiuger&agrave; questo documento altrimenti lo sostituir&agrave;)</li><li>\"tipo\" : che pu&ograve; assumenre i valori documento (1) o allegato (2)</li><li>\"nome\" : Nuovo nome del documento</li><li>\"documento\" : Base64 del file</li></ul>"
);

function trovaProcedimento($prot,$anno){
    $dbh = utils::getDb();
    $result = Array(
        "success" => 0,
        "message" => "",
        "result" => ""
    );
    $sql = "SELECT DISTINCT pratica FROM pe.avvioproc WHERE protocollo = ? and date_part('year',data_prot) = ?;";
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute(Array($prot,$anno))){
        $err= $stmt->errorInfo();
        $result["success"] = -1;
        $result["message"] = "Si è verificato un errore SQL";
    }
    else{
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $pratica = $res["pratica"];
        if ($pratica){
            $result["success"] = 1;
            $result["result"] = $pratica;
        }
        else{
            $result["success"] = -2;
            $result["message"] = sprintf("Nessuna pratica trovata con protocollo %s nel %s",$prot,$anno);
        }
    }
    return $result;
}

function leggiDocumento($pratica,$id,$tipo){
    $result = Array(
        "success" => 0,
        "message" => "",
        "result" => ""
    );
    $pr = new pratica($pratica);
    $baseDir = ($tipo == 1)?($pr->documenti):($pr->allegati);
    $sql = ($tipo == 1) ? ("SELECT file_doc as nomefile FROM stp.stampe WHERE id=? and pratica=?") : ("SELECT nome_file as nomefile FROM pe.file_allegati WHERE id=? and pratica=?");
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute(Array($id,$pratica))){
        $err= $stmt->errorInfo();
        $result["success"] = -1;
        $result["message"] = $err[0];
    }
    else{
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $filename = $res["nomefile"];
        if ($filename){
            $ffname = $baseDir.$filename;
            if (file_exists($baseDir.$filename)){
                $f = fopen($baseDir.$filename,'r');
                $text = fread($f,filesize($baseDir.$filename));
                fclose($f);
                $result["success"] = 1;
                $result["result"] = base64_encode($text);
            }
            else{
                $result["success"] = -3;
                $result["result"] = "Il file selezionato non si trova nella posizione specificata.";
            }
            
        }
        else{
            $result["success"] = -2;
            $result["message"] = sprintf("Nessun documento trovato con id %s sulla pratica %s",$id,$pratica);
        }
    }
    
    return $result;
}

function scriviDocumento($pratica,$id,$tipo,$nome,$documento){
    $result = Array(
        "success" => 0,
        "message" => "",
        "result" => ""
    );
    /*Verifico se il file ha un nome*/
    if (!$nome){
        return Array(
            "success" => -1,
            "message" => "Nessun nome di file specificato",
            "result" => ""
        );
    }
    /*Verifico se il file è stato inviato*/   
    if (!$documento){
        return Array(
            "success" => -1,
            "message" => "Nessun file inviato",
            "result" => ""
        );
    }
    else{
        $text = base64_decode($documento);
        /*Verifico se il file è stato codificato in Base64*/
        if($text === FALSE){
            return Array(
                "success" => -1,
                "message" => "Errore nella decodifica del file inviato",
                "result" => ""
            );
        }
    }
    /*Verifico se la pratica esiste*/
    $res = findPratica($pratica);
    if (!$res) {
        return Array(
            "success" => -1,
            "message" => "Non esiste nessuna pratica con questo identificativo : $pratica",
            "result" => ""
        );
    }
    
    /* Devo aggiornare il record della tabella */
    if($id){
        $res = ($tipo==1)?(findDocumento($id, $pratica)):(findAllegato($id,$pratica));
        if (!$res) {
            return Array(
                "success" => -1,
                "message" => "Non esiste nessun documento con identificativo $id associato alla pratica $pratica",
                "result" => ""
            );
        }
        
    }
    /*End Check*/
    
    $pr = new pratica($pratica);
    $baseDir = ($tipo == 1)?($pr->documenti):($pr->allegati);
    $baseDir = $pr->documenti;
    $fName = $baseDir.DIRECTORY_SEPARATOR.$nome;
    $f = fopen($fName,'w');
    /*Verifico se riesco a scrivere il file*/    
    if (!fwrite($f,$text)){
        fclose($f);
        return Array(
            "success" => -1,
            "message" => "Impossibile scrivere il file $nome",
            "result" => ""
        );
    }
    fclose($f);
    $baseDir = $pr->allegati;
    $fName = $baseDir.DIRECTORY_SEPARATOR.$nome;
    $f = fopen($fName,'w');
    /*Verifico se riesco a scrivere il file*/
    if (!fwrite($f,$text)){
        fclose($f);
        return Array(
            "success" => -1,
            "message" => "Impossibile scrivere il file $nome",
            "result" => ""
        );
    }
    fclose($f);
    
    $sql = "INSERT INTO stp.stampe(pratica,file_doc,data_creazione_doc,firma_digitale) VALUES(?,?,?,?)";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute(Array($pratica,$nome,date('d/m/Y'),1))){
        $err= $stmt->errorInfo();
        $result["success"] = -1;
        $result["message"] = "Si è verificato un errore SQL";
    }
    else{
        
        $result["success"] = 1;
        $result["result"] = $dbh->lastInsertId('stp.stampe_id_seq');
            
    }
    return $result;
}
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
//utils::debug("../debug/PIPPO.debug", $HTTP_RAW_POST_DATA);
$server->service($HTTP_RAW_POST_DATA);
?>

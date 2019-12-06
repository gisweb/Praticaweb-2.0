<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ERROR);

function trovaPratica($pratica){
    $sql = "SELECT * FROM pe.avvioproc WHERE pratica=?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute(Array($pratica))){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res)){
            return 1;
        }
        else{
            return 0;
        }
    }
    else{
        $errors = $stmt->errorInfo();
        $err = $errors[2];
        return -1;
    }
}
function trovaAllegati($pratica){
    $tipo=2;
    $result = Array("success"=>0,"totali"=>0,"file"=>Array(),"messages"=>Array());
    $sql = "SELECT * FROM pe.file_allegati WHERE pratica=?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute(Array($pratica))){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res)){
            for($i=0;$i<count($res);$i++){
                $doc = $res[$i];
                $id = $doc["id"];
                $infoDoc = appUtils::getDocumento($id,$pratia,$tipo);
                if ($infoDoc["success"]==1){
                    $result["$file"][] = Array(
                        "tipo"=>"allegato",
                        "data"=>"",
                        "protocollo"=>$doc["prot_allegato"],
                        "data_protocollo"=>$doc["data_prot_allegato"],
                        "note"=>$doc["note"],
                        "utente"=>"",
                        "name" => $infoDoc["name"],
                        "file"=>$infoDoc["file"]
                    );
                }
            }
        }
        else{
            $result["success"]=1;
            $result["messages"]=Array("Nessun documento trovato per la pratica");
        }
    }
    else{
        $errors = $stmt->errorInfo();
        $err = $errors[2];
        $result["messages"]=Array($err);
    }
}
function trovaDocumenti($pratica){
    $tipo=1;
    $result = Array("success"=>0,"totali"=>0,"file"=>Array(),"messages"=>Array());
    $sql = "SELECT * FROM stp.stampe WHERE pratica=?";
    $dbh = utils::getDb();
    $stmt = $dbh->prepare($sql);
    if ($stmt->execute(Array($pratica))){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res)){
            for($i=0;$i<count($res);$i++){
                $doc = $res[$i];
                $id = $doc["id"];
                $infoDoc = appUtils::getDocumento($id,$pratia,$tipo);
                if ($infoDoc["success"]==1){
                    $result["$file"][] = Array(
                        "tipo"=>"documento",
                        "data"=>$doc["data_creazione"],
                        "protocollo"=>$doc["protocollo"],
                        "data_protocollo"=>"",
                        "note"=>$doc["descrizione"],
                        "utente"=>$doc["utente_doc"],
                        "name" => $infoDoc["name"],
                        "file"=>$infoDoc["file"]
                    );
                }
            }
        }
        else{
            $result["success"]=1;
            $result["messages"]=Array("Nessun documento trovato per la pratica");
        }
    }
    else{
        $errors = $stmt->errorInfo();
        $err = $errors[2];
        $result["messages"]=Array($err);
    }
}
$_SESSION["USER_ID"] = 1;
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."login.php";
$pratica = $_REQUEST["pratica"];

$result = Array("success"=>0,"message"=>Array("Nessun id pratica passato"));
if ($pratica){
    $result = trovaDocumenti($pratica);
}
header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
return;
?>
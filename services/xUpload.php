<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*include_once "../login.php";
error_reporting(E_ERROR);
$db=  appUtils::getDB();
$dbh = utils::getDb();*/

$ds = DIRECTORY_SEPARATOR;  //1
 
include_once "../login.php";
$dbh = utils::getDb();
if (!empty($_FILES)) {
    $result = Array("success"=>0,"message"=>"");
    $pr = new pratica($_POST["pratica"]);
    if (!is_array($_FILES["file"]["name"])){
        $file = $_FILES['file'];
        $tempFile = $file['tmp_name'];          //3             
        $targetPath = dirname(dirname( __FILE__ )) . $ds. $storeFolder . $ds;  //4
        $targetFile =  $targetPath. $file['name'];  //5
        if(move_uploaded_file($tempFile,$targetFile)){
            $sql = "INSERT INTO stp.stampe(pratica,) VALUES();";
        } 
        else{
            
        }
    }
    else{
        for($i=0;$i<count($_FILES['file']["name"]);$i++){ 
            $file = $_FILES['file'];
            $tempFile = $file['tmp_name'][$i];          //3             
            $targetPath = $pr->documenti;  //4
            $targetFile =  $targetPath. $file['name'][$i];  //5
            if(move_uploaded_file($tempFile,$targetFile)){
                $data = Array($_POST["pratica"],$file['name'][$i],$_POST["form"],$_SESSION['USERNAME'],date("d-m-Y"),"Caricato il documento ".$file['name'][$i],$_SESSION["USER_ID"],time(),$_POST["app"]);
                $sql = "INSERT INTO stp.stampe(pratica,file_doc,form,utente_doc,data_creazione_doc,descrizione,uidins,tmsins,tipo_app) VALUES(?,?,?,?,?,?,?,?,?);";
                $stmt = $dbh->prepare($sql);
                if($stmt->execute($data)){
                    $result["success"] = 1;
                }
                else{
                    $result["message"] = "";
                }
            } 
            else{

            } //6
        }
    }
    
    header('Content-Type: application/json');
    print json_encode($result);
    return;
}
?>


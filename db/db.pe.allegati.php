<?php

function rearrange( $arr ){
    foreach( $arr as $key => $all ){
        foreach( $all as $i => $val ){
            $new[$i][$key] = $val;    
        }    
    }
    return $new;
}

$azione= strtolower($_POST["azione"]);
$dbh = utils::getDb();
//print_array($_REQUEST);


if($azione=="salva"){
    $pr = new pratica($idpratica);
    $dir = $pr->allegati;
    if($_REQUEST["mode"]=="new"){
        
        $uploadedFiles = rearrange($_FILES["file_allegato"]);
        
            $data = Array(
                $idpratica,
                $_REQUEST["prot_allegato"],
                $_REQUEST["prot_richiesta"],
                $_REQUEST["tipo_allegato"],
                $_REQUEST["stato_allegato"],
                $_REQUEST["note"]
            );
        $fldList = Array("pratica","prot_allegato","prot_richiesta","tipo_allegato","stato_allegato","note");
        if($_REQUEST["data_prot_allegato"]) {
            $data[]=$_REQUEST["data_prot_allegato"];
            $fldList[] = "data_prot_allegato"; 
        }
        if($_REQUEST["data_richiesta"]) {
            $data[]=$_REQUEST["data_richiesta"];
            $fldList[] = "data_richiesta"; 
        }
        for($i=0;$i<count($uploadedFiles);$i++){
           $newName = utils::filter_filename($uploadedFiles[$i]["name"]);
           if (is_file($dir.$newName)) $newName= sprintf("%d-%s",rand (100000, 999999),$newName);
           if($uploadedFiles[$i]["error"]==UPLOAD_ERR_NO_FILE){
               include_once "./db/db.savedata.php";
           }
           elseif($uploadedFiles[$i]["error"]==UPLOAD_ERR_OK && move_uploaded_file($uploadedFiles[$i]["tmp_name"], $dir.$newName)){
                $insData = $data;
                $insData[] = $newName;
                $insData[] = $uploadedFiles[$i]["type"];
                $insData[] = $uploadedFiles[$i]["size"];
                $fldList[]="nome_file";
                $fldList[] = "tipo_file";
                $fldList[] = "size_file";
                $fields = implode(",",$fldList);
                $nFields=count($fldList);
                $valueList= array_fill(0,$nFields,'?');
                $values = implode(",",$valueList);
                $sql = "INSERT INTO pe.file_allegati($fields) VALUES($values)";
                
                $stmt = $dbh->prepare($sql); 
                if(!$stmt->execute($insData)){
                    echo "<p>Errore nell'inserimento del record</p>";
                    print_array($stmt->errorInfo());
                }
           }
        }
    }
    else{
        $uploadedFiles = $_FILES["file_allegato"];
//        print_array($_FILES);
        $id = $_REQUEST["id"];
        if($uploadedFiles["error"]==UPLOAD_ERR_NO_FILE){
            include_once "./db/db.savedata.php";
        }
        elseif($uploadedFiles["error"]==UPLOAD_ERR_OK){
            include_once "./db/db.savedata.php";
            $newName = utils::filter_filename($uploadedFiles["name"]);
            if (is_file($dir.$newName)) $newName= sprintf("%d-%s",rand (100000, 999999),$newName);
            if(move_uploaded_file($uploadedFiles["tmp_name"], $dir.$newName)){
                $sql="UPDATE pe.file_allegati SET nome_file=?,tipo_file=?,size_file=? WHERE id=?;";
                $stmt = $dbh->prepare($sql);
                if(!$stmt->execute(Array($newName,$uploadedFiles["type"],$uploadedFiles["size"],$id))){
                    echo "<p>Errore nell'aggiornamento del Nome File</p>";
                }
            }else{
                
            }
        }
    }
}
elseif($azione=="elimina"){
    include_once "./db/db.savedata.php";
}


$active_form=$_POST["active_form"]."?pratica=$idpratica";
?>

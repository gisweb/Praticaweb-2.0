<?php



$conn = utils::getDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if ($_POST["azione"]=="Salva"){
    if($_REQUEST["mode"]=="new") {
        if ($_FILES["file_allegato"]["name"]){
	    $f = fopen($_FILES["file_allegato"]["tmp_name"],'r');
	    $ff = fread($f,filesize($_FILES["file_allegato"]["tmp_name"]));
	    fclose(f);
	    $filedata = base64_encode($ff);
            $data = Array($_REQUEST["pratica"],$_REQUEST["descrizione"],$_REQUEST["note"],$_FILES["file_allegato"]["name"],$_FILES["file_allegato"]["type"],$_FILES["file_allegato"]["size"],$filedata,1,$_SESSION["USERID"],time());

            $sql = "INSERT INTO storage.documentazione_inviata( pratica, descrizione, note, filename, filetype, filesize, filedata, chk, uidins, tmsins) VALUES ( ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt =  $conn->prepare($sql);

            if(!$stmt->execute($data)){
                print_array($stmt->getErrorInfo());
            }
        }
        else{
            $data = Array($_REQUEST["pratica"],$_REQUEST["descrizione"],$_REQUEST["note"],1,$_SESSION["USER_ID"],time());
            $sql = "INSERT INTO storage.documentazione_inviata( pratica, descrizione, note, chk, uidins, tmsins) VALUES ( ?,  ?, ?, ?, ?, ?);";
            $stmt =  $conn->prepare($sql);
            if(!$stmt->execute($data)){
                print_array($stmt->getErrorInfo());
            }
        }
        
    }
    else{
        if ($_FILES["file_allegato"]["name"]){
	    $f = fopen($_FILES["file_allegato"]["tmp_name"],'r');
            $ff = fread($f,filesize($_FILES["file_allegato"]["tmp_name"]));
            fclose(f);
            $filedata = base64_encode($ff);

            $data = Array($_REQUEST["descrizione"],$_REQUEST["note"],$_FILES["file_allegato"]["name"],$_FILES["file_allegato"]["size"],$_FILES["file_allegato"]["type"],$filedata,$_REQUEST["chk"]+1,$_SESSION["USERID"],time(),$_REQUEST["id"]);
            $sql = "UPDATE storage.documentazione_inviata SET descrizione=?, note=?, filename=?, filesize=?, filetype=?, filedata=?, chk=?, uidupd=?, tmsupd=? WHERE id = ?;";
            $stmt =  $conn->prepare($sql);
            if(!$stmt->execute($data)){
                print_array($stmt->getErrorInfo());
            }
        }
        else{
            $data = Array($_REQUEST["descrizione"],$_REQUEST["note"],1,$_SESSION["USER_ID"],time(),$_REQUEST["id"]);
            $sql = "UPDATE storage.documentazione_inviata SET descrizione=?, note=?, chk=?, uidupd=?, tmsupd=? WHERE id = ?;";
            $stmt =  $conn->prepare($sql);
            if(!$stmt->execute($data)){
                print_array($stmt->getErrorInfo());
            }
        }
        
    }
}
elseif($_POST["azione"]=="Elimina"){
    $data = Array($_REQUEST["id"]);
    $sql = "DELETE FROM storage.documentazione_inviata WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if(!$stmt->execute($data)){
        print_array($stmt->getErrorInfo());
    }
}


	
$active_form="storage.documentazione_inviata.php?pratica=$idpratica";
	
?>

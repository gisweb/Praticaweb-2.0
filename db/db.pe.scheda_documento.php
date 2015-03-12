<?php
require_once APPS_DIR.'utils'.DIRECTORY_SEPARATOR."filesystem.php";
$db=  utils::getDb();
$mode = $_REQUEST["mode"];
$action = $_REQUEST["azione"];
$idpratica = $_REQUEST["pratica"];
$files = "";
$pr=new pratica($idpratica);
$updDir=$pr->allegati;
if ($_FILES["file_allegato"]["size"]){
     $files = "nome_file=:nome_allegato, tipo_file=:tipo_file, size_file=:size_file,";
     $filename = $_FILES["file_allegato"]["name"];
     $filename = new_file_name($updDir.$filename);
     $file_name = str_replace($updDir, '', $filename);
     $file_size = $_FILES["file_allegato"]["size"];
     $file_type = $_FILES["file_allegato"]["type"];
     if (!move_uploaded_file($_FILES['file_allegato']['tmp_name'], $updDir. $file_name)) { 
         echo("***ERROR: Non è possibile copiare il file.<br />\n". $updDir. $_FILES['myfile']['name']); 
		  exit;
	} 
}
else{
    $file_name = "";
     $file_size = null;
     $file_type = null;
}
if($action=="Salva"){
    $values = Array(
        ":id"=>Array($_REQUEST["idfile"],PDO::PARAM_INT),
        ":pratica"=>Array($idpratica,PDO::PARAM_INT),
        ":allegato"=>Array($_REQUEST["id"],PDO::PARAM_INT),
        ":protocollo"=>Array($_REQUEST["protocollo"],PDO::PARAM_STR),
        ":data_protocollo" =>Array(($_REQUEST["data_protocollo"])?($_REQUEST["data_protocollo"]):(null),PDO::PARAM_STR),
        ":note"=>Array($_REQUEST["note"],PDO::PARAM_STR),
        ":stato_allegato"=>Array($_REQUEST["stato_allegato"],PDO::PARAM_STR),
        ":nome_allegato"=>Array($file_name,PDO::PARAM_STR),
        ":tipo_file"=>Array($file_type,PDO::PARAM_STR),
        ":size_file"=>Array($file_size,PDO::PARAM_INT),
        ":form"=>Array("allegati",PDO::PARAM_STR),
        ":uid"=>Array($_SESSION["USER_ID"],PDO::PARAM_INT),
        ":tms"=>Array(time(),PDO::PARAM_INT),
        ":chk"=>Array($_REQUEST["chk"]+1,PDO::PARAM_INT),
    );
    if ($mode=="new"){
        $sql="INSERT INTO pe.file_allegati(pratica,allegato,prot_allegato,data_prot_allegato,note,stato_allegato,nome_file,tipo_file,size_file,form,uidins,tmsins) 
VALUES(:pratica,:allegato,:protocollo,:data_protocollo,:note,:stato_allegato,:nome_allegato,:tipo_file,:size_file,:form,:uid,:tms);";
    }
    else{
       
        $sql="UPDATE pe.file_allegati
   SET $files 
       note=:note, chk=:chk, 
       uidupd=:uid, tmsupd=:tms, data_prot_allegato=:data_protocollo, prot_allegato=:protocollo, stato_allegato=:stato_allegato
    WHERE id = :id;";
    }
    $sth = $db->prepare($sql);
    foreach($values as $k=>$v){
        $sth->bindParam($k, $v[0],$v[1]);
    }
    if(!$sth->execute()){
        $arr = $sth->errorInfo();
        print_r($arr);
    }
    else{
        if ($mode=="new") $_SESSION["ADD_NEW"]=1;
    }
}
else if($action=="Elimina"){
    $sql="SELECT nome_file FROM pe.file_allegati WHERE id = ".$_REQUEST["idfile"];
    $sth = $db->prepare($sql);
    $sth->execute();
    $filename=$sth->fetchColumn();
    if (unlink($updDir.$filename)){
        $sql = "DELETE FROM pe.file_allegati WHERE id = ".$_REQUEST["idfile"];
        $sth = $db->prepare($sql);
        $sth->execute();
    }
}
$active_form="pe.scheda_documento.php?pratica=$idpratica&mode=list&id=all_".$_REQUEST["id"];

?>
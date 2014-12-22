<?php
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
$db=  utils::getDb();
$mode = $_REQUEST["mode"];
$action = $_REQUEST["azione"];
if($action=="Salva"){
    $values = Array(
        ":id"=>Array($_REQUEST["idfile"],PDO::PARAM_INT),
        ":pratica"=>Array($_REQUEST["pratica"],PDO::PARAM_INT),
        ":allegato"=>Array($_REQUEST["id"],PDO::PARAM_INT),
        ":protocollo"=>Array($_REQUEST["protocollo"],PDO::PARAM_STR),
        ":data_protocollo" =>Array($_REQUEST["data_protocollo"],PDO::PARAM_STR),
        ":note"=>Array($_REQUEST["note"],PDO::PARAM_STR),
        ":stato_allegato"=>Array($_REQUEST["stato_allegato"],PDO::PARAM_STR),
        ":nome_allegato"=>Array($_FILES["file_allegato"]["name"],PDO::PARAM_STR),
        ":tipo_file"=>Array($_FILES["file_allegato"]["type"],PDO::PARAM_STR),
        ":size_file"=>Array($_FILES["file_allegato"]["size"],PDO::PARAM_INT),
        ":form"=>Array("allegati",PDO::PARAM_STR),
        ":uid"=>Array($_SESSION["USERID"],PDO::PARAM_INT),
        ":tms"=>Array(time(),PDO::PARAM_INT),
        ":chk"=>Array($_REQUEST["chk"]+1,PDO::PARAM_INT),
    );
    if ($mode=="new"){
        $sql="INSERT INTO pe.file_allegati(pratica,allegato,prot_allegato,data_prot_allegato,note,stato_allegato,nome_file,tipo_file,size_file,form,uidins,tmsins) 
VALUES(:pratica,:allegato,:protocollo,:data_protocollo,:note,:stato_allegato,:nome_allegato,:tipo_file,:size_file,:form,:uid,:tms);";
    }
    else{
    $sql="UPDATE pe.file_allegati
   SET nome_file=:nome_allegato, tipo_file=:tipo_file, size_file=:size_file, 
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
        if ($mode=="new") $_SESSION["ADD_NEW"]=$sth->lastinsertid("");
    }
}

$active_form="pe.scheda_documento.php?pratica=$idpratica&mode=list&id=all_".$_REQUEST["id"];

?>
<?php

if($_REQUEST["azione"]=="Salva"){
    $scadenza=$_REQUEST["scadenza"];
    $tb=$_REQUEST["tabella"];
    $sql="DELETE FROM pe.e_tipopratica_scadenze WHERE codice='$codice';select setSequence('pe','e_tipopratica_scadenze','id');";
    $dbconn->sql_query($sql);
    foreach($scadenza as $k=>$v){
        if($v && $tb[$k]){
            $sql="INSERT INTO pe.e_tipopratica_scadenze(codice,tipo_pratica,scadenza,tabella) VALUES('$codice',$k,'$v','$tb[$k]')";
            $dbconn->sql_query($sql);
        }
    }
    $modo="list";
}

?>

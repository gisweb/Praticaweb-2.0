<?php

if($_REQUEST["azione"]=="Salva"){
    $scadenza=$_REQUEST["scadenza"];
    $tb=$_REQUEST["tabella"];
    $fld=$_REQUEST["campo"];
    $sql="DELETE FROM pe.e_tipopratica_scadenze WHERE codice='$codice';select setSequence('pe','e_tipopratica_scadenze','id');";
    $dbconn->sql_query($sql);
    foreach($scadenza as $k=>$v){
        if($v && $tb[$k]){
            $sql="INSERT INTO pe.e_tipopratica_scadenze(codice,tipo_pratica,scadenza,tabella,campo) VALUES('$codice',$k,'$v','$tb[$k]','$fld[$k]')";
            if(!$dbconn->sql_query($sql)) echo "<p>Errore nella query : <br>$sql</p>";
        }
    }
    $modo="list";
}

?>

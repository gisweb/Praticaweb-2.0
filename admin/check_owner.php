<?php
session_start();
if (!isset($_SESSION["PROPR_PRATICA_$idpratica"])){
        $sql="SELECT DISTINCT userid FROM  pe.responsabili_pratica WHERE pratica=?;";
        $conn=utils::getDb();
        $sth=$conn->prepare($sql);
        $sth->execute(Array($idpratica));
        $owners=$sth->fetchAll(PDO::FETCH_COLUMN);
        
    if(in_array($_SESSION['USER_ID'],$owners)){
        $_SESSION["PROPR_PRATICA_$idpratica"]=1;
    }
    else{
        $_SESSION["PROPR_PRATICA_$idpratica"]=0;
    }
}
?>
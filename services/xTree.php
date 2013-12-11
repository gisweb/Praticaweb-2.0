<?php
require_once "../login.php";

$db=appUtils::getDB();
$tipo=$_REQUEST["ricerca"];
$viacivico=$_REQUEST["id"];
$result=Array();
switch($tipo){
    case "civici-pratiche":
        if ($viacivico && strlen($viacivico)>1){
            $sql="SELECT DISTINCT pratica as id,tipo_pratica||' n°'||numero||'del '||data_presentazione as text,'open' as state FROM stp.single_pratica WHERE pratica IN (SELECT DISTINCT pratica FROM pe.indirizzi WHERE via||coalesce(' civico n° '||civico,'') ilike ?) order by 1";
            //echo $sql;
            $result=$db->fetchAll($sql,Array($viacivico));
        }
        elseif ($viacivico && strlen($viacivico)==1){
            $sql="SELECT DISTINCT lower(via||coalesce(' civico n° '||civico,'')) as id,lower(via||coalesce(' civico n° '||civico,'')) as text,'closed' as state FROM pe.indirizzi WHERE upper(substr(split_part(via,' ',2),1,1)) = ? order by 1";
            $result=$db->fetchAll($sql,Array($viacivico));
        }
        else{
            $sql="SELECT DISTINCT upper(substr(split_part(via,' ',2),1,1)) as text,upper(substring(split_part(via,' ',2),1,1)) as id,'closed' as state FROM pe.indirizzi WHERE coalesce(via,'')<>'' order by 1";
            $result=$db->fetchAll($sql);
        }
        break;
    case "civici-esistenti":
        if ($viacivico && strlen($viacivico)>1){
            $sql="SELECT DISTINCT pratica as id,tipo_pratica||' n°'||numero||'del '||data_presentazione as text,'open' as state FROM stp.single_pratica WHERE pratica IN (SELECT DISTINCT pratica FROM pe.indirizzi WHERE via||coalesce(' civico n° '||civico,'') ilike ?) order by 1";
            //echo $sql;
            $result=$db->fetchAll($sql,Array($viacivico));
        }
        elseif ($viacivico && strlen($viacivico)==1){
            $sql="SELECT DISTINCT lower(nome||coalesce(' civico n° '||label,'')) as id,lower(nome||coalesce(' civico n° '||label,'')) as text,'closed' as state FROM civici.pe_vie A inner join civici.pe_civici B ON(A.id=B.strada) WHERE upper(substr(split_part(nome,' ',2),1,1)) = ? order by 1";
            $result=$db->fetchAll($sql,Array($viacivico));
        }
        else{
            $sql="SELECT DISTINCT upper(substr(split_part(nome,' ',2),1,1)) as text,upper(substring(split_part(nome,' ',2),1,1)) as id,'closed' as state FROM civici.pe_vie WHERE coalesce(nome,'')<>'' order by 1";
            $result=$db->fetchAll($sql);
        }
        break;
}

//$result=$ris;
print json_encode($result);
?>
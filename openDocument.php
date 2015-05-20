<?php
require_once "login.php";
$id=$_REQUEST["id"];
$pratica=$_REQUEST["pratica"];
$t=$_REQUEST["type"];
if ($pratica!="null" && $pratica){
    $conn=utils::getDB();
    $sql="SELECT nome_file,tipo_file FROM pe.file_allegati WHERE id=?";
    $stmt=$conn->prepare($sql);
    $stmt->execute(Array($id));
    list($fName,$fType) = $stmt->fetch();
    $pr=new pratica($pratica,$type);
    $ext = pathinfo($fName, PATHINFO_EXTENSION);
    if($ext=='p7m') $fType="application/pkcs7-mime";
    $url=(defined('LOCAL_DOCUMENT') && LOCAL_DOCUMENT)?($pr->smb_allegati.$fName):($pr->allegati.$fName);
    $f=fopen($url,'r');
    $doc=fread($f,filesize($url));
    
}
else{
    $db=appUtils::getDB();
    $sql="SELECT nome FROM stp.e_modelli WHERE id=?";
    $fName=$db->fetchColumn($sql, array($id));
    $url=SMB_MODELLI.$fName;
}
//echo $url;exit;
header("Content-type: $fType");
header('Content-Disposition: inline; filename="'.$fName.'"');
//@header("Location: $url") ;
print $doc;
?>

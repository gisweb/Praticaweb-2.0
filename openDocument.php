<?php
require_once "login.php";
$id=$_REQUEST["id"];
$pratica=$_REQUEST["pratica"];
$t=$_REQUEST["type"];
$mode = $_REQUEST["mode"];
$contentDisposition="inline";
if ($mode == "anagrafe_tributaria"){
    $fName=$_REQUEST["filename"];
    $url=STAMPE.$fName;
    $contentDisposition="download";
    $f=fopen($url,'r');
    $doc=fread($f,filesize($url));
    $fName = sprintf("%d-%s",rand(10000,99999),"ANAGRAFE-TRIBUTARIA.txt");
}
elseif ($pratica!="null" && $pratica){
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
    $fName = sprintf("%d-%s",rand(10000,99999),$fName);
}
else{
    $db=appUtils::getDB();
    $sql="SELECT nome FROM stp.e_modelli WHERE id=?";
    $fName=$db->fetchColumn($sql, array($id));
    $url=SMB_MODELLI.$fName;
    $fName = sprintf("%d-%s",rand(10000,99999),$fName);
}

$st ="Content-Disposition: $contentDisposition; filename=\"$fName\"";

//echo $url;exit;
header("Content-type: $fType");
header($st);
//@header("Location: $url") ;
print $doc;
?>

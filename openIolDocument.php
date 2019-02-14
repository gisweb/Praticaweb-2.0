<?php
session_start();
$_SESSION["USER_ID"] = 1;

require_once "login.php";
$id=$_REQUEST["id"];
$pratica=$_REQUEST["pratica"];
$t=$_REQUEST["type"];
$mode = $_REQUEST["mode"];
$contentDisposition=(defined("FILE_DISPOSITION"))?(FILE_DISPOSITION):("inline");
if ($mode == "anagrafe_tributaria"){
    $fName=$_REQUEST["filename"];
    $url=STAMPE.$fName;
    $contentDisposition="download";
    $f=fopen($url,'r');
    $size=filesize($url);
    $doc=fread($f,$size);
    //$fName = sprintf("%d-%s",rand(10000,99999),"ANAGRAFE-TRIBUTARIA.txt");
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
    $size=filesize($url);
    $doc=fread($f,$size);
    $fName = sprintf("%d-%s",rand(10000,99999),$fName);
}
else{
    $db=appUtils::getDB();
    $sql="SELECT nome FROM stp.e_modelli WHERE id=?";
    $fName=$db->fetchColumn($sql, array($id));
    $url=SMB_MODELLI.$fName;
    $fName = sprintf("%d-%s",rand(10000,99999),$fName);
}
unset($_SESSION["USER_ID"]);
$st ="Content-Disposition: $contentDisposition; filename=\"$fName\"";

//echo $url;exit;
if ($contentDisposition=='attachment'){
    header('Content-Description: File Transfer');
    header($st);
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $size);
    header("Content-type: $fType");
}
else{
   $st ="Content-Disposition: $contentDisposition; filename=\"$fName\""; 
    header($st);
    header("Content-type: $fType");

}


//@header("Location: $url") ;
print $doc;
?>

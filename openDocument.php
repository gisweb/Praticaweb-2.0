<?php
require_once "login.php";
$id=$_REQUEST["id"];
$pratica=$_REQUEST["pratica"];
$type=($_REQUEST["cdu"]==1)?(1):(0);
if ($pratica!="null" && $pratica){
    $db=appUtils::getDB();
    $sql="SELECT file_doc FROM stp.stampe WHERE id=?";
    $fName=$db->fetchColumn($sql, array($id));
    $pr=new pratica($pratica,$type);

	$url=(defined('LOCAL_DOCUMENT') && LOCAL_DOCUMENT)?($pr->smb_documenti.$fName):($pr->url_documenti.$fName);
    //$f=fopen($url,'r');
    //$doc=fread($f,filesize($url));
    
}
else{
    $db=appUtils::getDB();
    $sql="SELECT nome FROM stp.e_modelli WHERE id=?";
    $fName=$db->fetchColumn($sql, array($id));
    $url=SMB_MODELLI.$fName;
}
//echo $url;exit;
header("Content-type: application/vnd.ms-word");
//header('Content-Disposition: inline; filename="'.$fname.'"');
@header("Location: $url") ;

?>

<?php
require_once "login.php";
$id=$_REQUEST["id"];
$conn=utils::getDB();
$sql="SELECT * FROM storage.documentazione_inviata WHERE id=?";
   
$stmt=$conn->prepare($sql);
$stmt->execute(Array($id));
$res = $stmt->fetch();
$doc=base64_decode($res["filedata"]);

//echo $url;exit;
header("Content-type: ".$res["filetype"]);
header('Content-Disposition: inline; filename="'.$res["filename"].'"');
//@header("Location: $url") ;
print $doc;
?>

<?php
class generalMailUtils {
   
   static function getUserInfo($userId=0){
	  if (!$userId>0) $userId = $_SESSION["USER_ID"];
	  $sql="SELECT * FROM admin.mail WHERE userid = ?";
	  $conn = utils::getDb();
	  
   }
   static function getHostInfo($id){
	  
   }
   static function sendMail($hostInfo,$to=Array(),$object,$attachments=Array(),$cc=Array(),$ccn=Array()){
	  
   }
}
?>
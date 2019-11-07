<?php
require_once APPS_DIR.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."PHPMailerAutoload.php";

class generalMailUtils {
   
   static function getUserInfo($userId=0){
	  if (!$userId>0) $userId = $_SESSION["USER_ID"];
	  $sql="SELECT * FROM admin.mail WHERE userid = ?";
	  $conn = utils::getDb();
      $stmt = $conn->prepare($sql);
      if ($stmt->execute(Array($userId))){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      else{
        
      }
	  return $res;
   }
   static function getHostInfo($id){
	  $sql="SELECT * FROM admin.mail WHERE id = ?";
	  $conn = utils::getDb();
      $stmt = $conn->prepare($sql);
      if ($stmt->execute(Array($id))){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      else{
        
      }
	  return $res;
   }
   
   static function getAttachments($idDocs = Array()){
	  $sql=sprintf("SELECT file_doc FROM stp.stampe WHERE id IN (%s)",implode(',',$idDocs));
	  $conn = utils::getDb();
      $stmt = $conn->prepare($sql);
      if ($stmt->execute()){
        $res = $stmt->fetchAll(PDO::FETCH_COLUMN);
      }
      else{
        
      }
	  return $res;
   }
   
   static function sendMail($hostInfo,$to=Array(),$subject,$body="",$attachments=Array(),$cc=Array(),$bcc=Array()){
	  $mail = new PHPMailer;

	  $mail->SMTPDebug = 4;                               // Enable verbose debug output
	  
	  $mail->isSMTP();                                      // Set mailer to use SMTP
	  $mail->Host = $hostInfo["mailhost"];  // Specify main and backup SMTP servers
	  $mail->SMTPAuth = true;                               // Enable SMTP authentication
	  $mail->Username = $hostInfo["username"];                 // SMTP username
	  $mail->Password = $hostInfo["passwd"];// SMTP password
	  if ($hostInfo["ssl"]){
		 $mail->SMTPSecure = 'ssl';
	  }
	  elseif($hostInfo["tls"]){
		 $mail->SMTPSecure = 'tls';		 
	  }
	  
	  //$mail->SMTPSecure = ($hostInfo["ssl"])?('ssl'):(($hostInfo["tls"])?('tls'):(''));                            // Enable TLS encryption, `ssl` also accepted
	  $mail->Port = $hostInfo["port"];
	  $mail->From = $hostInfo["username"];

	  
	  for($i=0;$i<count($to);$i++){
		 $mail->addAddress($to[$i]);
	  }
	  $mail->FromName = $hostInfo["nominativo"];
	  for($i=0;$i<count($cc);$i++){
		 $mail->addCC($cc[$i]);
	  }
	  for($i=0;$i<count($bcc);$i++){
		 $mail->addBCC($bcc[$i]);
	  }
	  for($i=0;$i<count($attachments);$i++){
		 $mail->addAttachment($attachments[$i]);
	  }
	  $mail->Subject = $subject;
	  $mail->Body    = $body;
	  //return $mail;
	  if(!$mail->send()) {
		 return Array("success"=>0,"message"=> $mail->ErrorInfo);
		 print_array($mail);
	  } else {
		 return Array("success"=>1,"message"=> 'Message has been sent');
	  }
   }
}
?>
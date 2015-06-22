<?php

require_once "../../login.php";
require_once LIB.DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."PHPMailerAutoload.php";
$conn=utils::getDb();
$sql = "SELECT * FROM admin.user_mail where id=2";
$stmt = $conn->prepare($sql);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);

$mail = new PHPMailer;
//Set who the message is to be sent from

$mail->Host = $config["smtp"];
$mail->Username = $config["username"];
$mail->Password = $config["smtp_pwd"];
$mail->Port = $config["smtp_port"];
$mail->setFrom($config["mail_from"]);

$mail->Subject = 'Test Invio Mail';

$mail->Body = 'Mail Autogenerata';

$mail->addAddress('marco.carbone.shop@gmail.com', 'Marco Carbone');
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>
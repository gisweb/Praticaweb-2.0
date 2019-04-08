<?php
//session_start();
//$_SESSION['USER_ID']=1;
require_once "login.php";
$pratica=22444;
$sql="SELECT * FROM stp.e_modelli;";
$dbh = utils::getDb();
$sth=$dbh->prepare($sql);
$sth->execute();
$modelli=$sth->fetchAll();

?>
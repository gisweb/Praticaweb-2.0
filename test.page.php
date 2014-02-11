<?php
//session_start();
//$_SESSION['USER_ID']=1;
require_once "login.php";
$pratica=22444;
$Errors=Array();
$customData=Array();

require_once LOCAL_LIB."../include/stampe.php";

if ($Errors)
    print_array($Errors);
print_array($customData);
?>
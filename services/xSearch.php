<?php
include_once "../login.php";
error_reporting(E_ERROR);
$action=(isset($_REQUEST['action']))?($_REQUEST['action']):('search');
$searchtype=$_REQUEST['searchType'];
$value=addslashes($_REQUEST['term']);
$usr=$_SESSION['USER_ID'];
$result=Array();
switch($action){
    
}

print json_encode($result);
return;


?>
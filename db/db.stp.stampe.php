<?php
require_once "login.php";
if (PRINT_VERSION == 1){
	include APPS_DIR."db/db.stp.stampe.v1.php";
}
else{
	include APPS_DIR."db/db.stp.stampe.v2.php";
}
?>
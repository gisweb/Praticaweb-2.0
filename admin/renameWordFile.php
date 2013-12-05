<?php
require_once "../login.php";
$directory = "E:\\ModelliDoc\\";
//get all text files with a .txt extension.
$files = glob($directory . "*.docx");
echo "<ol>";
foreach($files as $fileName){
    $newName = str_replace("à","a",$files);
    rename($fileName,$newName);
    echo "<li>Renaming \"".$info["basename"]."\" To \"$newName\"</li>";
}
echo "</ol>";
?>
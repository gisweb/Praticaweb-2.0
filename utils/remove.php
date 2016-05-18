<?php

$files = glob('../db/*/*.php');
foreach($files as $f){
    $infoFile = pathinfo($f);
    $fname = $infoFile["basename"];
    $dir = $infoFile["dirname"];
    $tmp = explode(".",$fname);
    array_shift($tmp);
    array_shift($tmp);
    $fname = implode(".",$tmp);
    $newName = $dir.DIRECTORY_SEPARATOR."db.".$fname;
    echo "<p>Renaming $f to $newName</p>";
    rename($f,$newName);
}
?>
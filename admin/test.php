<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$filename = "/data/spezia_demo/pe/praticaweb/modelli/permesso di costruire.docx";
if (!file_exists($filename)) die("<p><b>Impossibile trovare il file $filename ....</b></p>");
$f = fopen($filename,'r');
$t = fread($f, filesize($filename));
echo "<p>Done</p>";
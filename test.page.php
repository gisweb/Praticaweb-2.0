<?php
//session_start();
//$_SESSION['USER_ID']=1;


$data = Array("testo"=>htmlentities("nipvdwionbvewy8feQPè+àùSCD<><&$%€",ENT_COMPAT, 'UTF-8'));
//77$text = json_encode($data, JSON_UNESCAPED_UNICODE);
//echo "<p>Testo -1 : $text</p>";
$text = html_entity_decode(json_encode($data),ENT_COMPAT, 'UTF-8');
echo "<p>Testo -2 : $text</p>";
?>
<html>
    <body>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../login.php';
$db=appUtils::getDB();

$table=$_REQUEST["table"];
switch($table){
    case "e_parametri":
        $sql="select * from pe.e_parametri";
        $ris=$db->fetchAll($sql);
        foreach($ris as $val){
            //$flds[]="--\tParametro ".utf8_decode($val["nome"])."\n\t".$val["codice"]." varchar";
            echo "\tif rec.codice='".$val["codice"]."' then
\t\tris.".$val["codice"].":=ris.valore;
\tend if;\n";
        }
        $field=implode(",\n",$flds);
/*        echo "
CREATE TYPE stp.pprog AS(
--\tNumero di pratica
\tpratica integer,
$field
);";*/
        break;
    default:
        break;
}

?>
    </body>
</html>
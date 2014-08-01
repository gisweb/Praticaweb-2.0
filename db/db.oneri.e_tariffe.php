<?php

$conn=utils::getDb();
$anno=$_REQUEST["anno"];
$inizio=$_REQUEST["valido_da"];
$fine=$_REQUEST["valido_a"];
$conn->beginTransaction();
$sql="DELETE FROM oneri.e_tariffe WHERE anno=?";
$stmt=$conn->prepare($sql);
$stmt->execute(Array($anno));
$data=$_REQUEST["data"];
if($_REQUEST["azione"]=="Salva"){
    foreach($data as $key=>$val){
        $sql="INSERT INTO oneri.e_tariffe(tabella, anno, funzione, descrizione, tr, a, ie, k, valido_da,valido_a) VALUES(?,?,?,?,?,?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        if (!$stmt->execute(Array($key,$anno,$val["funzione"],$val["descrizione"],$val["tr"],$val["a"],$val["ie"],$val["k"],$inizio,$fine))){ 
            $error=1;
            $errors[]=$conn->errorInfo();
        }
    }
}
if ($error==1) {
    $conn->rollBack();
    print_array($errors);
}
else
    $conn->commit();
?>
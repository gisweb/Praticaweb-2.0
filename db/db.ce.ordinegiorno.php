<?php
$conn = utils::getDb();


if ($_POST["idpratica"]) {
	$pratiche=$_POST["idpratica"];
	$idcomm=$_POST["pratica"];
	//$numero=$_POST["numero"];
	$uid=$_SESSION['USER_ID'];
	$sql="SELECT tipo_comm,data_convocazione FROM ce.commissione WHERE id=?;";
	$stmt = $conn->prepare($sql);
        $stmt->execute(Array($idcomm));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	print_debug($sql);
	$tipo_comm=$row["tipo_comm"];
	$data=$row["data_convocazione"];
        $sqlOrdine= "SELECT max(coalesce(ordine,0))+1 as ordine FROM pe.pareri WHERE data_rich=? AND ente=?";
        $stmt=$conn->prepare($sqlOrdine);
        $stmt->execute(Array($data,$tipo_comm));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	$ordine = $row["ordine"];

        $sql="INSERT INTO pe.pareri(pratica,ente,data_rich,data_ril,ordine,uidins,tmsins) VALUES(?,?,?,?,?,?,?)";
	$stmt=$conn->prepare($sql);
	for($i=0;$i<count($pratiche);$i++){
		$tmsins=time();
                $parere = Array($pratiche[$i],$tipo_comm,$data,$data,($ordine+$i),$uid,$tmsins);
		$stmt->execute($parere);
	}
}
$active_form="ce.ordinegiorno.php?comm=1&pratica=$idpratica";
?>

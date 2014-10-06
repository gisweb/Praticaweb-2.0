<?php
$indirizzoip=getenv("REMOTE_ADDR");
// Cripto la password 
$pwd=$password;
$password = md5($password);
// Controllo se l'utente ï¿œregistrato e attivo

$sql = "SELECT * FROM admin.users WHERE username=? AND enc_pwd=?";
$conn=utils::getDb();
$sth=$conn->prepare($sql);
$sth->execute(Array($username,$password));

$result = $sth->fetchAll(PDO::FETCH_ASSOC);
$nrec=count($result);

if($nrec==1){
    $groups=Array();
    $sql="UPDATE admin.users SET ultimo_accesso=CURRENT_TIMESTAMP(1) WHERE username='$username'";
    $conn->exec($sql);
    $conn->exec("INSERT INTO admin.accessi_log(ipaddr,username,data_enter) VALUES('$indirizzoip','$username',CURRENT_TIMESTAMP(1))");
    
    //Metto in sessione l'utente
    extract($result[0]);
    //se l'utente è stato disattivato lo avviso ed esco
    if(!$attivato){
	    echo "Il tuo account non &egrave; pi&ugrave; valido.Contatta l'amministratore del Sistema per ottenere un nuovo account. <a href=\"mailto:info@gisweb.it\" style=\"color:red; text-align:center; font-size:13px\">info@gisweb.it</a>";
	    exit;
    }
    $sql="SELECT nome FROM admin.groups WHERE id in ($gruppi);";
    $sth=$conn->prepare($sql);
    $sth->execute(Array($gruppi));
    $ris = $sth->fetch(PDO::FETCH_ASSOC);
    for($i=0;$i<count($ris);$i++) $groups[]=$ris[$i]['nome'];
;
    $_SESSION['USER_NAME'] = $username;
    $_SESSION['USERNAME'] = $username;
    $_SESSION['PERMESSI']=$permessi;
    $_SESSION['USER_ID']=$userid;
    $_SESSION['NOMINATIVO']=trim("$app $cognome $nominativo");
    $_SESSION['GROUPS']=$groups;
} 
else {
	$sql="INSERT INTO admin.errori_log(ipaddr,username,data_enter) VALUES('$indirizzoip','$username',CURRENT_TIMESTAMP(1))";
	$conn->exec($sql);
	$sql="SELECT * FROM admin.errori_log WHERE username='?' AND data_enter=CURRENT_TIMESTAMP(1)";
	$sth=$conn->prepare($sql);
        $sth->execute(Array($username));
	$ris = $sth->fetchAll(PDO::FETCH_ASSOC);
	$nrec=count($ris);
	if ($nrec>5) {
		$sql="UPDATE admin.users SET attivato=0 WHERE username='$username'";
		$conn->exec($sql);
		echo "Il tuo account non &egrave; pi&ugrave; valido.Contatta l'amministratore del Sistema per ottenere un nuovo account. <a href=\"mailto:info@gisweb.it\" style=\"color:red; text-align:center; font-size:13px\">info@gisweb.it</a>";
	}
	else {
		include_once "./admin/enter.php";
	}
	exit;
}
?>

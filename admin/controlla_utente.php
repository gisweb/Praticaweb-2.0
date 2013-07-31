<?
$indirizzoip=getenv("REMOTE_ADDR");
// Cripto la password 
$pwd=$password;
$password = md5($password);
// Controllo se l'utente ï¿œregistrato e attivo
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql = "SELECT * FROM admin.users WHERE username='$username' AND enc_pwd='$password'";
$db->sql_query($sql);
$risultato = $db->sql_fetchrowset();
$nrec=$db->sql_numrows();

if($nrec==1){
    $groups=Array();
    $sql="UPDATE admin.users SET ultimo_accesso=CURRENT_TIMESTAMP(1) WHERE username='$username'";
    $db->sql_query($sql);
    $db->sql_query("INSERT INTO admin.accessi_log(ipaddr,username,data_enter) VALUES('$indirizzoip','$username',CURRENT_TIMESTAMP(1))");
    
    //Metto in sessione l'utente
    foreach( $risultato[0] AS $key=>$val ) $$key = stripslashes( $val );
    //se l'utente è stato disattivato lo avviso ed esco
    if(!$attivato){
	    echo "Il tuo account non &egrave; pi&ugrave; valido.Contatta l'amministratore del Sistema per ottenere un nuovo account. <a href=\"mailto:info@gisweb.it\" style=\"color:red; text-align:center; font-size:13px\">info@gisweb.it</a>";
	    exit;
    }
    $sql="SELECT nome FROM admin.groups WHERE id in ($gruppi);";
    $db->sql_query($sql);
    $ris = $db->sql_fetchrowset();
    for($i=0;$i<count($ris);$i++) $groups[]=$ris[$i]['nome'];
    $db->sql_close();
    $_SESSION['USER_NAME'] = $username;
    $_SESSION['USERNAME'] = $username;
    $_SESSION['PERMESSI']=$permessi;
    $_SESSION['USER_ID']=$userid;
    $_SESSION['NOMINATIVO']=trim("$app $cognome $nominativo");
    $_SESSION['GROUPS']=$groups;
} 
else {
	$sql="INSERT INTO admin.errori_log(ipaddr,username,data_enter) VALUES('$indirizzoip','$username',CURRENT_TIMESTAMP(1))";
	$db->sql_query($sql);
	$sql="SELECT * FROM admin.errori_log WHERE username='$username' AND data_enter=CURRENT_TIMESTAMP(1)";
	$db->sql_query($sql);
	$ris = $db->sql_fetchrowset();
	$nrec=$db->sql_numrows();
	if ($nrec>5) {
		$sql="UPDATE admin.users SET attivato=0 WHERE username='$username'";
		$db->sql_query($sql);
		echo "Il tuo account non &egrave; pi&ugrave; valido.Contatta l'amministratore del Sistema per ottenere un nuovo account. <a href=\"mailto:info@gisweb.it\" style=\"color:red; text-align:center; font-size:13px\">info@gisweb.it</a>";
		$db->sql_close();
	}
	else {
		$db->sql_close();
		include_once "./admin/enter.php";
	}
	exit;
}
?>

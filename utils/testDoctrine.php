	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php

function getSequence($tab,$db){
	list($sk,$tb)=explode('.',$tab);

	$sql="select array_to_string(regexp_matches(column_default, 'nextval[(][''](.+)['']::regclass[)]'),'') as sequence from information_schema.columns where table_schema='$sk' and table_name='$tb' and column_default ilike 'nextval%'";
	$sequence=$db->fetchColumn($sql);
	return $db->fetchColumn("select currval('$sequence')");
}
require_once '../login.php';

use Doctrine\Common\ClassLoader;
require_once APPS_DIR.'plugins/Doctrine/Common/ClassLoader.php';
$classLoader = new ClassLoader('Doctrine', APPS_DIR.'plugins/');
$classLoader->register();
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = array(
    'dbname' => 'sit_alghero',
    'user' => 'postgres',
    'password' => 'postgres',
    'host' => '127.0.0.1',
    'driver' => 'pdo_pgsql',
);
$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
/*
$sm = $conn->getSchemaManager();
$sequences = $sm->listSequences('sit_alghero');
foreach ($sequences as $sequence) {
    if(strpos($sequence->getName(),'pe.avvioproc')!==FALSE) {
		$sql="select currval('".$sequence->getName()."') as lastid;";
		$lastid = $conn->fetchColumn($sql);

	}
			
}*/
$arrDati=Array(
	'pratica'=>32087,
	'role'=>'rdp',
	'utente'=>48,
	'data'=>'now',
	'tmsins'=>time(),
	'uidins'=>48,
	"note"=>null
);
//$sql="INSERT INTO pe.wf_roles(pratica,role,utente,data,uidins,tmsins) VALUES($this->pratica,$r,$usr,$data,$this->userid,$t);";
//$db->sql_query($sql);
//
print $conn->insert('pe.wf_roles', $arrDati);
$seq=getSequence('pe.wf_roles',$conn);
print $seq;


/*try{
print $conn->delete('pe.wf_roles', Array("pratica"=>32087,'role'=>'rdp'));
}
catch(Exception $e){
	print_r($e->errorInfo[2]);
}*/
/*$sql = "SELECT * FROM pe.avvioproc;";
$stmt = $conn->query($sql);
$i=0;
while ($row = $stmt->fetch()) {
	$i++;
    echo "<p>$i) Numero :".$row['numero']."</p>";
}*/
?>


<?
$base=$_POST["base"];
$inc=$_POST["inc"];
$anno=$_POST["anno"];
$inc=str_replace(",",".",trim($inc));

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

if ($_POST["azione"]=="Salva") {
	if ($modo=="edit"){
		//controllo valori di POST (da fare)
		$tr=$_POST["tr"];
		$a=$_POST["a"];
		foreach ($tr as $key=>$value){
			$sql="update oneri.e_tariffe set tr=$value, a=".$a[$key]." where anno=$anno and funzione='$key'";
			//echo "<p>$sql</p>";
			if (!$db->sql_query($sql)) echo "<p>Errore nell'aggiornamento delle tabelle degli oneri $f<br>$sql</p>";
		}
	} else {
		//controllo valori di POST 
		if(!is_numeric(trim($inc)) or trim($inc)>100) $Errors["inc"]="Incremento non valido";
		if(!ereg("[0-9]{4}",trim($anno))) $Errors["anno"]="Anno non valido";
		
		$sql="SELECT * FROM oneri.elenco_anno WHERE opzione='$anno'";
		$db->sql_query($sql);
		$rows=$db->sql_numrows();
		if ($rows){
			$Errors["anno"]="Anno già presente";
			//echo "$sql<br>N° Righe $rows<br>"; 
		} else {
			if (!$Errors){
				$sql="SELECT * FROM oneri.e_tariffe WHERE anno='$base'";
				$db->sql_query($sql);
				$ris=$db->sql_fetchrowset();
				for($i=0;$i<count($ris);$i++){
					$tab=substr($ris[$i]["tabella"],strlen($ris[$i]["tabella"])-1,1);
					$f=$ris[$i]["funzione"];
					$desc=addslashes($ris[$i]["descrizione"]);
					$tr=number_format($ris[$i]["tr"]+($ris[$i]["tr"])*($inc/100),2,".","");
					$a=number_format($ris[$i]["a"]+($ris[$i]["a"])*($inc/100),2,".","");
					$ie=$ris[$i]["ie"];
					$k=$ris[$i]["k"];
					$sql="INSERT INTO oneri.e_tariffe VALUES('$tab','$anno','$f','$desc',$tr,$a,$ie,$k);";
					if (!$db->sql_query($sql)) echo "<p>Errore nell'aggiornamento delle tabelle degli oneri $f<br>$sql</p>";
				}
			}
		}
	}
}
?>
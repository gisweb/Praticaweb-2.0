<?
include "login.php";

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

switch ($_GET["funz"]){
	case "crea_elenco":
		$obj=$_GET["elenco"];
		$tab=$_GET["tabella"];
		$filtro=$_GET["filtro"];
		$val_filtro=$_GET["val_filtro"];
		$pratica=$_GET["pratica"];
		$oggetto=$_GET["oggetto"];
		
		/**/
		$sql="SELECT data_presentazione,destuso1 FROM pe.avvioproc LEFT JOIN pe.progetto ON avvioproc.pratica=progetto.pratica WHERE avvioproc.pratica=$pratica;";
		//if($_SESSION["USER_ID"]<=3) echo "<p>$sql</p>";
		$db->sql_query($sql);
		$ris=$db->sql_fetchrow();
		$data=$ris["data_presentazione"];
		$destuso=$ris["destuso1"];
		$sql="SELECT tipoxoneri FROM pe.vincoli LEFT JOIN pe.e_vincoli_zone ON vincoli.vincolo=e_vincoli_zone.vincolo AND vincoli.zona=e_vincoli_zone.zona WHERE pratica=$pratica;";
		//if($_SESSION["USER_ID"]<=3) echo "<p>$sql</p>";
		$db->sql_query($sql);
		$ris=$db->sql_fetchlist("tipoxoneri");
		if ($ris){
			foreach($ris as $val){
				$sql="SELECT DISTINCT opzione FROM oneri.elenco_d2 WHERE zona LIKE '%$val;%'";
				if ($db->sql_query($sql)) $opt=$db->sql_fetchrow();
				if ($opt) break;
			}
		}
		if ($opt) $sua=$opt[0];
		
		$selezionato=Array();
		
		for($i=0;$i<count($obj);$i++){
			unset($tmp);
			$campo="";
			$valore="";
			$tabella=$tab[$i];
			
			if ($obj[$i]=="anno" or $obj[$i]=="init") {
				$d=(strpos('-',$data))?(explode("-",$data)):(explode("/",$data));
				if (($d[1] < MESE_ONERI) or ($d[1] == MESE_ONERI and $d[0]<=GIORNO_ONERI)) $selezionato=Array("tabella"=>"oneri.elenco_anno","campo"=>"id","valore"=>$d[2]-1);
				else
					$selezionato=Array("tabella"=>"oneri.elenco_anno","campo"=>"id","valore"=>$d[2]);
			}
			elseif($obj[$i]=="tabella"){
				$selezionato=Array("tabella"=>"oneri.elenco_funzione","campo"=>"opzione","valore"=>$destuso);
			}
			elseif($obj[$i]=="d2" and ENABLE_D2){
				$selezionato=Array("tabella"=>"oneri.elenco_d2","campo"=>"opzione","valore"=>"$sua");
			}
			/**/	
			if ($filtro){
				if (is_array($_GET["filtro"][$obj[$i]])){
					for($j=0;$j<count($_GET["filtro"][$obj[$i]]);$j++) {
						$f=$_GET["filtro"][$obj[$i]][$j];
						$vf=$val_filtro[$obj[$i]][$j];
						if ($vf) $tmp[]="$f='$vf'";
					}
					if ($tmp) $filtro="WHERE ".implode(" AND ",$tmp);
					else
						$filtro="";
				}
				else{
						
					$f=$_GET["filtro"][$obj[$i]][0];
					$vf=$val_filtro[$obj[$i]][0];
					if ($vf) $filtro="WHERE $f='$vf'";
					else
						$filtro="";
				}
					
			}
			$sql="SELECT id,opzione FROM $tabella $filtro";
			// if($_SESSION["USER_ID"]<=3) echo "<p>$sql</p>";;
			if($db->sql_query($sql)){
				$arr_id=$db->sql_fetchlist("id");
				$arr_opt=$db->sql_fetchlist("opzione");
				$id="Array('".implode("','",$arr_id)."')";
				$opt="Array('".implode("','",$arr_opt)."')";
				if ($tabella==$selezionato["tabella"]) {
					$campo=$selezionato["campo"];
					$valore=$selezionato["valore"];
				}
				//$output[]="Array('$pratica','".$obj[$i]."',$id,$opt,'$campo','$valore','".str_replace("'","",$sql)."','')";
				$output[]=str_replace("\n","","Array('$pratica','".$obj[$i]."',$id,$opt,'$campo','$valore','','','$oggetto')");
			}
			else
				//$output[]="Array('','".$obj[$i]."','','','".str_replace("'","\'",$sql)."','Errore')";
				$output[]="Array('','".$obj[$i]."','','','','Errore','$oggetto')";
		}
		$output="new Array(".implode(",",$output).")";
		break;
	case "codice_fiscale":
		require_once("calcolacodicefiscale.php");
		$obj=$_GET["oggetto"];
		$cognome=$_GET['cognome'];
		$nome=$_GET['nome'];
		$sesso=$_GET['sesso'];
		$comune=$_GET['comunato'];
		$datanascita=$_GET['datanato'];
		$r=new risultato;
		$r=calcolacodicefiscale($cognome,$nome,$sesso,$comune,$datanascita);
		if (sizeof($r->errori)){
			$errors= "Si sono verificati i seguenti errori:";
			reset ($r->errori);
		    while (list ($key, $val) = each ($r->errori)) {
		        $errors.= ($key+1)." - $val;\n";
		    }
		} 
		else{
		    $codfis= $r->codicefiscale;
		}
		$output=json_encode(Array('$obj','$codfis','$errors'));
		break;
	default:
		break;
}
echo str_replace("\n","",$output);
?>

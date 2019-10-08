<?php
$active_form="cdu.vincoli.php?pratica=$idpratica";
function setData($val){
	if (strlen($val)>0){
		if (get_magic_quotes_runtime or get_magic_quotes_gpc) {
			$val="'".htmlentities($val)."'";
		}
		else{
			$val="'".htmlentities(addslashes($val),ENT_QUOTES)."'";
		}
	}
	elseif (strlen($val)===0) $val="NULL";
	return $val;
}

include_once ("login.php");
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$idpratica=$_POST["pratica"];
$azione=$_POST["azione"];
//print_array($_POST);

if(!$_POST["foglio"] && !$_POST["mappale"])	// EDIT VINCOLI
{
	$part=$_POST["part"];
	$fm=explode(',',$part);
	$sezione=$fm[0];
	$foglio=$fm[1];
	$mappale=$fm[2];
	$vincolo=$_POST["vincolo"];
	$sezione=$_POST["sezione"];
	$tavola=$_POST["tavola"];
	$perc=$_POST["perc_area"];
	$zona=$_POST["zona"];
    if($azione=="Aggiungi"){ 
		$sql_del="delete from cdu.mappali where foglio='$foglio' and mappale='$mappale' and coalesce(vincolo,'')='';";
		print_debug($sql_del); 
		$db->sql_query($sql_del); 
		$sql="insert into cdu.mappali (pratica,sezione,foglio,mappale,vincolo,tavola,zona,perc_area) values($idpratica,'$sezione','$foglio','$mappale','$vincolo','$tavola','$zona','$perc') ;";
		print "$sql"; 
		$db->sql_query($sql); 
	}
}
else {		// EDIT MAPPALI
	$sezione=setData($_POST["sezione"]);
	$foglio=setData($_POST["foglio"]);
	$mappale=setData($_POST["mappale"]);

	$sqlmappali="foglio=$foglio and mappale=$mappale";
	if (isset($_POST["sezione"])) $sqlmappali.=" and sezione=$sezione";
	if($azione=="Aggiungi"){ 
        $sql="SELECT coalesce(data_certificazione,CURRENT_DATE) as data FROM cdu.richiesta WHERE pratica=$idpratica;";
        $db->sql_query($sql);
        $data=$db->sql_fetchfield('data');
        
		$sql="insert into cdu.mappali (pratica,sezione,foglio,mappale,vincolo,tavola,zona,perc_area) 
		select $idpratica,particelle.sezione,particelle.foglio,particelle.mappale,zona_plg.nome_vincolo,zona_plg.nome_tavola,zona_plg.nome_zona,
		round(sum(area(intersection (particelle.".THE_GEOM.",zona_plg.the_geom))/area (particelle.".THE_GEOM.")*100)::numeric,1) from
		nct.particelle,(SELECT A.* FROM vincoli.zona_plg A inner join vincoli.zona B using(nome_vincolo,nome_tavola,nome_zona)  inner join vincoli.tavola using(nome_vincolo,nome_tavola) WHERE '$data'::date BETWEEN coalesce(data_da,'01/01/1970'::date) AND coalesce(data_a,CURRENT_DATE) AND cdu=1) as zona_plg
        WHERE $sqlmappali and (particelle.".THE_GEOM." && zona_plg.the_geom) and
		(area(intersection (particelle.".THE_GEOM.",zona_plg.the_geom))>10 or (area(intersection(particelle.".THE_GEOM.",zona_plg.the_geom))/area (particelle.".THE_GEOM.")*100)>=0.02) 

		group by particelle.sezione,particelle.foglio,particelle.mappale,zona_plg.nome_vincolo,zona_plg.nome_tavola,zona_plg.nome_zona,particelle.".THE_GEOM;

		$result=$db->sql_query ($sql);
		//echo "<p>$sql</p>";
		//$err=$db->sql_error();
		print_debug($sql); 
		//$numrows=$db->sql_affectedrows();

		//if($numrows===0 or $err["message"]){
		$sql="insert into cdu.mappali (pratica,sezione,foglio,mappale) values ($idpratica,$sezione,$foglio,$mappale)";
		$result=$db->sql_query ($sql); 
		//}
	}
	
}

if($azione=="Elimina"){ 	
	if($_POST["active_form"]=="cdu.richiesta.php"){ 
		$id=$_POST["id"];
		$sql="delete from cdu.mappali where id in(select q.id from cdu.mappali as p,cdu.mappali as q where p.foglio=q.foglio and p.mappale=q.mappale and p.id=$id);"; 
		
		$db->sql_query($sql); 
	}
	
	else if($_POST["active_form"]=="cdu.vincoli.php") { 
		$id=$_POST["idriga"];
		$sql_count="SELECT coalesce(count(*),0) as quantita FROM cdu.mappali where pratica=$idpratica and foglio='$foglio' and mappale='$mappale'";
		 
		$db->sql_query($sql_count);
		$quantita=$db->sql_fetchfield('quantita'); 
		if($quantita>1){
			$sql="delete from cdu.mappali where id=$id";
			print_debug($sql); 
			$db->sql_query($sql);

		}
		else{

			$sql="update cdu.mappali set sezione=NULL,vincolo=NULL,zona=NULL,tavola=NULL,perc_area='0' where id=$id";
			$db->sql_query($sql); 
		}
	}	

}
		
		

?>

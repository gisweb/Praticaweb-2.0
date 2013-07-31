<?
include_once ("login.php");
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
//print_r($_POST);
$idriga=$_POST["idriga"];
$fg=$_POST["foglio"];
$mp=$_POST["mappale"];
$db->sql_query ("SELECT coalesce(data_prot,data_presentazione) as data FROM pe.avvioproc WHERE pratica=$idpratica;");
$dataprot=$db->sql_fetchfield("data");
/*
$sql="select pratica from pe.vincoli where pratica=$idpratica;";

$db->sql_query ($sql);
$numrows=$db->sql_numrows();

if ($numrows>0) return;*/
$sezione=$_POST["sezione"];
if($_POST["foglio"] && $_POST["mappale"]) $sqlmappali=" and foglio='$fg' and mappale='$mp'";
if (isset($_POST["sezione"])) $sqlmappali.=" and sezione='$sezione'";
//print_array($_POST);
$sql="select sezione,foglio,mappale from pe.cterreni where pratica=$idpratica $sqlmappali;";
//echo "<p>$sql</p>";
$db->sql_query ($sql);
$mappali=$db->sql_fetchrowset(); 
$numrows=$db->sql_affectedrows();
//if (!$mappali) return;

unset ($sqlmappali);
foreach ($mappali as $mappale){ 
	unset ($sql);
	if ($mappale["sezione"]) $sql="sezione='".$mappale["sezione"]."' and ";
	if ($mappale["foglio"]) $sql.="foglio='".$mappale["foglio"]."' and ";
	if ($mappale["mappale"]) $sql.="mappale='".$mappale["mappale"]."' and ";
	$sqlmappali.="(".substr($sql,0,-5).") or ";
}
$sqlmappali=substr($sqlmappali,0,-4);

if($_POST["azione"]=="Elimina"){ //cancello i vincoli dopo aver eliminato dei mappali da cterreni

	$sql="delete from pe.vincoli where pratica=$idpratica and 
(vincolo,tavola,zona) in ( 
	(select 
		nome_vincolo,nome_tavola,nome_zona 
	from 
		nct.particelle,vincoli.zona_plg 
	where
		(particelle.bordo_gb && zona_plg.the_geom ) and 
		(area(intersection(particelle.bordo_gb,zona_plg.the_geom))>10 or (area(intersection(particelle.bordo_gb,zona_plg.the_geom))/area(particelle.bordo_gb)*100)>=0.02) and 
		--(foglio,mappale) in (select foglio,mappale from pe.cterreni where pratica=$idpratica)
		foglio='$partic[0]' and mappale='$partic[1]') 
EXCEPT 
	(select 
		nome_vincolo,nome_tavola,nome_zona 
	from
		nct.particelle,vincoli.zona_plg 
	where 
		(particelle.bordo_gb && zona_plg.the_geom ) and
		(area(intersection(particelle.bordo_gb,zona_plg.the_geom))>10 or (area(intersection(particelle.bordo_gb,zona_plg.the_geom))/area(particelle.bordo_gb)*100)>=0.02) and 
		(foglio,mappale) in (select foglio,mappale from pe.cterreni where pratica=$idpratica and id<>$idrow))
);";
	$db->sql_query ($sql);
}

elseif($_POST["azione"]=="Aggiungi"){
	$sql="insert into pe.vincoli (pratica,vincolo,tavola,zona) select DISTINCT $idpratica,nome_vincolo,nome_tavola,nome_zona  
	from nct.particelle,(SELECT A.* FROM vincoli.zona_plg A inner join vincoli.zona B using(nome_vincolo,nome_tavola,nome_zona) WHERE '$dataprot'::date BETWEEN coalesce(data_da,'01/01/1970'::date) AND coalesce(data_a,CURRENT_DATE) ) zona_plg where
	particelle.".THE_GEOM." && zona_plg.the_geom and 
	(area(intersection (particelle.".THE_GEOM.",zona_plg.the_geom))>10 or (area(intersection(particelle.".THE_GEOM.",zona_plg.the_geom))/area (particelle.".THE_GEOM.")*100)>=0.02) and 
	($sqlmappali) and nome_vincolo||nome_tavola||nome_zona not in (select vincolo||tavola||zona from pe.vincoli where pratica=$idpratica) group by nome_vincolo,nome_tavola,nome_zona";
	
	//if (DEBUG) echo $sql;
	$db->sql_query ($sql);
}
echo "<p>$sql</p>";

?>
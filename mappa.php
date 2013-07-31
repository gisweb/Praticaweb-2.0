<?
include_once("login.php");

$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
if (isset($_GET["mapkey"]))	$mapkey=explode('@',$_GET["mapkey"]);
//print_r ($mapkey);
$tipo=$mapkey[0];
$mappale=$mapkey[1];
$foglio=$mapkey[2];

$sql="select oid,extent(buffer(the_geom,".BUFFER_SIZE.")) from map.ct_particelle where foglio ilike('$foglio') and mappale ilike ('$mappale') group by oid;";

$result = $db->sql_query ($sql);
$extent = $db->sql_fetchrow();

$ext=$extent["extent"];
$objid=$extent["oid"];
if($ext){
	$p1=strpos($ext,"(")+1;
	$p2=strpos($ext,")");
	$ext=substr($ext,$p1,$p2-$p1); 
	$ext=str_replace(",","+",$ext);
	$ext=str_replace(" ","+",$ext);
	$ext2=str_replace("+",";",$ext);
	include "http://localhost/cgi-bin/mapserv.exe?map=C:\[pmapper]\projects\ceriale\map\catasto.map&mapext=$ext";
	
?>
<SCRIPT LANGUAGE="JavaScript1.2">
  function openPmapper(winwidth, winheight, gLanguage, startParameters){
  //CASO FULLSCREEN DA AGGIUNGERE ALLE POSSIBILITA
  //se il browser non riconosce la dimensione della finestra potrei passare un 800x600 
  //alert (screen.height);
  //return;
  
    if(document.all){
		myWidth = screen.availWidth-5;
		myHeight =screen.availHeight-30;
		mapw=screen.Width-280;
		maph=screen.Height-220;
	}
    else{
	
			
		//myWidth = screen.availWidth;
		//myHeight =screen.availHeight;
	}

myw=window.open("/pmapper/map.phtml?startmapsize=" + mapw + "," + maph + "&language=2" + startParameters, "MapServer", "width=" + myWidth + ",height=" + myHeight + ",menubar=no,scrollbar=auto,resizable=yes,top=0,left=0,status=no");
  }
</SCRIPT>

<a href="javascript:openPmapper(980, 700, 2, '&start=1&project=ceriale&theme=completatoctree&objlayer=particelle&objid=<?=$objid?>&zoomextent=<?=$ext2?>')"><p>Apri in Praticaweb Navigator</p></a>

<?
	
}
else{
echo "includo un file con il messaggio che non esiste in cartografia o l'oggetto o il livello di oggetti";


}
?>
<?php // content="text/plain; charset=utf-8"
// Gantt example
require_once 'login.php';
 //ini_set('intl.default_locale', 'it_IT');
require_once (APPS_DIR.'plugins/jpgraph/jpgraph.php');
require_once (APPS_DIR.'plugins/jpgraph/jpgraph_gantt.php');
$db=appUtils::getDB();
$pratica=$_REQUEST["pratica"];
$sql="SELECT *,to_char(coalesce(data_prot,data_presentazione),'YYYY-MM-DD') as data_inizio,to_char(coalesce(data_prot,data_presentazione)+60,'YYYY-MM-DD') as data_fine FROM pe.avvioproc WHERE pratica=?";
$ris=$db->fetchAll($sql,Array($pratica));
$nPratica=$ris[0]["numero"];
$dataProt=$ris[0]["data_inizio"];
$datafine=$ris[0]["data_fine"];
$sql="SELECT to_char(data_rilascio,'YYYY-MM-DD') as data_rilascio FROM pe.titolo WHERE pratica=?";
$ris=$db->fetchAll($sql,Array($pratica));
$titolo=$ris[0]["data_rilascio"];
// 
// The data for the graphs
//
//setlocale(LC_ALL, 'it_IT.utf8');
$dateLocale = new DateLocale(); 
// Use Swedish locale 
//$dateLocale->Set('it_IT.utf8');
$data = array(
  array(0,ACTYPE_GROUP,     "Tempo Totale",	$dataProt,$datafine)
  //array(1,ACTYPE_NORMAL,    "Istruttoria Preliminare","2011-12-13","2011-12-30"),
  //array(2,ACTYPE_NORMAL,    "Istruttoria Tecnica",      "2011-12-30","2012-01-18"),
  //array(3,ACTYPE_NORMAL,    "Istruttoria Amministrativa", "2012-01-18","2012-01-30") ,
  
);
if ($titolo) $data[]=array(4,ACTYPE_MILESTONE, "Rilascio Titolo", $titolo,'RILASCIO TITOLO');
// Create the basic graph
$graph = new GanttGraph();
$graph->title->Set("Scadenze Pratica n° ".$nPratica);

// Setup scale
//$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

// Add the specified activities
$graph->CreateSimple($data);

// .. and stroke the graph
$graph->Stroke();
//phpinfo();

?>



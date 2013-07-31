<?
define('NOME_COMUNE','Comune di .. Demo PraticaWeb');//nome completo del comune che compare nell'intestazione

define('DEBUG', 1); // Debugging 0 off 1 on
define('DB_HOST','127.0.0.1');
define('DB_NAME','gw_demo');
define('DB_USER','postgres');
define('DB_PWD','postgres');

define('MENU',DATA_DIR."praticaweb/mnu/");//cartella contenente la  configurazione dei menu
define('TAB',DATA_DIR."praticaweb/tab/");//cartella contenente la  configurazione dei forms via file tab
define('TAB_ELENCO',DATA_DIR."praticaweb/tab_elenco/");//cartella con elenchi testuali

define('MODELLI',DATA_DIR."praticaweb/modelli/");//cartella con i modelli di stampa 
define('STAMPE',DATA_DIR."praticaweb/stampe/");//cartella con le stampe
define('MODELLI_DIR',DATA_DIR."praticaweb/modelli/");//cartella con i modelli di stampa 
define('STAMPE_DIR',DATA_DIR."praticaweb/stampe/");//cartella con le stampe
define('DEBUG_DIR',DATA_DIR."praticaweb/debug/");//cartella con i debug
define('LIB',DATA_DIR."praticaweb/lib/");
define('ALLEGATI',DATA_DIR."praticaweb/file_allegati/");//cartella dei file allegati sotto praticaweb

define('HOME',"index.php");
define('URL_ALLEGATI','allegati/');//url relativo dei file allegati con / finale

define('MESE_ONERI',3);
define('GIORNO_ONERI',30);
define('ENABLE_D2',1);

define('AREA_MIN','5');//area minima di intersezione per le query di overlay

define('SELF',$_SERVER["PHP_SELF"]);

define('THE_GEOM','bordo_gb');
define('MAPPA_PRATICHE','vezzano_demo');
define('LAYER_MAPPALI','particelle');
define('LAYER_CIVICI','civici');
define('VERSION','gisclient2');
define('TEMPLATE','gisclient');

//in sessione per pmapper
$_SESSION['USER_DATA']=DATA_DIR;


//includo il file per il database in uso
include_once (LIB."wrapdb/postgres.php");
include_once (LIB."utils/debug.php")
?>

<?php
    function loadLibs(){
        if($_SESSION["USER_ID"]==1)
            $libs=Array("pratica.class.php","app.utils.class.php","utils.class.php","menu.class.php","mail.class.php");
        else
            $libs=Array("pratica.class.php","app.utils.class.php","utils.class.php","menu.class.php","mail.class.php");
        foreach($libs as $lib){
	    
            if (file_exists(LOCAL_LIB.$lib)){
                require_once LOCAL_LIB.$lib;
//                if ($_SESSION["USER_ID"]==1) print "<p>Loading ".LOCAL_LIB.$lib."</p>";
            }
            elseif(file_exists(APPS_DIR."lib".DIRECTORY_SEPARATOR.$lib)) {
                require_once LIB.$lib;
//                if ($_SESSION["USER_ID"]==1) print "<p>Loading ".LIB.$lib."</p>";
            }
            else die("impossibile caricare la libreria $lib");
        }
    };
    error_reporting(E_ERROR);
    
	if (!session_id())
	session_start();
        
        $appsDir=  getenv('PWAppsDir');
        $dataDir=  getenv('PWDataDir');
        if (!$dataDir) die("Manca la variabile d'ambiente PWDataDir nel file di configurazione di Apache.");
        if (!$appsDir) die("Manca la variabile d'ambiente PWAppsDir nel file di configurazione di Apache.");
        define('DATA_DIR',$dataDir);
        define('APPS_DIR',$appsDir);

	/*AGGIUNTA DI UN PATH PER LE LIBRERIE GLOBALI*/
	$path = '/apps/php-lib/mail';
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	if (!file_exists(DATA_DIR.'config.php')) die("Nessun file di configurazione trovato!");
        require_once DATA_DIR.'config.php';
        loadLibs();
		
	
	if (!defined('PRINT_VERSION')) define('PRINT_VERSION',2);
				 
	if ((defined('UPDATE_SW') && UPDATE_SW==1 && $_SESSION["USER_ID"]>4)){
            require_once "aggiornamento.php";
            exit;
        }
	//per il debug
	$dbconn=new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$dbconn->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME." sulla porta ".DB_PORT);
	//Se sto validando l'utente includo la validazione, se va male esco altrimenti continuo a caricare la pagina stessa
	
	if(isset($_POST['entra'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		if((!$username) || (!$password)){
	 		include_once "./admin/enter.php";
			exit;
		}
		else
			include_once "./admin/controlla_utente.php";
	}	
	//Se la sessione non � impostata mi devo nuovamente loggare
	if (!isset($_SESSION["USER_ID"])) {
		include_once "./admin/enter.php";
		exit;
	}
	//Se mi porto dietro i get e/o i post riscrivendoli sulla pagina di enter  posso recuperarli quando mi loggo
 ?>

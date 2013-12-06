<?php
	error_reporting(E_ERROR);
    //error_reporting(E_ALL);
	if (!session_id())
		session_start();
	$hostname=$_SERVER["HTTP_HOST"];
	$tmp=explode(".",$hostname);

	$user_data=$tmp[0];
	if($user_data=='mappe') $user_data='savona';
	$user_domain=$tmp[1];
    
	if (stristr(PHP_OS, 'WIN')){
		if(in_array('castor',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("E:","Dati",$user_data,"pe")).DIRECTORY_SEPARATOR);
			define('APPS_DIR',implode(DIRECTORY_SEPARATOR,Array("E:","Applicazioni","praticaweb-2.0")).DIRECTORY_SEPARATOR);
		}
		elseif(in_array('becrux',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","ms4w",'data',$user_data,"pe")).DIRECTORY_SEPARATOR);
			define('APPS_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","ms4w","praticaweb-2.0")).DIRECTORY_SEPARATOR);
		}
		elseif(in_array('deneb',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","Applicazioni",'data',$user_data,"pe")).DIRECTORY_SEPARATOR);
			define('APPS_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","Applicazioni","apps","praticaweb-2.0")).DIRECTORY_SEPARATOR);
		}
		else{
			//TODO
		}
		
	}
	else{
            if ($hostname=='192.192.193.236' || $hostname=='vm-svsit') $user_data='savona';
		define('DATA_DIR',DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,Array("data",$user_data,"pe")).DIRECTORY_SEPARATOR);
		define('APPS_DIR',DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,Array("apps",'praticaweb-2.0')).DIRECTORY_SEPARATOR);
	}
	
	
	include_once DATA_DIR.'config.php';
	require_once DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."pratica.class.php";
        require_once DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."app.utils.class.php";
        require_once DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."utils.class.php";
	require_once APPS_DIR."lib".DIRECTORY_SEPARATOR."menu.class.php";
	
	//per il debug
	$dbconn=new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$dbconn->db_connect_id)  die( "Impossibile connettersi al database");
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
	//Se la sessione non è impostata mi devo nuovamente loggare
	if (!isset($_SESSION["USER_ID"])) {
		include_once "./admin/enter.php";
		exit;
	}
	//Se mi porto dietro i get e/o i post riscrivendoli sulla pagina di enter  posso recuperarli quando mi loggo
 ?>

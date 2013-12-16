<?php
    function loadLibs(){
        $libs=Array("app.utils.class.php","utils.class.php","message.class.php");
        foreach($libs as $lib){
            if (file_exists(LOCAL_LIB.$lib)){
                require_once LOCAL_LIB.$lib;
            }
            else{
                require_once LIB.$lib;
            }
        }
    };
    error_reporting(E_ERROR);
    //error_reporting(E_ALL);
	if (!session_id())
		session_start();
	$hostname=$_SERVER["HTTP_HOST"];
	$tmp=explode(".",$hostname);

	$user_data=$tmp[0];
	if($user_data=='mappe') $user_data='savona';
	$user_domain=$tmp[1];
    define('APPS_DIR',getcwd().DIRECTORY_SEPARATOR);
	if (stristr(PHP_OS, 'WIN')){
		if(in_array('castor',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("E:","Dati",$user_data,"pe")).DIRECTORY_SEPARATOR);
		}
		elseif(in_array('becrux',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","ms4w",'data',$user_data,"pe")).DIRECTORY_SEPARATOR);	
		}
		elseif(in_array('deneb',$tmp)){
			define('DATA_DIR',implode(DIRECTORY_SEPARATOR,Array("D:","Applicazioni",'data',$user_data,"pe")).DIRECTORY_SEPARATOR);
		}
		else{
			//TODO
		}
		
	}
	else{
            if ($hostname=='192.192.193.236' || $hostname=='vm-svsit') $user_data='savona';
		define('DATA_DIR',DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,Array("data",$user_data,"pe")).DIRECTORY_SEPARATOR);
	}
	
	
	include_once DATA_DIR.'config.php';
    loadLibs();

	
	//per il debug
	$db=utils::getDoctrineDB();
	
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
	$_SESSION["USER_ID"]=1;
	if (!isset($_SESSION["USER_ID"])) {
		include_once "./admin/enter.php";
		exit;
	}
	//Se mi porto dietro i get e/o i post riscrivendoli sulla pagina di enter  posso recuperarli quando mi loggo
 ?>

<?php

$azione=  strtolower($_REQUEST["azione"]);
$modo=($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$pr=new pratica($idpratica,$app);

if (in_array($azione, Array("salva","elimina"))){
    $modo=($azione=='elimina')?("list"):("view");
    $id=($_SESSION["ADD_NEW"])?($_SESSION["ADD_NEW"]):($_REQUEST["id"]);
    if ($azione=='elimina'){
        require_once 'db.savedata.php';
        $fName=$_REQUEST['file_doc'];
        $r=unlink ($pr->documenti. $fName);
        
    }
    elseif ($_FILES['file']['tmp_name']){
        $fName=($_REQUEST['file_doc'])?($_REQUEST['file_doc']):($_FILES['file']['name']);
        $_POST['file_doc']=$fName;
        if(!pathinfo($fName,PATHINFO_EXTENSION)){
            $ext=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
            $fName.=".$ext";
            $_POST['file_doc']=$fName;
        }
        require_once 'db.savedata.php';
        if (file_exists($pr->documenti. $fName)){
            $r=unlink ($pr->documenti. $fName);
            if (!$r) echo "<p>Impossibile rimuovere il file ".$pr->documenti."$fName</p>";
        }
        
        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $pr->documenti. $fName)) { 
          print("***ERROR: Non è possibile copiare il file.<br />\n". $pr->documenti. $fName); 
	    }
    }
	elseif ($_REQUEST["file_doc"] && $_REQUEST["old_name"]){
            $newName=$pr->documenti.$_REQUEST['file_doc'];
            $oldName=$pr->documenti.$_REQUEST['old_name'];
            if (rename($oldName,$newName)){
                require_once 'db.savedata.php';
            }
            else{
                $message=<<<EOT
<p style="color:red;font-weight: bold;">ERRORE: Non è possibile rinominare il file %s in %s</p>
EOT;
                $message=sprintf($message,$oldName,$fName);
                print $message;
            }


    }

     $modo='list';
}
elseif($_POST["azione"]=="Invia Prisma"){
	
	$includeFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."init.stp.documenti.php";
	$pratica = $_REQUEST["pratica"];
	$file_doc = $_REQUEST["file_doc"];
	$user = $_SESSION["USER_ID"];
	$data = date("d/m/Y");
	$tms = time();
	$uid = $user;
	$nota = "File $file_doc inviato a Prisma";
	$data = Array($pratica,$data,$nota,$user,$tms,$uid);
	$pr = new pratica($pratica);
	$id = $_REQUEST["id"];
	//print_array($pr);
	
	$numero = $pr->numero;
	$fascicolo = $pr->info["fascicolo"];
	$anno = $pr->info["anno_fascicolo"];
	$descrizione = ($_REQUEST["descrizione"])?($_REQUEST["descrizione"]):("Inserimento documento $file_doc della pratica $numero.");
	$fname = $pr->documenti.$file_doc;
	$dbh = utils::getDb();
	$sql = "SELECT username FROM admin.users_ads WHERE userid=?";
	$stmt = $dbh->prepare($sql);
	if($stmt->execute(Array($user))){
		$userAds = $stmt->fetchColumn(0);
	}
	if ($userAds and $fascicolo){
		if(file_exists($includeFile)){
			require_once DATA_DIR."config.ads.php";
			require_once LOCAL_LIB."WSSoapClient.php";
			define('TEMPLATE_FILE',DATA_DIR."praticaweb/scripts/template.php");
			$client = new SoapClient(WSDL_LOGIN_URL);
			require_once TEMPLATE_FILE;
			require_once $includeFile;
			$res = $client->login(Array("strCodEnte"=>CODICE_ENTE,"strUserName"=>SERVICE_USER,"strPassword"=>SERVICE_PASSWD));
			$r = json_decode(json_encode($res),true);

			$strDST=$r["LoginResult"]["strDST"];

			$clientCreation = new SoapClient(WSDL_DOCUM_URL,array("login"=>SERVICE_USER,"password"=>SERVICE_PASSWD,"trace" => true,'exceptions' => true));
			$result = creaDocumento($clientCreation,$userAds,$fascicolo,$anno,$descrizione,$id,$fname);
			if($result["success"]==1){
				$idExt = $result["data"]["idDocumentoEsterno"];
				$sql = "UPDATE stp.stampe SET external_id = ?, descrizione = ? WHERE id = ?;";
				$stmt = $dbh->prepare($sql);
				if(!$stmt->execute(Array($idExt,$descrizione,$id))){
					echo "<p>Errore nell'aggiornamento del documento</p>";
				}
				$sql = "INSERT INTO pe.annotazioni(pratica,data_annotazione,note,utente,tmsins,uidins) VALUES(?,?,?,?,?,?)";
				$stmt = $dbh->prepare($sql);
				if(!$stmt->execute($data)){
					echo "<p>Errore nell'inserimento dell'annotazione</p>";
				}
			}
			else{
				echo "<p>Errore nell'inserimento del file su Prisma</p>";
			}
		}
	}
	elseif(!$fascicolo){
		print "<h3 style='color:red;font-weight:bold'>Impossibile inviare il documento, manca il fascicolo</h3>";
	}
	elseif(!$userAds){
		print "<h3 style='color:red;font-weight:bold'>Impossibile inviare il documento, nessuno utente registrato su Prisma</h3>";
	}
	
	
	
	$modo="list";
}
elseif($azione=="annulla"){
    $modo='list';
}
if ($is_cdu) $active_form="cdu.iter.php?pratica=$idpratica";
elseif($is_ce) $active_form="ce.iter.php?pratica=$idpratica";
elseif($is_vigi) $active_form="vigi.iter.php?pratica=$idpratica";
else
$active_form="stp.documenti.php?mode=$modo&pratica=$idpratica";
?>

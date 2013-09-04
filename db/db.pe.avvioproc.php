<?//creare un trigger sul db per la numerazione automatica
	//per ora calcolo qui il nuovo numero pratica senza controlli
	//CREARE UN TRIGGER CHE AGGIORNA PRATICA A ID NELLA TABELLA AVVIOPROC 
	//UTILIZZARE UNA TRANSAZIONE PER L'EREDITARIETA DEI DATI DELLA NUOVA PRATICA
	
	//se ho annullato esco
	if ($_POST["azione"]=="Annulla"){
		$active_form.="?pratica=$idpratica";
		return;
	}
	//se ho già inserito il record recupero l'IDpratica ed esco
	if (($_POST["mode"]=="new") && ($_SESSION["ADD_NEW"])){
		$idpratica=$_SESSION["ADD_NEW"];
		$ERRMSG= "Il record " . $_SESSION["ADD_NEW"] . " è già stato inserito! ";
		return;
	}
	if($_REQUEST['mode']=='edit'){
		$prPrec=new pratica($idpratica);
		$db=$prPrec->db1;
		$pratPrec=$db->fetchAssoc("SELECT * FROM pe.avvioproc WHERE pratica=?",Array($idpratica));
        }
	//Modulo condiviso per la gestione dei dati
	include_once "./db/db.savedata.php";
	
	$d_resp=($_REQUEST['data_resp'])?($_REQUEST['data_resp']):("now");
	$d_respIT=($_REQUEST['data_resp_it'])?($_REQUEST['data_resp_it']):("now");
	$d_respIA=($_REQUEST['data_resp_ia'])?($_REQUEST['data_resp_ia']):("now");
	
	if ($_POST["mode"]=="new"){
            $idpratica=$_SESSION["ADD_NEW"];

            $pr=new pratica($idpratica);
            $pr->addRecenti();
            $pr->nuovaPratica(Array("data_resp"=>$d_resp,"data_resp_it"=>$d_respIT,"data_resp_ia"=>$d_respIA));
            //numerazione automatica
            $data_presentazione=$_POST["data_presentazione"];
            $tipo_pratica=$_POST["tipo"];
            $resp_proc=$_POST['resp_proc'];
            //$pr->addScadenze($tipo_pratica);
        //$numero=preg_replace("|([^A-z0-9\-]+)|",'',str_replace('/','-',str_replace('\\','-',$numero)));
		//Aggiungo il menù della nuova pratica alla tabella menu	
		$menu->list_menu($idpratica,$_POST["tipo"]);
		
		
		//Inserisco le scadenze per inizio lavori DIA
		if ($_POST["tipo"]>=10000 && $_POST["tipo"]<11000){
			$data_prot=$_POST["data_prot"];
			$pr->setDateLavori($data_prot);
		}

		//inserisco i dati relativi ai riferimenti:
            if ($_POST["rif_aut_amb"]){
                $ref_amb=$_POST["rif_aut_amb"];
                $sql="UPDATE pe.avvioproc SET aut_amb=(SELECT pratica FROM pe.avvioproc WHERE numero='$ref_amb') WHERE pratica=$idpratica";
                $db->sql_query($sql); 
                $ref=$ref_amb;
                $sql="SELECT pratica FROM pe.avvioproc WHERE numero='$ref'";
                $db->sql_query($sql);
                $refid=$db->sql_fetchfield('pratica');
			include ("db.pe.importa_pratica.php");
            }
            else{
		$sql="UPDATE pe.avvioproc SET aut_amb=null WHERE pratica=$idpratica";
                $db->sql_query($sql);   
            }
            if ($_POST["rif_pratica"]){
                //esiste la pratica di riferimento importo tutti i dati della pratica di riferimento
                $ref=$_POST["rif_pratica"];
                $sql="SELECT pratica FROM pe.avvioproc WHERE numero='$ref'";
                $db->sql_query($sql);
                $refid=$db->sql_fetchfield('pratica');
                include ("db.pe.importa_pratica.php");
            }
            elseif ($_POST["riferimento"]){
                $newref=$_POST["riferimento"];
                $newref=addslashes($newref);
                //aggiungo il riferimento e lo assegno alla pratica
                $db->sql_query ("insert into pe.riferimenti (descrizione) values ('$newref')");
                $idref=$db->sql_nextid();
                $db->sql_query ("update pe.avvioproc set riferimento=$idref where pratica=$idpratica");
                //aggiungo i riferimenti territoriali inseriti: via e civico
                if($_POST["via"]){
                        $sqlcampi="via";
                        $sqlvalori="'".htmlentities(addslashes($_POST["via"]))."'";
                        if($_POST["civico"]){
                                $sqlcampi.=",civico";
                                $sqlvalori.=",'".htmlentities(addslashes($_POST["civico"]))."'";
                        }
                        $sql="insert into pe.indirizzi (pratica,$sqlcampi,uidins,tmsins) values ($idpratica,$sqlvalori,".$_SESSION["USER_ID"].",".time().")"; 
                        //if(DEBUG) echo $sql;
                        $db->sql_query($sql);
                }
			//aggiungo i riferimenti territoriali inseriti: catasto terreni
			if($_POST["ctfoglio"]){
				$sqlcampi="foglio";
				$sqlvalori="'".htmlentities(addslashes($_POST["ctfoglio"]))."'";
				if($_POST["ctsezione"]){
					$sqlcampi.=",sezione";
					$sqlvalori.=",'".htmlentities(addslashes($_POST["ctsezione"]))."'";
				}
				if($_POST["ctmappale"]){
					$sqlcampi.=",mappale";
					$sqlvalori.=",'".htmlentities(addslashes($_POST["ctmappale"]))."'";					
				}
				$sql="insert into pe.cterreni (pratica,$sqlcampi,uidins,tmsins) values ($idpratica,$sqlvalori,".$_SESSION["USER_ID"].",".time().")"; 
				//if(DEBUG) echo $sql;
				$db->sql_query($sql);
			}
		}
	}//fine sezione nuova pratica
	
	//aggiorno una pratica esistente
	elseif($_POST["mode"]=="edit"){
            $pr=new pratica($idpratica);
            //devo solo controllare se è stato cambiato il tipo di pratica: in questo caso aggiorno il menu
            $tipo=$_POST["tipo"];
            $oldtipo=$_POST["oldtipo"];
            if ($tipo!=$oldtipo)
                    $menu->change_menu($idpratica,$oldtipo,$tipo);
            $menu->add_menu($idpratica,60);
            if ($_POST["rif_aut_amb"]){
                $ref_amb=$_POST["rif_aut_amb"];
                $sql="UPDATE pe.avvioproc SET aut_amb=(SELECT pratica FROM pe.avvioproc WHERE numero='$ref_amb')  WHERE pratica=$idpratica";
                $db->sql_query($sql);   
            }
            else{
		$sql="UPDATE pe.avvioproc SET aut_amb=null  WHERE pratica=$idpratica$";
                $db->sql_query($sql);   
            }
		
            if($pratPrec['resp_proc']!=$pr->info["resp_proc"]) 
                    $pr->addTransition(Array('codice'=>'rardp',"utente_fi"=>$pr->info["resp_proc"],"data"=>$d_resp));	
            if($pratPrec['resp_it']!=$_REQUEST['resp_it'])
                    $pr->addTransition(Array('codice'=>'raitec',"utente_fi"=>$pr->info["resp_it"],"data"=>$d_respIT));	
            if($pratPrec['resp_ia']!=$_REQUEST['resp_ia']) 
                    $pr->addTransition(Array('codice'=>'raiamm',"utente_fi"=>$pr->info["resp_ia"],"data"=>$d_respIA));	
	}
	$db->sql_close();
	$active_form.="?pratica=$idpratica";  
	//IN TUTTI I db.mioform.php risetto i parametri per active_form da passare all'iframe
	//$active_form.="?pratica=$idpratica&id=$id&ruolo=$ruolo";
	
	if (file_exists(DATA_DIR."praticaweb/db/db.pe.avvioproc.php")){
            require_once DATA_DIR."praticaweb/db/db.pe.avvioproc.php";
       }
	
?>

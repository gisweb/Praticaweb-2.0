<?php
use Doctrine\Common\ClassLoader;
require_once APPS_DIR.'plugins/Doctrine/Common/ClassLoader.php';
require_once APPS_DIR.'lib/tabella.class.php';

class savedata{
	var $db;
	var $tabella;
	var $schema;
	var $tb;
	var $campi_obbl;
	var $array_config;
	var $azione;
	var $config_file;
	var $modo;
	var $data;
	var $isElenco;
	var $pratica;
	var $id;
	var $message=Array(
			"noconfigfile"=>"Nessun file di configurazione definito",
			"noaction"=>""
		);
	
	function __construct($dati,$activeForm,$pratica=null){
		if(!$dati['config_file']){
			$mex=$this->message['noconfigfile'];
			print "<p class='error'>".$mex."</p>";
			return Array("status"=>-1,$mex);
		}
		if (!$dati['azione']){
			$mex=$this->message['noaction'];
			print "<p class='error'>".$mex."</p>";
			return Array("status"=>-1,$mex);
		}
		$this->active_form=$activeForm;
		$this->db=$this->setDB();
		$this->init($dati);
		$this->id=$dati["id"];
		$this->pratica=$pratica;
		switch($this->azione){
			case "annulla":
				return;
				break;
			case "elimina":
				if (!$this->id) $this->id=$dati["idriga"];
				$db->delete($this->tb,Array("id"=>$this->id));
			case "salva":
			case "aggiungi":
				$res=$this->save($dati);
				if ($res["status"]==-1){
					$Errors=$res["errors"];
					include $this->activeForm;			
					exit;
				}
				elseif($res["status"]==-2 || $res["status"]==-2){
					print "<p class='error'>".$res["errors"]."</p>";
				}
				break;
			default:
				break;
		}
		
	}
	private function init(){
		$this->azione=strtolower($dati["azione"]);
		$this->config_file=$dati["config_file"];
		$this->modo=$dati['mode'];
		$tb=new Tabella($config_file,$modo);
		$this->campi_obbl=$tb->campi_obbl;
		$this->array_config=$tb->tab_config;
		list($this->schema,$this->tabella)=explode('.',$tb->tabelladb);
		$this->tb=$tb->tabelladb;
		$this->isElenco=$tb->table_list;
		
	}
	private function validaDati($dati){
		//dall'array tratto dal file di configurazione crea l'array campi=>valori validati per il db
		$OK_Save=1;
		$db = $this->db;
		//Controllo dei campi obbligatori
		if (isset($this->campi_obbligatori)){
			foreach($this->campi_obbligatori as $c){
				if (strlen(trim($_POST[trim($c)]))==0){
					$errors[trim($c)]="Campo Obbligatorio";
					$OK_Save=0;
				}
			}
		}
		for ($i=0;$i<count($this->array_config);$i++){
			$row_config=$this->array_config[$i];
			foreach($row_config as  $r)
				$array_def[]=explode(';',$r);
		}
		foreach($array_def as $def){
			$campo=$def[1];
			$tipo=trim($def[3]);
			$val=trim($dati[$campo]);
			switch ($tipo) {
				case "idriga":	
					$val=''; //inutile metterlo nella query
					break;
				case "pratica":
					if (strlen(trim($val))>0){
						$pr=$db->fetchColumn("SELECT pratica FROM pe.avvioproc WHERE numero=?",Array($db->quote($val)));
						if (!$pr) {
							$OK_Save=0;
							$errors[$campo]="La pratica $val non esiste";
						}
						else
							$val=$db->quote($val);
					}
					else
						$val=null;
					break;
				case "text":	
				case "textarea":
				case "richtext":
				case "autosuggest":
					$val=(strlen(trim($val))>0)?($db->quote($val)):(null);
					break;
				case "data":
					if (strlen(trim($val))>0){
						$result=$db->fetchColumn("SELECT '?'::date as result",Array($val));
						if(!$result){
							$OK_Save=0;
							$errors[$campo]="Formato della data non valido $val";
						}
					}
					else
						$val=null;
					
					break;
				case "select":
					if ($val) $val=$db->quote($val);
					break;

				case "multiselectdb":
					if (is_array($val) && count($val)){
						$val=implode(',',$val);
					}
					else
						$val=null;
					break;				
				case "selectdb":
				case "selectRPC":
					if ($val==-1) {
						$OK_Save=0;
						$errors[$campo]=($campo=="tipo_allegati")?("Impossibile modificare il tipo per allegati. Prima di modicarlo rimuovere tutti gli allegati presenti"):("Errore generico");
					}
					elseif(strlen(trim($val))==0) $val=null;
					elseif(!is_numeric($val)) $val=$db->quote($val);

				case "elenco":
					break;
				case "ora":
					$val=str_replace(",",".",$val);
					$val=str_replace(".",":",$val);
					if (strlen(trim($val))>0){
						$result=$db->fetchColumn("SELECT '?'::time as result",Array($val));
						if(!$result){
							$OK_Save=0;
							$errors[$campo]="Formato dell\'ora non valido $val";
						}
					}
					else
						$val=null;

					break;	
				case "superficie":
				case "volume":
				case "numero":
				case "valuta":
					$val=str_replace(",",".",$val);
					if (strlen($val) and !is_numeric($val)){
						$OK_Save=0;
						$errors[$campo]="Dato non numerico";
					}
					//else if (strlen($val)==0) $val=0.00;
					break;	
				case "intero": 
					if (is_numeric($val)) $val=(int)$val;
					$val=str_replace(","," ",$val);
					if (strlen($val) and !is_numeric($val) and (!ereg("/^[0-9]{1,12}$/"))){
						$OK_Save=0;
						$errors[$campo]="Dato non numerico";
					}
					//else if (strlen($val)==0) $val=0.00;
					break;		
				case "bool":
					($val="SI")?($val="'t'"):($val="'f'");
					break;
				case "yesno": 
					if ($val=='SI')
						$val=1;
					else if ($val=='NO')
						$val=0;
					break;	
				case "checkbox":
				case "semaforo":
					if ($val=='on')
						$val=1;
					else
						$val=0;
					break;	
				case "radio":
					$arvalue=$_POST[$campo];
					break;

			}
			if(($tipo!="button") and ($tipo!="submit"))
				$array_data[$campo]=$val;

		}

		return array("data"=>$array_data,"errors"=>$errors);
	}
	private function validaCampi($arr){
		foreach ($arr as $key=>$value){
			switch($key){
				case "codfis":
					$val=str_replace(" ","",$value);
					break;
				default:
					$val=$value;
					break;
			}
			$ris[$key]=$val;
		}
		return $ris;
	}
	function save($dati){
		$db=$this->db;
		$array_dati=$this->validaDati($dati);
		
		if($array_dati["errors"]){
			return Array("status"=>-1,"errors"=>$array_dati["errors"]);
		}
		$Dati=$this->validaCampi($array_dati["data"]);
		
		if($this->modo=="edit"){
			if (!$this->isElenco){
                $chkret=0;
                $chkret = $db->fetchColumn("select coalesce(chk,0) as chk from ? where id=?",Array($this->tb,$this->id));
                if (!($chkret==$dati["chk"])){
                    return Array("status"=>-1,"errors"=>Array("Multiutenza"=> "Un altro utente ha salvato il record, oppure è gia stato salvato.....aggiornare  il form"));
                }
                $Dati["chk"]=++$chkret;
                $Dati["uidupd"]=$_SESSION["USER_ID"];
                $Dati["tmsupd"]=time();
            }
			try{
				$result=$db->update($this->tb,$Dati,Array("id"=>$this->id));
			}
			catch(Exception $e){
				return Array("status"=>-3,"errors"=>$e->errorInfo[2]);
			}
		}
		elseif($this->modo=="new"){
			if ($_SESSION["ADD_NEW"]){
				return Array("status"=>-2,"errors"=> "Il record è già stato inserito ".$_SESSION["ADD_NEW"]);
			}
			if (!$this->isElenco){
				$Dati["pratica"]=$idpratica;
                $Dati["chk"]=1;
                $Dati["uidins"]=$_SESSION["USER_ID"];
                $Dati["tmsins"]=time();
			}
			else{
				$Dati['id']=$db->fetchColumn("SELECT max(id)+1 FROM ?",Array($this->tb));
			}
			try{
				$result=$db->insert($this->tb,$Dati);
				$_SESSION["ADD_NEW"]=($this->isElenco)?($Dati['id']):($this->getLastId($$this->schema, $this->tabella));
			}
			catch (Exception $e){
				return Array("status"=>-3,"errors"=>$e->errorInfo[2]);
			}
		}
		return Array("status"=>1);
	}
	function setDB(){
		$classLoader = new ClassLoader('Doctrine', APPS_DIR.'plugins/');
		$classLoader->register();
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array(
			'dbname' => DB_NAME,
			'user' => DB_USER,
			'password' => DB_PWD,
			'host' => DB_HOST,
			'port' => DB_PORT,
			'driver' => DB_DRIVER,
		);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		return $conn;
	}
	function getLastId($sk,$tb){
		$db=$this->db;
		$sql="select array_to_string(regexp_matches(column_default, 'nextval[(][''](.+)['']::regclass[)]'),'') as sequence from information_schema.columns where table_schema=? and table_name=? and column_default ilike 'nextval%'";
		$sequence=$db->fetchColumn($sql,Array($sk,$tb));
		return $db->fetchColumn("select currval('$sequence')");
	}
	
}

?>
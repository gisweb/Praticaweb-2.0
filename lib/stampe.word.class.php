<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new
 *
 * @author marco
 */
//require_once APPS_DIR."plugins/phpWord.php";

function decode(&$item, &$key){
	$item=(mb_detect_encoding($item)=='UTF-8' && FALSE)?(utf8_decode($item)):($item);
}
require_once APPS_DIR."plugins/openTbs/tbs_class_php5.php";
require_once APPS_DIR."plugins/openTbs/tbs_plugin_opentbs.php";

class wordDoc {
	var $db;
	var $modello;
	var $pratica;
	var $viste;
	var $funzioni;
	var $data;
	var $schema='stp';
	var $modelliDir;
	
	function __construct($modello,$pratica){
		$this->db=appUtils::getDb();
		$db=$this->db;
		$this->modello=$modello;
		$this->pratica=$pratica;
		$sql="SELECT * FROM stp.e_modelli WHERE id=?";
		$ris=$db->fetchAssoc($sql,Array($modello));
		$this->type=(strpos($ris["form"],'cdu.')!==FALSE)?(1):(0);
		$this->modello=$ris["nome"];
		$this->viste=explode(',',$ris["views"]);
		$this->funzioni=explode(',',$ris["functions"]);
		$this->modelliDir=DATA_DIR.DIRECTORY_SEPARATOR."praticaweb".DIRECTORY_SEPARATOR."modelli".DIRECTORY_SEPARATOR;
		$info=pathinfo($this->modello);
		$this->basename=$info["filename"];
		$this->extension=$info["extension"];
		$this->docName=$this->pratica."-".$this->modello;
		$this->actions=$ris["action"];
	}
	private function getData(){
		$db=$this->db;
		for($i=0;$i<count($this->viste);$i++){
			$vista=$this->viste[$i];
			if ($vista){
				$sql="SELECT * FROM ".$this->schema.".$vista WHERE pratica=?";
				
				$ris=$db->fetchAll($sql,Array($this->pratica));
				array_walk_recursive($ris, 'decode');
				$this->data[$vista]=$ris;
					
				
			}
			
		}
		for($i=0;$i<count($this->funzioni);$i++){
			$funzione=$this->funzioni[$i];
			if ($funzione){
				$sql="SELECT * FROM ".$this->schema.".$funzione(?);";
				$ris=$db->fetchAll($sql,Array($this->pratica));
				array_walk_recursive($ris, 'decode');
				$this->data[$funzione]=$ris;
			}
		}
		$customData=$this->data;
		switch($this->type){
			case 1:
				if(file_exists(LOCAL_INCLUDE."cdu.php")){
					include_once LOCAL_INCLUDE."cdu.php";
				}
				
				break;
			default:
				if(file_exists(LOCAL_INCLUDE."stampe.php")){
					include_once LOCAL_INCLUDE."stampe.php";
				}
				break;
		}
		$this->data=$customData;
	}
	function createDoc($test=0){
		$TBS = new clsTinyButStrong; // new instance of TBS
		$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

		$this->getData();
		$TBS->LoadTemplate($this->modelliDir.$this->modello, OPENTBS_ALREADY_XML);	
		$TBS->SetOption('noerr',true);
		//$template = $PHPWord->loadTemplate($this->modelliDir.$this->modello);
		foreach($this->data as $tb=>$data){
			if (is_array($data))
				$TBS->MergeBlock($tb, $data);
			else
				$TBS->MergeField($tb,$data);

		}
		$TBS->MergeField("data", date("d/m/Y"));
		$pr=new pratica($this->pratica,$this->type);
		if ($test==1){
			$TBS->Show(TBS_OUTPUT);
		}
		else{
			$TBS->Show(OPENTBS_FILE,$pr->documenti.$this->docName);
			//echo $pr->documenti.$this->docName;
		}
		//print_array($this->data);
	} 
}
?>

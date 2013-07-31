<?php
class print_model{
	var $tag=Array(
		"inizio_ciclo"=>'|<span class="iniziocicli">(.*)IN_CICLO(.*)</span>|Umi',
		"fine_ciclo"=>'|<span class="finecicli">(.*)FI_CICLO(.*)</span>|Umi',
		"inizio_se"=>'|<span class="iniziose">(.*)INIZIO_SE(.*)</span>|Umi',
		"fine_se"=>'|<span class="finese">(.*)FINE_SE(.*)</span>|Umi',
		"valore"=>'|<span class="valore">(.+)</span>|Ui',
		"obbligatorio"=>'|<span class="obbligatori">(.+)</span>|Ui'
	);
	var $fieldList;
	var $model;
	var $status;
	var $errors;
	var $db;
	
	function __construct($text){
		$text=html_entity_decode($text);
		//$text=str_replace(chr(13),"",$text);
		$text=str_replace('/"','"',$text);
		//$text=str_replace('<br>','<br>'.chr(13),$text);
		//$text=str_replace('<br />','<br>'.chr(13),$text);
		//$text=str_replace('</p>','</p>'.chr(13),$text);
		//$text=str_replace('</div>','</div>'.chr(13),$text);
		//$text=str_replace('</span>','</span>'.chr(13),$text);
		$this->model=$text;
		if (!$db){
			//include_once "postgres.php";
			$this->db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
			if(!$this->db->db_connect_id)  die( "Impossibile connettersi al database");
		}
		$sql="SELECT alias_nome_vista||'.'||alias_nome as fields FROM stp.colonne WHERE visibile=1 order by alias_nome_vista,alias_nome";
		if(!$this->db->sql_query($sql)){
			$this->status=-1;
			$this->errors["field"]="Errore nell'acquisizione dei campi unione.";
			return;
		}
		$this->fieldList=$this->db->sql_fetchlist('fields');
	}
	function __destruct(){
	
	}
	function check(){
		foreach($this->tag as $key=>$regexp){
			if($key=="valore" || $key=="obbligatorio"){
				preg_match_all($regexp,$this->model,$out,PREG_SET_ORDER);
				for($i=0;$i<count($out);$i++){
					foreach($this->fieldList as $fld){
						if (strtolower($out[$i][1])==strtolower("O.".$fld)){
							//$this->model=str_replace($out[$i][0],"O.".$fld,$this->model);
						}
						elseif (strtolower($out[$i][1])==strtolower("V.".$fld)){
							//$this->model=str_replace($out[$i][0],"V.".$fld,$this->model);
						}
						elseif (strtolower($out[$i][1])==strtolower("F.".$fld)){
							//$this->model=str_replace($out[$i][0],"F.".$fld,$this->model);
						}
						else{
							$this->status=-1;
							$this->errors["field"][]=$out[$i][1];
						}
					}
				}
			}
			else{
				$out=Array();
				preg_match_all($regexp,$this->model,$out,PREG_SET_ORDER);
				print_debug("$key ---- $regexp");
				print_debug($out);
				for($i=0;$i<count($out);$i++){
					switch($key){
						case "inizio_ciclo":
							$this->model=str_replace($out[$i][0],'<span class="iniziocicli">IN_CICLO</span>',$this->model);
							break;
						case "fine_ciclo":
							$this->model=str_replace($out[$i][0],'<span class="finecicli">FI_CICLO</span>',$this->model);
							break;
						case "inizio_se":
							$this->model=str_replace($out[$i][0],'<span class="iniziose">INIZIO_SE</span>',$this->model);
							break;
						case "fine_se":
							$this->model=str_replace($out[$i][0],'<span class="finese">FINE_SE</span>',$this->model);
							break;
					}
				}
			}
		}
	}
	function save($id=0,$nome,$form,$css_id){
		if(!$id)
			$sql="INSERT INTO stp.e_modelli (nome,form,proprietario,testohtml,css_id) values ('$nome','$form','pubblico','".addslashes($this->model)."','$css_id') ";
		else
			$sql="UPDATE stp.e_modelli SET testohtml=' ".addslashes($this->model)." ',css_id='$css_id' WHERE id=$id;";
print_debug($sql,null,'prova');		
if(!$this->db->sql_query($sql)){
			$this->status=-1;
			$this->errors["save"]="Errore nel salvataggio del modello.";
			return;
		}
	}
}
?>
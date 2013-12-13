<?php
class search{
    var $key='pratica';
    var $type;
    var $offset;
    var $limit;
    var $fields;
    var $fieldList;
    var $mainTable;
    var $mainSchema;
    var $idList;
    var $result;
    var $responseType;
    function __construct($table,$type='AND',$params){
        
        $this->connettidb();
        list($this->mainSchema,$this->mainTable)=explode('.',$table);
        $this->type=$type;
        $this->getFieldList($params['columns']);
        $this->getFilter($params['filter']);
        $this->offset=(isset($params['iDisplayStart']) && ctype_digit($params['iDisplayStart']))?($params['iDisplayStart']):(null);
        $this->limit=(isset($params['iDisplayLength']) && ctype_digit($params['iDisplayLength']) && $params['iDisplayLength']>0)?($params['iDisplayLength']):(-1);
    }
    function __destruct(){
        
    }
    public function response(){
        switch($this->responseType){
            case "HTML":
                break;
            default:
                print json_encode($this->result);
                return;
        }
        
    }
    private function getFieldList($prm){
        for($i=0;$i<count($prm);$i++){
            $tmp=explode('.',$prm[$i]);
            switch(count($tmp)){
                case 1:
                    $this->fields[]=$tmp[0];
                    break;
                case 2:
                    if($this->mainTable==$tmp[0] || !$tmp[0]){
                        $this->fields[]=$tmp[1];
                    }
                    else{
                        $this->fieldList[$this->mainSchema.".".$tmp[0]][]=$tmp[1];
                    }
                    break;
                case 3:
                    $this->fieldList[$tmp[0].".".$tmp[1]][]=$tmp[2];
                    break;
                default:
                    break;
            }
        }
    }
    private function getFilter($arr){
        //print json_encode($arr);
        foreach($arr as $key=>$value){
            $sqlTables[]="$this->key IN (SELECT DISTINCT $this->key FROM $key WHERE ".implode(" $this->type ",$value).")";
        }
        $sql="SELECT DISTINCT $this->key FROM $this->mainSchema.$this->mainTable WHERE ".implode(" $this->type ",$sqlTables);
        $this->sql=$sql;
        $sth=$this->db->prepare($sql);
        $sth->execute();
        $this->idList=$sth->fetchAll(PDO::FETCH_COLUMN);
        $this->total=count($this->idList);
    }
    function getResult(){
        $sql="SELECT $this->key as pkey,".implode(",",$this->fields)." FROM $this->mainSchema.$this->mainTable WHERE $this->key IN ('".implode("','",$this->idList)."');";      
        $sth=$this->db->prepare($sql);
        $sth->execute();
        $ris=$sth->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
        foreach($this->fieldList as $table=>$arrFlds){
            $sql="SELECT $this->key as pkey,".implode(",",$arrFlds)." FROM $table WHERE $this->key IN ('".implode("','",$this->idList)."');";
            $sth=$this->db->prepare($sql);
            $sth->execute();
            $res[$table]=$sth->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
        }
        
        foreach($ris as $key=>$val){
            $r[$key]=$ris[$key][0];
            foreach($res as $k=>$v){
                
                $r[$key][$k]=$v[$key];
            }
        }
		if(!count($r)) $r=Array();
        $this->result=Array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $this->total,
            "iTotalDisplayRecords" => $this->filtered,
            "aaData"=>array_values($r),
			"query"=>$this->sql
        );
    }
    private function sort(){
        if ( isset( $_REQUEST['iSortCol_0'] ) ){
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ ){
                if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" ){
                    $sOrder .= $aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]." ".pg_escape_string( $_REQUEST['sSortDir_'.$i] ) .", ";
                }
            }
             
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
    }
    private function paginate(){
        if ( $this->offset && $this->limit != '-1' ){
            $sLimit = "LIMIT $this->limit OFFSET $this->offset";
        }
    }
    private function connettidb(){
		$this->db = $dbh = new PDO('pgsql:host='.DB_HOST.';port=5432;dbname='.DB_NAME, DB_USER, DB_PWD);		
	}
}
?>
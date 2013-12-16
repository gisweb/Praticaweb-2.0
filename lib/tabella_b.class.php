<?php
require_once APPS_DIR."lib/tabella.class.php";
class Tabella_b extends Tabella{
   
//CREATES TABLE IN VIEW MODE  
    public function viewTable($curr=0){

	$nrows=$this->num_col;
        $table=<<<EOT
        <div class="container">
%s
        </div>
EOT;
	if ($this->viewable){
            for ($i=0;$i<$nrows;$i++){
                $text=<<<EOT
            <div class="row">
                %s
            </div>
EOT;
                $rows[]=sprintf($text,$this->get_riga($i));
            }
	}
	else{
            $text=<<<EOT
            <div class="row">
                <div class="alert alert-warning col-md-12">
                    %s
                </div>
            </div>
EOT;
           $rows[]=sprintf($text,message::getMessage('no-view-right'));
        }
	$table=sprintf($table,implode("",$rows));
    
  
//AGGIUNTA 24/11/2011 BOTTONI   
    
	//$buttons=$this->set_buttons();

	print $table;
        //print $buttons;
    }
    
//RETURN ROW IN VIEW MODE
    function get_riga($nrow){
        $rowCfg=$this->tab_config[$nrow];
        for($i=0;$i<count($rowCfg);$i++){
            $cfg=$rowCfg[$i];
            extract($cfg);
            $span=($span)?($span):(4);
            $dato=(in_array($this->mode,Array("new","edit")))?($this->controllo($cfg)):($this->get_dato($cfg));
            $text=<<<EOT
            <div class="col-md-$span">
                <label for="$field">$label</label>
                $dato
            </div>
EOT;
            $cols[]=$text;
        }
        return implode("",$cols);
    }
    
    function get_dato($cfg){
        $val=$this->array_dati[$this->curr_record][$cfg["field"]];
        $html5Attr=(in_array("html5",array_keys($cfg)))?($this->getHTML5Attr($cfg["html5"])):("");
        switch($cfg["type"]){
            default:
                $value=<<<EOT
<span id="$cfg[field]" class="%s" style="%s" %s>%s</span>                
EOT;
                break;
        }
        $value=sprintf($value,$cfg["class"],$cfg["style"],$html5Attr,$val);
        return $value;
    }
    
    function get_controllo ($cfg){
        $val=$this->array_dati[$this->curr_record][$cfg["field"]];
        $html5Attr=(in_array("html5",array_keys($cfg)))?($this->getHTML5Attr($cfg["html5"])):("");
        switch($cfg["type"]){
            case "select":

                    break;
                default:
                $value=<<<EOT
<input type="text" id="$cfg[field]" name="$cfg[field]" value="$val" class="%s" style="%s" %s/>                
EOT;
                    break;
        }
        $value=sprintf($value,$cfg["class"],$cfg["style"],$html5Attr,$val);
        return $value;
    }   
}
?>
<?php
require_once APPS_DIR."lib/tabella.class.php";
class Tabella_b extends Tabella{
   
//CREATES TABLE IN VIEW MODE  
    public function viewTable($curr=0){
	$nrows=$this->num_col;
        $this->getTitle();
        $editButton=<<<EOT
<button id="btn_edit" type="button" class="btn pull-right">%s<i class="icon-edit"></i></button>
EOT;
        $title=$this->title;
        $editButton=($this->mode=='view')?(sprintf($editButton,message::getMessage("edit"))):("");
        $table=<<<EOT
    <div class="container">
        <div id="title" class="title well">%s %s</div>
        <div class="well">       
%s
        </div>
    </div>
EOT;
	if ($this->viewable){
            for ($i=0;$i<$nrows;$i++){
                $text=<<<EOT
            <div class="row-fluid">
                %s
            </div>
EOT;
                $rows[]=sprintf($text,$this->get_riga($i));
            }
	}
	else{
            $text=<<<EOT
            <div class="row-fluid">
                <div class="alert alert-warning span12">
                    %s
                </div>
            </div>
EOT;
           $rows[]=sprintf($text,message::getMessage('no-view-right'));
        }
	$table=sprintf($table,$title,$editButton,implode("",$rows));
    
  
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
            //$dato=(in_array($this->mode,Array("new","edit"))?($this->getControl($cfg)):($this->get_dato($cfg));
            $dato=$this->getControl($cfg);
            $offset=(isset($cfg["offset"]) && $cfg["offset"])?("offset".$cfg["offset"]):("");
            $text=<<<EOT
            <div class="span$span $offset">
                <label for="$field">$label</label>
                <div>$dato</div>
            </div>
EOT;
            $cols[]=$text;
        }
        return implode("",$cols);
    }
        
    function getControl ($cfg){
        
        extract($cfg);
        $disabled=(in_array($this->mode,Array("new","edit")))?(""):("disabled");

        $val=$this->array_dati[$this->curr_record][$cfg["field"]];
        
        $style=(in_array("style",array_keys($cfg)))?($this->getAttr($cfg["style"])):("");
        $html5Attr=(in_array("html5",array_keys($cfg)))?($this->getHTML5Attr($cfg["html5"])):("");
        $class=array_merge(Array("pw-data"),($class)?($class):(Array()));
        switch($cfg["fieldType"]){
            case "select":
                $opts=$this->getSelectionList($val,$source,$source_key,$source_label,$source_order,$source_filter);
                $value=<<<EOT
<select id="$field" name="$field" class="select pw-data %s" style="%s" %s %s/>
    $opts
</select>
EOT;
                break;
            case "date":
                $class[]="datepicker";
                $valueEdit=<<<EOT
    <div class="input-append date">
        <input type="text" id="$field" name="$field" value="$val" class="%s" style="%s" %s %s><span class="add-on"><i class="icon-th"></i></span>
    </div>                    
EOT;
                $valueView=<<<EOT
        <input type="text" id="$field" name="$field" value="$val" class="%s" style="%s" %s %s>                    
EOT;
                $value=(in_array($this->mode,Array("edit","new")))?($valueEdit):($valueView);
                break;
            case "textarea":
                $value=<<<EOT
<textarea id="$field" name="$field" class="%s" rows="$rows" style="%s" %s %s>
$val
</textarea>        
EOT;
                break;
            case "text":
            default:
                $value=<<<EOT
<input type="text" id="$field" name="$field" value="$val" class="%s" style="%s" %s %s/>                
EOT;
                    break;
        }
        $value=sprintf($value,implode(" ",$class),$style,$html5Attr,$disabled);
        return $value;
    }
}
?>
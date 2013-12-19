<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of field
 *
 * @author mamo
 */
class field {
    
    static function field($type,$params){
        switch($type){
            case "input":
                $element=self::input($params);
                break;
            case "select":
                $options=Array();
                if(in_array("selectList",array_keys($params)) && isArray($params["selectList"])){
                    $options=$params["selectList"];
                    unset($params["selectList"]);
                }
                if(in_array("value",array_keys($params))){
                    $selected=$params["value"];
                    unset($params["value"]);
                }
                
                $element=self::select($params,$options,$selected);
                break;
            case "check":
                $element=self::check($params,$options,$selected);
                break;
            case "radio":
                $element=self::radio($params,$options,$selected);
                break;
            case "textarea":
                if(in_array("value",array_keys($params))){
                    $text=$params["value"];
                    unset($params["value"]);
                }
                $element=self::textarea($params,$text);
                break;
            default:
                break;
        }
        return $element;
    }
    static function input($params){
        $prms=Array();
        foreach($params as $key=>$val){ 
            $prms[]="$key=\"$val\"";
        }
        return sprintf("<input type=\"text\" %s/>",implode(" ",$prms));
    }
    static function select($params,$options,$selected){
        foreach($options as $val=>$label){
            $sel=($val==$selected)?("selected"):("");
            $opts[]=sprintf("<options value=\"%s\" $sel>%s</options>");
        }
        $prms=Array();
        foreach($params as $key=>$val){ 
            $prms[]="$key=\"$val\"";
        }
        $el=<<<EOT
    <select %s>
        %s
    </select>            
EOT;
        return sprintf($el,implode(" ",$prms),implode("\n\t\t",$opts));
    }
    static function textarea($params,$value){
        $prms=Array();
        foreach($params as $key=>$val){ 
            $prms[]="$key=\"$val\"";
        }
        $el=<<<EOT
    <textarea %s>
        %s
    </textarea>            
EOT;
        return sprintf($el,implode(" ",$prms),$value);
    }
    static function radio($params,$options,$selected){
        
    }
    static function check($params,$options,$selected){
        
    }
}
?>
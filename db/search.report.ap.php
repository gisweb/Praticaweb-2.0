<?php
$regexp1="|(.+)_in$|";
$regexp2="|(.+)_fi$|";
foreach($_REQUEST as $key=>$value){
    if (!in_array($key,Array("azione","config_file","id","pratica","chk")) && $value){
        if(preg_match($regexp1, $key))
            $flt[]=str_replace('_in', '', $key).">='$value'";
        elseif(preg_match($regexp2, $key))
            $flt[]=str_replace('_fi', '', $key)."<='$value'";
        else {
            $flt[]="$key::varchar ilike '$value'";
        }
    }
}
$filter=implode(" AND ",$flt);
?>

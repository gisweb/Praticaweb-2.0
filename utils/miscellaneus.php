<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function parse_query($sql,$title=""){
    $parser = new PHPSQLParser($sql, true);
    foreach ($parser->parsed["SELECT"] as $v){
        $key=($v["alias"])?($v["alias"]["name"]):($v["base_expr"]);
	$res[$key]=Array("title"=>$key);
    }
    asort($res);
    return ($title)?Array("title"=>$title,"isFolder"=>"true","key"=>$title,"children"=>array_values($res)):($res);
}

?>
<?php
function validateURL($URL,$mode=0) {
	$prot=($mode==0)?("(((ht|f)tp(s?))\://)+"):("");
    $domain = "((([[:alpha:]][-[:alnum:]]*[[:alnum:]])(\.[[:alpha:]][-[:alnum:]]*[[:alpha:]])+)|(([1-9]{1}[0-9]{0,2}\.[1-9]{1}[0-9]{0,2}\.[1-9]{1}[0-9]{0,2}\.[1-9]{1}[0-9]{0,2})+))";
    $dir = "(/[[:alpha:]][-[:alnum:]]*[[:alnum:]])*";
    $page = "(/[[:alpha:]][-[:alnum:]]*\.[[:alpha:]]{3,5})?";
    $getstring = "(\?([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+)(&([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+))*)?";
    $pattern = "^".$prot.$domain.$dir.$page.$getstring."$";
    return eregi($pattern, $URL);
}
function validateEmail($email){
	$exp = "^[a-z0-9]+[a-z0-9\?\.\+-_]*@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]+$"; 
	 return eregi($exp,$email);
}
?>
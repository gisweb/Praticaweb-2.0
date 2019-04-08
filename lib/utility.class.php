<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utility
 *
 * @author mamo
 */



class utility {



	static function dsprintfMatch($m1,$m2,&$data,&$used_keys) {
            if (isset($data[$m1])) { // if the key is there
                $str = $data[$m1];
                $used_keys[$m1] = $m1; // dont unset it, it can be used multiple times
                return sprintf("%".$m2,$str); // sprintf the string, so %s, or %d works like it should
            } 
            else {
                return "%".$m2; // else, return a regular %s, or %d or whatever is used
            }
        }

    static function dsprintf() {
        $data = func_get_args(); // get all the arguments
        $string = array_shift($data); // the string is the first one
        if (is_array(func_get_arg(1))) { // if the second one is an array, use that
            $data = func_get_arg(1);
        }
        $used_keys = array();
        // get the matches, and feed them to our function
        $string = preg_replace('/\%\((.*?)\)(.)/e','self::dsprintfMatch(\'$1\',\'$2\',\$data,$used_keys)',$string); 
        $data = array_diff_key($data,$used_keys); // diff the data with the used_keys
        return vsprintf($string,$data); // yeah!
    }
}

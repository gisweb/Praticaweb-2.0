<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/***
 * Description of utils
 *
 * @author marco
 */
class utils {
    const jsURL = "/js";
    const jsLocalURL = "/js/local";
    const cssURL="/css";
    const cssLocalURL="/css/local";
    public static $js = Array('jquery-1.10.2','jquery-ui-1.10.2.min','jquery.ui.datepicker-it','jquery.dataTables.min','dataTables.date.order','window','init.config','praticaweb','page.controller','sprintf','jq.ui-extension','message');
    public static $css = Array('praticaweb-1.10.4/jquery-ui.custom.min','styles','tabella_v','menu','jq.ui-extension');
    
    static function mergeParams($prms=Array(),$defaultParams=Array()){
        foreach($defaultParams as $key=>$val){
            $result[$key]=(!array_key_exists($key, $prms) || is_null($prms[$key]))?($val):($prms[$key]);
        }
        
    }
//Funzione che restituisce Array di File in una directory
//Params :  1) srcDir = Directory da scandire
//          2) ext    = Array con le estensioni dei file da cercare
//          3) dir    = Elenco anche delle directory    
    static function listFile($prms=Array()){
        $defaultPrms=Array("srcDir"=>"./","ext"=>Array(),"dir"=>false);
        $result=Array();
        return $result;
    }
    static function uploadFiles($prms=Array()) {
        
    }
    static function resizeImages($prms=Array()) {
        
    }
    static function getDb($params=Array()){
        $dsn = sprintf('pgsql:dbname=%s;host=%s;port=%s',DB_NAME,DB_HOST,DB_PORT);
        $conn = new PDO($dsn, DB_USER, DB_PWD);
        return $conn;
    }
    
    static function url_exists($url) {
        $exists = true;
        $file_headers = @get_headers($url);
        $file_headers[]="$url";
        utils::debug(DEBUG_DIR.'test.debug',$file_headers);
        $InvalidHeaders = array('404', '403', '500');
        foreach($InvalidHeaders as $HeaderVal)
        {
                if(strstr($file_headers[0], $HeaderVal))
                {
                        $exists = false;
                        break;
                }
        }
        return $exists;
    }
    static function loadJS($f=Array(),$default=1){
        $dirName = (dirname($_SERVER['REQUEST_URI'])=="\\")?(""):(dirname($_SERVER['REQUEST_URI']));
        if($default){
            foreach(self::$js as $js){
                $jsLocalURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/') .self::jsLocalURL,$js);
                $jsURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsURL,$js);
                if (self::url_exists($jsLocalURL))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
                elseif(self::url_exists($jsURL))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsURL);
                else
                    $tag="";
                echo $tag;
            }
        }
        if (is_array($f) && count($f)){
            
            foreach($f as $js){
                $jsLocalURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsLocalURL,$js);
                $jsURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsURL,$js);
                if (self::url_exists($jsLocalURL))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
                elseif(self::url_exists($jsURL))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsURL);
                else
                    $tag="";
                echo $tag;
            } 
        }
    }
    static function loadCSS($f=Array(),$default=1){
        $dirName = (dirname($_SERVER['REQUEST_URI'])=="\\")?(""):(dirname($_SERVER['REQUEST_URI']));
        if($default){
            foreach(self::$css as $css){
                $cssLocalURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssLocalURL,$css);
                $cssURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssURL,$css);
                if (self::url_exists($cssLocalURL))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(self::url_exists($jsURL))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssURL);
                else
                    $tag="";
                echo $tag;
            } 
        }
        if (is_array($f) && count($f)){
            foreach($f as $css){
                $cssLocalURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssLocalURL,$css);
                $cssURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssURL,$css);
                if (self::url_exists($cssLocalURL))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(self::url_exists($jsURL))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssURL);
                else
                    $tag="";
                echo $tag;
            }
        }
    }
    
    static function rand_str($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
        // Length of character list
        $chars_length = (strlen($chars) - 1);

        // Start our string
        $string = $chars{rand(0, $chars_length)};

        // Generate random string
        for ($i = 1; $i < $length; $i = strlen($string))
        {
            // Grab a random character from our list
            $r = $chars{rand(0, $chars_length)};

            // Make sure the same two characters don't appear next to each other
            if ($r != $string{$i - 1}) $string .=  $r;
        }

        // Return the string
        return $string;
    }
    static function now(){
        return date('d/m/Y h:i:s', time());
    }
    static function debug($file,$data,$mode='a+'){
        $now=self::now();
        $f=fopen($file,$mode);
        ob_start();
            echo "------- DEBUG DEL $now -------\n";
        print_r($data);
        $result=ob_get_contents();
        ob_end_clean();
        fwrite($f,$result."\n-------------------------\n");
        fclose($f);
    }
    static function postRequest($url,$fields){

        //array_walk_recursive($fields, 'urlencode');
        //url-ify the data for the POST
        $fields_string=http_build_query($fields);
        

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        return $result;
    }
    
    static function arrayGroupBy(array $arr, callable $key_selector) {
        $result = array();
        foreach ($arr as $i) {
          $key = call_user_func($key_selector, $i);
          $result[$key][] = $i;
        }  
        return $result;
    }
    static function getDbDataToJson($sql,$conn){
        if (!$conn){
            $conn=self::getDb();
        }
        $sth=$conn->prepare($sql);
        $sth->execute();
        $result=$sth->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
    static function getUser($userId){
        $conn=self::getDb();
        //DETTAGLI SULL'UTENTE
        $sql="SELECT nome FROM admin.users WHERE userid=?";

        $stmt=$conn->prepare($sql);
        if (!$stmt->execute(Array($userId))){
            return "sconosciuto";
        }
        $utente=$stmt->fetchColumn(0);
        return $utente;
    }
    /**
     * Copy a file, or recursively copy a folder and its contents
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       int      $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    static function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }
}
?>
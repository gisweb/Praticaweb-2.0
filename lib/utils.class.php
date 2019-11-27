<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
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

        $jsPath = APPS_DIR.DIRECTORY_SEPARATOR."js";
        $jsLocalPath = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."js";
        if($default){
            foreach(self::$js as $js){
                $jsLocalURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/') .self::jsLocalURL,$js);
                $jsURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsURL,$js);
                if (file_exists($jsLocalPath.DIRECTORY_SEPARATOR.$js.".js"))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
                elseif (file_exists($jsPath.DIRECTORY_SEPARATOR.$js.".js"))
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
                if (file_exists($jsLocalPath.DIRECTORY_SEPARATOR.$js.".js"))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
                elseif (file_exists($jsPath.DIRECTORY_SEPARATOR.$js.".js"))
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsURL);
                else
                    $tag="";
                echo $tag;
            } 
        }
    }

    static function loadJSTest($f=Array(),$default=1){
        $dirName = (dirname($_SERVER['REQUEST_URI'])=="\\")?(""):(dirname($_SERVER['REQUEST_URI']));

        $jsPath = APPS_DIR.DIRECTORY_SEPARATOR."js";
        $jsLocalPath = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."js";
        if($default){
            foreach(self::$js as $js){
                $jsLocalURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/') .self::jsLocalURL,$js);
                $jsURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsURL,$js);
                if (file_exists($jsLocalPath.DIRECTORY_SEPARATOR.$js.".js")){
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
					echo "<p>File $jsLocalURL trovato</p>";
				}
                elseif (file_exists($jsPath.DIRECTORY_SEPARATOR.$js.".js")){
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsURL);
					echo "<p>File $jsURL trovato</p>";
				}
                else{
					echo "<p>File $js non trovato in ($jsLocalPath , $jsPath)</p>";
                    $tag="";
				}
                echo $tag;
            }
        }
        if (is_array($f) && count($f)){
            
            foreach($f as $js){
                $jsLocalURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsLocalURL,$js);
                $jsURL=sprintf("http://%s%s/%s.js",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::jsURL,$js);
                if (file_exists($jsLocalPath.DIRECTORY_SEPARATOR.$js.".js")){
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsLocalURL);
					echo "<p>File $jsLocalURL trovato</p>";
				}
                elseif (file_exists($jsPath.DIRECTORY_SEPARATOR.$js.".js")){
                    $tag=sprintf("\n\t\t<SCRIPT language=\"javascript\" src=\"%s\"></script>",$jsURL);
					echo "<p>File $jsURL trovato</p>";
				}
                else{
					echo "<p>File $js non trovato in ($jsLocalPath , $jsPath)</p>";
                    $tag="";
				}
                echo $tag;
            } 
        }
    } 
    static function loadCSS($f=Array(),$default=1){
        $dirName = (dirname($_SERVER['REQUEST_URI'])=="\\")?(""):(dirname($_SERVER['REQUEST_URI']));
        $cssPath=APPS_DIR.DIRECTORY_SEPARATOR."css";
        $cssLocalPath= DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."css";
        if($default){
            foreach(self::$css as $css){
                $cssLocalURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssLocalURL,$css);
                $cssURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssURL,$css);
                if (file_exists($cssLocalPath.DIRECTORY_SEPARATOR.$css.".css"))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(file_exists($cssPath.DIRECTORY_SEPARATOR.$css.".css"))
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
                if (file_exists($cssLocalPath.DIRECTORY_SEPARATOR.$css.".css"))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(file_exists($cssPath.DIRECTORY_SEPARATOR.$css.".css"))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssURL);
                else
                    $tag="";
                echo $tag;
            }
        }
    }
    
/*    static function loadCSS($f=Array(),$default=1){
        $dirName = (dirname($_SERVER['REQUEST_URI'])=="\\")?(""):(dirname($_SERVER['REQUEST_URI']));
        if($default){
            foreach(self::$css as $css){
                $cssPath=sprintf("%s/%s.js",self::cssPath,$css);
                $cssLocalPath=sprintf("%s/%s.js",self::cssLocalPath,$css);
                $cssLocalURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssLocalURL,$css);
                $cssURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssURL,$css);
                if (file_exists($cssLocalPath))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(file_exists($cssPath))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssURL);
                else
                    $tag="";
                echo $tag;
            }
        }
        if (is_array($f) && count($f)){
            foreach($f as $css){
                $cssPath=sprintf("%s/%s.js",self::cssPath,$css);
                $cssLocalPath=sprintf("%s/%s.js",self::cssLocalPath,$css);
                $cssLocalURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssLocalURL,$css);
                $cssURL=sprintf("http://%s%s/%s.css",$_SERVER["HTTP_HOST"],rtrim($dirName,'/').self::cssURL,$css);
                if (file_exists($cssLocalPath))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssLocalURL);
                elseif(file_exists($cssPath))
                    $tag=sprintf("\n\t\t<LINK media=\"screen\" href=\"%s\" type=\"text/css\" rel=\"stylesheet\"></link>",$cssURL);
                else
                    $tag="";
                echo $tag;
            }
        }
    }
*/
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
    
    function curlJsonCall($service,$data,$headers){
        $baseHeader = array(
          'Content-Type'=>'multipart/form-data;charset="utf-8"',
          'Accept-Encoding'=>'gzip,deflate',
          'Cache-Control'=>'no-cache',
          'Pragma'=>'no-cache',
          'Content-length'=>strlen($data["data"]),
        );
        //Integro gli header di base con quelli fornito
        if($headers){
            foreach($headers as $k=>$v){
                $baseHeader[$k]=$v;
            }
        }
        //Scrivo gli Headers nella forma corretta
        foreach($baseHeader as $k=>$v) $header[]=sprintf("%s : %s",$k,$v);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $service );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_USERPWD, IOL_USER . ":" . IOL_PWD);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST,           TRUE );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $data);
        if(!$result = curl_exec($ch)) {
            $err = 'Curl error: ' . curl_error($ch);
            curl_close($ch);
            return Array("success"=>0,"result"=>$err);
        } 
        else {
            curl_close($ch);
            return Array("success"=>1,"result"=>$result);
        }
    }
/*    
    function curlJsonCall($service,$data,$headers){
        $baseHeader = array(
          'Content-Type'=>'text/json;charset="utf-8"',
          'Accept-Encoding'=>'gzip,deflate',
          'Cache-Control'=>'no-cache',
          'Pragma'=>'no-cache',
          'Content-length'=>strlen($soap_request),
        );
        //Integro gli header di base con quelli fornito
        if($headers){
            foreach($headers as $k=>$v){
                $baseHeader[$k]=$v;
            }
        }
        //Scrivo gli Headers nella forma corretta
        foreach($baseHeader as $k=>$v) $header[]=sprintf("%s : %s",$k,$v);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $service );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT,        30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST,           true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $soap_request);
        

        if(!$result = curl_exec($ch)) {
            $err = 'Curl error: ' . curl_error($ch);
            curl_close($ch);
            return Array("success"=>0,"result"=>$err);
        } 
        else {
            curl_close($ch);
            return Array("success"=>1,"result"=>$result);
        }
            
    }
 */   
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
    static function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    static function filter_filename($filename, $beautify=true) {
        // sanitize filename
        $filename = preg_replace(
            '~
            [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
            [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
            [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
            [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
            [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
            ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) $filename = self::beautify_filename($filename);
        // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        return $filename;
    }
    static function beautify_filename($filename) {
        // reduce consecutive characters
        $filename = preg_replace(array(
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/'
        ), '-', $filename);
        $filename = preg_replace(array(
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

    static function getStpData($type,$pr){
            $dbh = self::getDb();
            $result=Array("single"=>Array("data_odierna"=>"SELECT CURRENT_DATE as oggi;"),"multiple"=>Array(),"fromfile"=>Array());
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'single_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $stmt = $dbh->prepare($sql);
            if ($stmt->execute())
                $ris=$stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i<count($ris);$i++){
                $view=$ris[$i]["name"];
                $fieldList=$ris[$i]["field_list"];
                $result["single"][$view]=sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;","pe.avvioproc");
            }
            $ris=Array();
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'multiple_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $stmt = $dbh->prepare($sql);
            if ($stmt->execute())
                $ris=$stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i<count($ris);$i++) {
                $view = $ris[$i]["name"];
                $fieldList = $ris[$i]["field_list"];
                $result["multiple"][str_replace('multiple_', '', $view)] = sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;", "pe.avvioproc");
            }
            $ris=Array();
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'fromfile_multiple_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $stmt = $dbh->prepare($sql);
            if ($stmt->execute())
                $ris=$stmt->fetch(PDO::FETCH_ASSOC);
            for($i=0;$i<count($ris);$i++){
                $view=$ris[$i]["name"];
                $fieldList=$ris[$i]["field_list"];
                $result["file_multi"][str_replace('fromfile_multiple_','',$view)]=sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;","pe.avvioproc");
            }
            $data= Array();
            foreach($result["single"] as $sql){
                $stmt = $dbh->prepare($sql);
                if ($stmt->execute(Array($pr)))
                    $ris=$stmt->fetch(PDO::FETCH_ASSOC);
                else{
                    //print_array($stmt->errorInfo());
                }
                $data=(!$ris)?($data):(array_merge($data,$ris));
            }
            foreach($result["multiple"] as $key=>$sql){
                $stmt = $dbh->prepare($sql);
                if ($stmt->execute(Array($pr)))
                    $ris=$stmt->fetchAll(PDO::FETCH_ASSOC);
                $data[$key]=$ris;
            }
            utils::debug(DEBUG_DIR."/DATA_STP.debug",$data);
            return $data;
    }

    static function subst($txt,$data){
        foreach($data as $k=>$v){
            if (!is_array($v)) $txt = str_replace("%($k)s",$v,$txt);
        }
        return $txt;
    }
    static function debugAdmin($data){
        if($_SESSION["USER_ID"]==1){
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }
    }
    static function printMessage($message){
        ob_start();
        utils::loadJS();
        utils::loadCss();
        $jscss = ob_get_contents();
        ob_end_clean();
        
        $html =<<<EOT
<HTML>
	<HEAD>
		<TITLE>Messaggio di Errore</TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        $jscss
    </HEAD>
    <BODY>
        $message
    </BODY>
</HTML>
EOT;
        header('Content-Type: text/html; charset=utf-8');
        print $html;
        return;
    }
    
    static function validation($type,$val){
        $result = 0;
        switch($type){
            case "valuta":
                if (preg_match('/^\d+([\.,]\d{1,2})?$/', $val)) $result = 1;
                else 
                    $result =  0;
                break;
            case "intero":
                if (preg_match('/^\d+?$/', $val)) $result = 1;
                else 
                    $result =  0;
                break;
            case "email":
                if(filter_var($val, FILTER_VALIDATE_EMAIL)) $result = 1;
                break;
            case "url":
                if(filter_var($val, FILTER_VALIDATE_URL)) $result = 1;
                break;
            case "ip":
                if(filter_var($val, FILTER_VALIDATE_IP)) $result = 1;
            default:
                $result = 1;   
        }
        return $result;
    }
    
    static function is_zero($val){
        $res = preg_replace('|[\.,0]|',"",$val);
        if (strlen($res)==0) return 1;
        else
            return 0;
            
    }
    
    static function json_error($err){
        switch ($err) {
            case JSON_ERROR_NONE:
                return Array("success"=>1,"error"=>"");
            break;
            case JSON_ERROR_DEPTH:
                return Array("success"=>0,"error"=>"Maximum stack depth exceeded");
               
            break;
            case JSON_ERROR_STATE_MISMATCH:
                return Array("success"=>0,"error"=>"Underflow or the modes mismatch");
            break;
            case JSON_ERROR_CTRL_CHAR:
                return Array("success"=>0,"error"=>"Unexpected control character found");
            break;
            case JSON_ERROR_SYNTAX:
                return Array("success"=>0,"error"=>"Syntax error, malformed JSON");
            break;
            case JSON_ERROR_UTF8:
                return Array("success"=>0,"error"=>"Malformed UTF-8 characters, possibly incorrectly encoded");
            break;
            default:
                return Array("success"=>0,"error"=>"Unknown error");
            break;
        }
    }
}

?>

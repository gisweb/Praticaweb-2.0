<?php
$basicMessages = Array();
$basicMessages["ISTRUZIONI_PAGOPA_VIEW"]=<<<EOT
<div style="color:red;font-weight:bold;font-size:13px;">E' possibile creare richieste di pagamento e stampare i relativi ordini di incasso per tutte le pratiche, ma è possibile pubblicare le richieste sul portale delle istanze online solo per quelle presentate online.<br> Per le pratiche cartacee dovranno essere fatte delle integrazioni con pagamento tramite lo stesso portale</div>
EOT;
$basicMessages["AVVERTIMENTO_PROTOCOLLO_USCITA"]=<<<EOT

EOT;
class generalMessages{
    
    static $messages;
    static $localMessages = Array(
        
    );
    static function subst($txt,$data=Array()){
        foreach($data as $k=>$v){
            if (!is_array($v)) $txt = str_replace("%($k)s",$v,$txt);
        }
        return $txt;
    }
    
    static function initMessages($mex=Array()){
        self::$messages = $basicMessages;
        $messages = self::$messages;
        foreach($mex as $k=>$v){
            $messages[$k] = $v;
        }
        return $messages;
    }
    static function getMessage($code,$local=Array(),$data=Array()){
        $messages = self::initMessages($local);
        
        if (!array_key_exists($code,$messages)) return "Codice '$code' non trovato";
        
        $message = self::subst($messages[$code],$data);
        return $message;
    }
}
?>
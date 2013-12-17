<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message
 *
 * @author mamo
 */
class message {
    public static $message = Array(
        "cancel"=>"Annulla",
        "close"=>"Chiudi",
        "default"=>"Default Message",
        "delete"=>"Elimina",
        "edit"=>"Modifica",
        "file-not-found"=>"Il file %s non è stato trovato.",
        "no-message"=>"Messaggio non trovato",
        "no-view-right"=>"Non si dispone dei diritti per visualizzare i dati",
        "save"=>"Salva",
        "select"=>"Seleziona",
        "select-all"=>"Seleziona Tutti"
    );
    static function getMessage($key="default",$v=''){
        $m=self::$message;
        if(!in_array($key,array_keys($m))){
            return $m["no-message"];
        }
        else{
            $mex=sprintf($m[$key],$v);
            return $mex;
        }
    }
}

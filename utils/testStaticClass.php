<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../login.php';

class pippo{
    public function __construct($numero) {
        $this->numero=$numero;
        $this->info=appUtils::getPraticaRole($numero);
    }
}

$n=15551;
$a = new pippo($n);
print_array($a);
?>

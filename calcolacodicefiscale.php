
<?
//====================================================================================================//
//                                  - CALCOLO DEL CODICE FISCALE -                                    //
//                                                                                                    //
// Copyright (C) 2001 - Frizzoni Riccardo                                                             //
//                                                                                                    //
// Questo programma Ãš software libero; Ãš lecito redistribuirlo e/o modificarlo secondo i termini      //
// della versione 2 della Licenza Pubblica Generica GNU come Ãš pubblicata dalla Free Software         //
// Foundation. Questo programma Ãš distribuito nella speranza che sia  utile, ma SENZA ALCUNA GARANZIA;//
// senza neppure la garanzia implicita di NEGOZIABILITÃ o di APPLICABILITÃ PER UN PARTICOLARE SCOPO.  //
// Si veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.                              //
// Questo programma deve essere distribuito assieme ad una copia della Licenza Pubblica Generica GNU; //
// in caso contrario, se ne puÃ² ottenere una scrivendo alla Free Software Foundation, Inc., 59 Temple //
// Place, Suite 330, Boston, MA 02111-1307 USA.                                                       //
//                                               ---                                                  //
// This program is free software; you can redistribute it and/or modify it under the terms of the GNU //
// General Public License as published by the Free Software Foundation; either version 2 of the       //
// License. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; //
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the  //
// GNU General Public License for more details. You should  have received a copy of the GNU General   //
// Public License along with this program; if not, write to the Free Software Foundation, Inc., 59    //
// Temple Place, Suite 330, Boston, MA  02111-1307  USA                                               //
//                                               ---                                                  //
//                                                                                                    //
// Questi commenti e questa licenza NON DEVONO ESSERE RIMOSSI !!!!!!!!!!!!!!!!!!!!!                   //
//                                                                                                    //
// Il riferimento all'autore nella pagina in cui verrÃ  sfruttato lo script non Ãš obbligatorio ma      //
// sarebbe MOLTO apprezzato dallo stesso.                                                             //
//                                                                                                    //
// Per segnalare errori, o per avere maggiori informazioni potete contattare l'autore al seguente     //
// indirizzo e-mail: rifrizzo@tiscalinet.it                                                           //
// oppure visitate il sito in cui questo script Ãš implementato:                                       //
//                                                                                                    //
//                                     www.rifrizzo.any.za.net/cf                                     //
//                                                                                                    //
// Per spiegazioni sul funzionamento e sull'implementazione dello script riferirsi rispettivamente ai //
// commenti della function calcolacodicefiscale e alle note a fondo dello stesso script.              //
//                                                                                                    //
//                                                                           Riccardo Frizzoni        //
//                                                                                                    //
//====================================================================================================//


class risultato {
//----------------------------------------------------------------------------------------------------//
//                                                                                                    //
// classe usata per restituire il valore del calcolo della funzione calcolacodicefiscale nella        //
// proprietÃ  $codicefiscale insieme ad eventuali errori nell'array errori[]                           //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//

      var $codicefiscale;        //risultato del calcolo
      var $errori=array();       //array contenente eventuali errori nel calcolo
}


function calcolacodicefiscale($ccognome,$cnome,$csesso,$ccomune,$cdatasingola="",$cgiorno="",$cmese="",$canno=""){
//----------------------------------------------------------------------------------------------------//
//                                                                                                    //
//                                     calcolacodicefiscale                                           //
//                                                                                                    //
// funzione principale da richiamare, restituisce un oggetto con le seguenti proprietÃ :               //
//                  $r->codicefiscale:       risultato del calcolo                                    //
//                  $r->errori:              array contenente eventuali spiegazione di errori occorsi //
//                                                                                                    //
// - la data puÃ² essere inviata sia in $cdatasingola in formato "gg/mm/aaaa" sia separatamente in     //
//   $cgiorno,$cmese,$canno                                                                           //
// - se $cdatasingola contiene qualcosa allora viene presa come data altrimenti vengono considerati   //
//   $cgiorno $cmese $canno                                                                           //
// - l'anno puÃ² essere espresso in qualsiasi formato da 1 a 4 caratteri (es. 1,01,2001,987), il mese  //
//   ed il giorno possono essere sia a 2 che a 1 cifra (es. 5/6/1980 = 05/06/1980 = 5/06/1980)        //
// - se $csesso Ãš F (come f) si considera femmina altrimenti per qualsiasi altro carattere maschio    //
//                                                                                                    //
// si puÃ² richiamare la funzione in questi modi                                                       //
//     calcolacodicefiscale("frizzoni","riccardo","m","pistoia","23/9/72");                           //
//     calcolacodicefiscale("frizzoni","riccardo","m","pistoia","","23","9","72");                    //
//----------------------------------------------------------------------------------------------------//

         //settaggi vari
         $MESI = "ABCDEHLMPRST"; //lettere x corrispondenza mesi
         $crisultato=new risultato; //conterrÃ  risultato-errori

         //matrice per calcolare il carattere di controllo
         //il primo indice Ãš il codice ascii del carattere il secondo indice la posizione pari o dispari
         // "A"
         $MATRICECOD[65][0] = 1;
         $MATRICECOD[65][1] = 0;
         // "B"
         $MATRICECOD[66][0] = 0;
         $MATRICECOD[66][1] = 1;
         // "C"
         $MATRICECOD[67][0] = 5;
         $MATRICECOD[67][1] = 2;
         // "D"
         $MATRICECOD[68][0] = 7;
         $MATRICECOD[68][1] = 3;
         // "E"
         $MATRICECOD[69][0] = 9;
         $MATRICECOD[69][1] = 4;
         // "F"
         $MATRICECOD[70][0] = 13;
         $MATRICECOD[70][1] = 5;
         // "G"
         $MATRICECOD[71][0] = 15;
         $MATRICECOD[71][1] = 6;
         // "H"
         $MATRICECOD[72][0] = 17;
         $MATRICECOD[72][1] = 7;
         // "I"
         $MATRICECOD[73][0] = 19;
         $MATRICECOD[73][1] = 8;
         // "J"
         $MATRICECOD[74][0] = 21;
         $MATRICECOD[74][1] = 9;
         // "K"
         $MATRICECOD[75][0] = 2;
         $MATRICECOD[75][1] = 10;
         // "L"
         $MATRICECOD[76][0] = 4;
         $MATRICECOD[76][1] = 11;
         // "M"
         $MATRICECOD[77][0] = 18;
         $MATRICECOD[77][1] = 12;
         // "N"
         $MATRICECOD[78][0] = 20;
         $MATRICECOD[78][1] = 13;
         // "O"
         $MATRICECOD[79][0] = 11;
         $MATRICECOD[79][1] = 14;
         // "P"
         $MATRICECOD[80][0] = 3;
         $MATRICECOD[80][1] = 15;
         // "Q"
         $MATRICECOD[81][0] = 6;
         $MATRICECOD[81][1] = 16;
         // "R"
         $MATRICECOD[82][0] = 8;
         $MATRICECOD[82][1] = 17;
         // "S"
         $MATRICECOD[83][0] = 12;
         $MATRICECOD[83][1] = 18;
         // "T"
         $MATRICECOD[84][0] = 14;
         $MATRICECOD[84][1] = 19;
         // "U"
         $MATRICECOD[85][0] = 16;
         $MATRICECOD[85][1] = 20;
         // "V"
         $MATRICECOD[86][0] = 10;
         $MATRICECOD[86][1] = 21;
         // "W"
         $MATRICECOD[87][0] = 22;
         $MATRICECOD[87][1] = 22;
         // "X"
         $MATRICECOD[88][0] = 25;
         $MATRICECOD[88][1] = 23;
         // "Y"
         $MATRICECOD[89][0] = 24;
         $MATRICECOD[89][1] = 24;
         // "Z"
         $MATRICECOD[90][0] = 23;
         $MATRICECOD[90][1] = 25;
         // "0"
         $MATRICECOD[48][0] = 1;
         $MATRICECOD[48][1] = 0;
         // "1"
         $MATRICECOD[49][0] = 0;
         $MATRICECOD[49][1] = 1;
         // "2"
         $MATRICECOD[50][0] = 5;
         $MATRICECOD[50][1] = 2;
         // "3"
         $MATRICECOD[51][0] = 7;
         $MATRICECOD[51][1] = 3;
         // "4"
         $MATRICECOD[52][0] = 9;
         $MATRICECOD[52][1] = 4;
         // "5"
         $MATRICECOD[53][0] = 13;
         $MATRICECOD[53][1] = 5;
         // "6"
         $MATRICECOD[54][0] = 15;
         $MATRICECOD[54][1] = 6;
         // "7"
         $MATRICECOD[55][0] = 17;
         $MATRICECOD[55][1] = 7;
         // "8"
         $MATRICECOD[56][0] = 19;
         $MATRICECOD[56][1] = 8;
         // "9"
         $MATRICECOD[57][0] = 21;
         $MATRICECOD[57][1] = 9;

         //converte tutto in maiuscolo
         $ccomune=strtoupper(stripslashes($ccomune));
         $ccognome=strtoupper(stripslashes($ccognome));
         $cnome=strtoupper(stripslashes($cnome));


         //controlla in quale formato Ãš stata passata la data
         if ($cdatasingola==""){
             //prende buoni $cgiorno $cmese $canno
             if (strlen($canno)<2) { $canno="0".$canno; }//se l'anno Ãš 1 -> anno = 01
             //controlla validitÃ  data
             if (!calcolacodicefiscale_controllodata($cgiorno,$cmese,$canno)){
                  //setta l'errore
                  $crisultato->errori[]="Data $cgiorno/$cmese/$canno non corretta";
             }
         } else {
             //prende buono $cdatasingola

             //spezza la data
             //$pezzi=explode("/",$cdatasingola);
			$pezzi=preg_split("/[-.\/]/",$cdatasingola);
             //ci sono 3 pezzi ?
             if (sizeof($pezzi)!=3){
                  //se non ho 3 pezzi
                  //setta l'errore
                  $crisultato->errori[]="Data $cdatasingola non corretta";
             } else {
                  //se ha 3 pezzi
                  //setta giorno mese anno
                  $cgiorno=$pezzi[0];
                  $cmese=$pezzi[1];
                  $canno=$pezzi[2];
                  if (strlen($canno)<2) { $canno="0".$canno; }//se l'anno Ãš 1 -> anno = 01

                  //controlla validitÃ  data
                  if (!calcolacodicefiscale_controllodata($cgiorno,$cmese,$canno)){
                       //setta l'errore
                       $crisultato->errori[]="Data $cdatasingola non corretta";
                  }
             }
         }

         //calcola comune
         $codcomune=calcolacodicefiscale_calcolacomune($ccomune);

         //se non trova il comune
         if ($codcomune=="0"){
             //setta l'errore
             $crisultato->errori[]="Non ho trovato il comune '$ccomune'";
         }

         //se non ha errori calcola il codice
         if (!sizeof($crisultato->errori)) {

                  //setta $csesso 40 o 0  a seconda che sia F o qualcos'altro (M)
                  if (strtoupper($csesso)=="F"){
                      $csesso=40;
                  } else {
                      $csesso=0;
                  }

                  $codice=calcolacodicefiscale_calcolacognome(trim($ccognome));   //inserisce cognome

                  $codice.=calcolacodicefiscale_calcolanome(trim($cnome));        //inserisce nome

                  $codice.=substr($canno,-2);                                     //inserisce anno

                  $codice.=substr($MESI,$cmese-1,1);                              //inserisce mese

                  $tmp=strval(intval($csesso)+$cgiorno);                          //inserisce giorno+sesso
                  //aggiunge lo 0 iniziale se $tmp Ãš da 1 a 9                     //
                  if (strlen($tmp)<2){                                            //
                      $tmp="0".$tmp;                                              //
                  }                                                               //
                  $codice.=$tmp;                                                  //

                  $codice.=$codcomune;                                            //inserisce comune

                  //calcola carattere di controllo
                  $codcontrollo = 0;
                  //scorre $codice fino ad ora calcolato
                  For ($i= 0;$i<=14;$i++){
                        //aggiunge a $codcontrollo il valore relativo al carattere trovato in base alla posizione pari o dispari
                        $codcontrollo = $codcontrollo + $MATRICECOD[ord(substr($codice, $i, 1))][ $i % 2];
                  }
                  $codice .= chr(65 + ($codcontrollo % 26) );                     //inserisce carattere di controllo

                  //scrive il codice trovato in $crisultato
                  $crisultato->codicefiscale=$codice;
         }

         return $crisultato;

}


function calcolacodicefiscale_noaccentate($s){
//----------------------------------------------------------------------------------------------------//
//                               calcolacodicefiscale_noaccentate                                     //
// restituisce la stringa $s trasformando le lettere accentate in non accentate                       //
// ad esempio "andÃ²" viene trasformata "ando"                                                         //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//

         $ACCENTATE=" ÃÃÃÃÃÃÃ ÃšÃ©Ã¬Ã²Ã¹";
         $NOACCENTO=" AEEIOUAEEIOU";
         $i=0;
         //scorre la stringa originale
         while ($i<strlen($s)){
                 $p=strpos($ACCENTATE,substr($s,$i,1));
                 //se ha trovato una lettera accentata
                 if ($p){
                     //sostituisce con la relativa non accentata
                     $s[$i]=substr($NOACCENTO,$p,1);
                 }
                 $i++;
         }

         return $s;
}


function calcolacodicefiscale_calcolacognome($cogn){
//----------------------------------------------------------------------------------------------------//
//                               calcolacodicefiscale_calcolacognome                                  //
// restituisce le 3 lettere relative al cognome passato in $cogn                                      //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//

         $VOCALI = " AEIOU";
         $CONSONANTI = " BCDFGHJKLMNPQRSTVWXYZ";

         //elimina le accentate
         $cogn=calcolacodicefiscale_noaccentate($cogn);
         $i = 0;
         $stringa = "";//stringa conterÃ  le 3 lettere
         //scorre il cognome in cerca di 3 consonanti
         while ((strlen($stringa) < 3) and ($i < strlen($cogn))){
                 //se trova una consonante la aggiunge a $stringa
                 if (  strrpos($CONSONANTI, substr($cogn, $i, 1))   ){
                       $stringa = $stringa.substr($cogn, $i, 1);
                 }
                 $i++;
         }
         $i = 0;
         //se non ha ancora 3 consonanti sceglie fra le vocali
         while ((strlen($stringa) < 3) and ($i < strlen($cogn))){
                 //se trova una vocale la aggiunge a $stringa
                 if (  strrpos($VOCALI, substr($cogn, $i, 1))       ){
                       $stringa = $stringa.substr($cogn, $i, 1);
                 }
                 $i++;
         }
         //se non ha ancora 3 lettere aggiunge x per arrivare a 3
         if (strlen($stringa) < 3) {
             for ($i=strlen($stringa);$i<=2;$i++){
                   $stringa = $stringa."X";
             }
         }

         return $stringa;
}


function calcolacodicefiscale_calcolanome($nom){
//----------------------------------------------------------------------------------------------------//
//                               calcolacodicefiscale_calcolagnome                                    //
// restituisce le 3 lettere relative al nome passato in $nom                                          //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//

         $VOCALI = " AEIOU";
         $CONSONANTI = " BCDFGHJKLMNPQRSTVWXYZ";
         //elimina le accentate
         $nom=calcolacodicefiscale_noaccentate($nom);
         $i = 0;
         $cons = "";
         $stringa = "";//stringa conterÃ  le 3 lettere
         //scorre il nome in cerca di 4 consonanti
         while ((strlen($cons) < 4) and ($i < strlen($nom))){
                 if (  strrpos($CONSONANTI, substr($nom, $i, 1))     ){
                       //se trova una consonante la aggiunge a $cons
                       $cons = $cons.substr($nom, $i, 1);
                 }
                 $i++;
         }
         if (strlen($cons)>3){
             //se ha 4 consonanti prende la 1Â°, 3Â°, 4Â°
             $stringa=substr($cons,0,1).substr($cons,2,2);
         } else {
             //altrimenti mette quelle che ha trovato in $stringa
             $stringa=$cons;
         }
         $i = 0;
         //scorre il nome in cerca di vocali finchÃš $stringa non contiene 3 lettere
         while ((strlen($stringa) < 3) and ($i < strlen($nom))){
                 if (  strrpos($VOCALI, substr($nom, $i, 1))         ){
                       //se trova una consonante la aggiunge a $stringa
                       $stringa = $stringa.substr($nom, $i, 1);
                 }
                 $i++;
         }
         //se non ha ancora 3 lettere aggiunge x per arrivare a 3
         if (strlen($stringa) < 3) {
             for ($i=strlen($stringa);$i<=2;$i++){
                   $stringa = $stringa."X";
             }
         }

         return $stringa;

}


function calcolacodicefiscale_controllodata($g,$m,$a){
//----------------------------------------------------------------------------------------------------//
//                               calcolacodicefiscale_controllodata                                   //
// Controlla la validitÃ  di una data, restituendo true se la data Ãš valida                            //
// $g=giorno $m=mese $a=anno                                                                          //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//

         $controllo=false;
         $controllo=checkdate(intval($m),intval($g),intval($a));
         return $controllo;
}


function calcolacodicefiscale_calcolacomune($com){
//----------------------------------------------------------------------------------------------------//
//                               calcolacodicefiscale_calcolacomune                                   //
// Restituisce il codice riferito al comune di nascita $com cercandolo nei files  comuni1(2,3,4).csv  //
// La lista dei comuni Ãš stata suddivisa in 4 files per rendere piÃ¹ veloce la ricerca.                //
// Se non viene trovato il comune il valore di ritorno Ãš "0"                                          //
//                                                                                                    //
//----------------------------------------------------------------------------------------------------//


    $finali="12344";  // stringa che contiene le lettere finali del file a seconda dell'iniziale
    //$PERCORSO="./dati/comuni/"; //path dei files comuni1-4.csv
    $iniziale=substr($com,0,1);
    // se l'iniziale non Ãš una lettera ritorna il codice di errore
    if ((ord($iniziale)<65)||(ord($iniziale)>90)) {
         return ("0");
    }
    //sceglie il nome del file in base all'iniziale
   /* $filecomuni=$PERCORSO."comuni".substr($finali,floor((ord($iniziale)-64)/6),1).".csv";
    //apre in lettura il file in $filecomini che contiene l'elenco dei comuni e dei rispettivi codici
    $fp = fopen($filecomuni,"r");
    $cod="0";
    // legge il file finchÃš non trova il comune (inserisce il codice in $cod)
    while (  ($stringa = fgets($fp,4096)) and ($cod=="0")   ){
               //riporta nell'array $campi il nome del comune letto($campi[0]) e il codice ($campi[1])
               $campi=explode(";",$stringa);
               if ($campi[0]==$com) {
                    //se ha trovato il comune mette in $cod il codice
                    $cod=substr($campi[1],0,4);
               }
    }
    fclose($fp);*/
	
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$sql="SELECT codice FROM pe.e_comuni WHERE nome ilike '$com'";
	if ($db->sql_query($sql)){
		$ris=$db->sql_fetchrowset();
		if (count($ris)==1)	$cod=$ris[0]['codice'];
		else
			return 0;
	}
    return $cod;
}

//====================================================================================================//
//                                                                                                    //
//                                                                                                    //
/*                                                                                                    //
// - per utilizzare lo script Ãš sufficiente inserire all'inizio della pagina in cui si effettua la    //
//   visualizzazione del risultato la seguente riga:                                                  //

require("...percorso.../calcolacodicefiscale.inc.php");

// - per visualizzare il risultato usare un codice del tipo:                                          //

$r=new risultato;

$r=calcolacodicefiscale($cognome,$nome,$sesso,$comune,"",$giorno,$mese,$anno);
 oppure
$r=calcolacodicefiscale($cognome,$nome,$sesso,$comune,$datanascita);

if (sizeof($r->errori)){
    echo "Si sono verificati i seguenti errori:<br>";
    reset ($r->errori);
    while (list ($key, $val) = each ($r->errori)) {
       echo ($key+1)."- ".$val."<br>";
    }
} else {
    echo $r->codicefiscale;
}

*/                                                                                                    //
//                                                                                                    //
//                                                                                                    //
//====================================================================================================//
?>
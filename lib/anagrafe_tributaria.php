<?php
/**
 * Created by PhpStorm.
 * User: mamo
 * Date: 26/08/17
 * Time: 11:26
 */
$recordDefs = Array(
    //TRACCIATO RECORD DI TESTA -- SOGGETTO OBBLIGATO
    "0"=>Array(
        //DATI IDENTIFICATIVI DELLA FORNITURA
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"0\"","obbl"=>1),//1
        Array("da"=>1,"a"=>5,"len"=>5,"desc"=>"Codice identificativo della fornitura","tipo"=>"str","note"=>"Vale sempre \"DIAXX\"","obbl"=>1),//2
        Array("da"=>6,"a"=>7,"len"=>2,"desc"=>"Codice numerico della fornitura","tipo"=>"int","note"=>"Vale sempre \"29\"","obbl"=>1),//3
        //IDENTIFICATIVO FISCALE DEL SOGGETTO OBBLIGATO
        Array("da"=>8,"a"=>23,"len"=>13,"desc"=>"Codice fiscale","tipo"=>"int","note"=>"Se è numerico deve essere allineato a sinistra.Controllo di presenza e di correttezza formale del codice fiscale; controllo di allineamento a sinistra in caso di codice fiscale numerico; controllo della \"congruenza\" del codice fiscale rispetto ai dati anagrafici impostati;","obbl"=>1),//4
        //DATI IDENTIFICATIVI DEL SOGGETTO OBBLIGATO (da impostare solo nel caso di persona fisica)
        Array("da"=>24,"a"=>49,"len"=>26,"desc"=>"Cognome","tipo"=>"str","note"=>"Cognome del soggetto obbligato","obbl"=>0),//5
        Array("da"=>50,"a"=>74,"len"=>25,"desc"=>"Nome","tipo"=>"str","note"=>"Nome del soggetto obbligato","obbl"=>0),//6
        Array("da"=>75,"a"=>75,"len"=>1,"desc"=>"Sesso","tipo"=>"str","note"=>"Valori ammessi:M = Maschio, F = Femmina","obbl"=>0),//7
        Array("da"=>76,"a"=>83,"len"=>8,"desc"=>"Data di nascita","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\"","obbl"=>0),//8
        Array("da"=>84,"a"=>87,"len"=>4,"desc"=>"Codice catastale del Comune di nascita","tipo"=>"int","note"=>"Codice catastale del comune di nascita del soggetto obbligato","obbl"=>0),//9
        //DATI IDENTIFICATIVI DEL SOGGETTO OBBLIGATO (da impostare solo nel caso di persona giuridica)
        Array("da"=>88,"a"=>147,"len"=>60,"desc"=>"Denominazione","tipo"=>"int","note"=>"Denominazione del soggetto obbligato"),//10
        Array("da"=>148,"a"=>151,"len"=>4,"desc"=>"Codice catastale della sede legale","tipo"=>"int","note"=>"Codice catastale del comune della sede legale del soggetto obbligato.","obbl"=>0),//11
        //ESTREMI DELLA FORNITURA
        Array("da"=>152,"a"=>155,"len"=>4,"desc"=>"Anno di riferimento","tipo"=>"str","note"=>"Da indicare nel formato \"AAAA\"","obbl"=>0),//12
        //CARATTERI DI CONTROLLO
        Array("da"=>156,"a"=>366,"len"=>211,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),//13
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),//14
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)//15
    ),
    //TRACCIATO RECORD DI DETTAGLIO - IDENTIFICAZIONE DELLA RICHIESTA
    "1"=>Array(
        //IDENTIFICATIVO RECORD
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"1\"","obbl"=>1),//1
        //CODICE FISCALE DEL RICHIEDENTE
        Array("da"=>1,"a"=>16,"len"=>16,"desc"=>"Codice Fiscale","tipo"=>"str","note"=>"","obbl"=>1),//2
        //DATI IDENTIFICATIVI DEL RICHIEDENTE (PERSONA FISICA)
        Array("da"=>17,"a"=>42,"len"=>26,"desc"=>"Cognome","tipo"=>"int","note"=>"","obbl"=>0),//3
        Array("da"=>43,"a"=>67,"len"=>25,"desc"=>"Nome","tipo"=>"int","note"=>"","obbl"=>0),//4
        Array("da"=>68,"a"=>68,"len"=>1,"desc"=>"Sesso","tipo"=>"str","note"=>"Valori ammessi:M = Maschio, F = Femmina","obbl"=>0),//5
        Array("da"=>69,"a"=>76,"len"=>8,"desc"=>"Data di nascita","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\"","obbl"=>0),//6
        Array("da"=>77,"a"=>80,"len"=>4,"desc"=>"Codice catastale del Comune di nascita","tipo"=>"int","note"=>"Codice catastale del comune di nascita del soggetto obbligato","obbl"=>0),//7
        //DATI IDENTIFICATIVI DEL RICHIEDENTE (PERSONA NON FISICA)
        Array("da"=>81,"a"=>140,"len"=>60,"desc"=>"Denominazione","tipo"=>"str","note"=>"Denominazione del soggetto obbligato"),//8
        Array("da"=>141,"a"=>144,"len"=>4,"desc"=>"Codice catastale della sede legale","tipo"=>"str","note"=>"Codice catastale del comune della sede legale del soggetto obbligato.","obbl"=>0),//9
        //QUALIFICA DEL RICHIEDENTE
        Array("da"=>145,"a"=>145,"len"=>1,"desc"=>"Qualifica del richiedente","tipo"=>"int","note"=>"Valori Ammessi : 1 = Proprietario;, 2 = Usufruttuario, 3 = Titolare di altro diritto sull'immobile, 4 = Rappresentante legale o volontario di uno degli aventi titolo sopra indicati","obbl"=>1),//10
        //TIPO DELLA RICHIESTA
        Array("da"=>146,"a"=>146,"len"=>1,"desc"=>"Tipo richiesta","tipo"=>"int","note"=>"Valori Ammessi : 0 = Permesso di costruire, certificato di agibilità o altro atto di assenso, 1 = Denuncia di inizio attività (DIA)","obbl"=>1),//11
        //DATI RELATIVI ALLA RICHIESTA
        Array("da"=>147,"a"=>147,"len"=>1,"desc"=>"Tipologia di intervento","tipo"=>"int","note"=>"Valori Ammessi : 1 = Interventi di manutenzione ordinaria (art. 3, comma 1, lett. a) DPR 380/2001, 2 = Interventi di manutenzione straordinaria (art. 3, comma 1, lett. b) DPR 380/2001, 3 = Interventi di restauro e di risanamento conservativo (art. 3, comma 1, lett. c) DPR 380/2001, 4 = Interventi di ristrutturazione edilizia (art. 3, comma 1, lett. d) DPR 380/2001, 5 = Interventi di nuova costruzione (art. 3, comma 1, lett. e) DPR 380/2001, 6 = Interventi di ristrutturazione urbanistica (art. 3, comma 1, lett. f) DPR 380/2001, 7 = Altro","obbl"=>1),//12
        Array("da"=>148,"a"=>167,"len"=>20,"desc"=>"Numero di Protocollo","tipo"=>"str","note"=>"Campo obbligatorio. Rappresenta il numero di protocollo assegnato alla richiesta. Tale valore deve essere riportato in tutti i record che identificano i beneficiari (tipo record 2), i dati catastali (tipo record 3), i professionisti (tipo record 4, se presenti) e le ditte esecutrici (tipo record 5, se presenti).","obbl"=>1),//13
        Array("da"=>168,"a"=>168,"len"=>1,"desc"=>"Tipologia della richiesta","tipo"=>"int","note"=>"Valori Ammessi : 0 = Rilascio, 1 = Cessazione. Si considerano atti di cessazione: revoca, abrogazione, ritiro, annullamento, pronuncia di decadenza, diniego di rinnovo o di proroga, rinuncia ed estinzione.","obbl"=>1),//14
        Array("da"=>177,"a"=>184,"len"=>8,"desc"=>"Data di presentazione della richiesta","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\".","obbl"=>1),//15
        Array("da"=>177,"a"=>184,"len"=>8,"desc"=>"Data di Inizio Lavori","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\". Se non conosciuta, indicare la data di rilascio.Campo obbligatorio solo nel caso di Permesso di costruire, certificato di agibilità o altro atto di assenso (campo \"Tipo richiesta \" = 0)","obbl"=>0),//16
        Array("da"=>185,"a"=>192,"len"=>8,"desc"=>"Data di Fine Lavori","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\"","obbl"=>1),//17
        Array("da"=>193,"a"=>227,"len"=>35,"desc"=>"Indirizzo","tipo"=>"str","note"=>"Via e Numero civico","obbl"=>1),//18
        //CARATTERI DI CONTROLLO
        Array("da"=>228,"a"=>366,"len"=>139,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),//19
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),//20
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)//121
    ),
    //TRACCIATO RECORD DI DETTAGLIO - IDENTIFICAZIONE DEI BENEFICIARI
    "2"=>Array(
        //IDENTIFICATIVO RECORD
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"2\"","obbl"=>1),//Tipo Record
        //DATI DI CONCATENZAZIONE
        Array("da"=>1,"a"=>16,"len"=>16,"desc"=>"Codice Fiscale del Richiedente","tipo"=>"str","note"=>"","obbl"=>1),//Tipo Record
        Array("da"=>17,"a"=>36,"len"=>20,"desc"=>"Numero di Protocollo","tipo"=>"str","note"=>"Campo obbligatorio. Rappresenta il numero di protocollo assegnato alla richiesta. Deve essere uguale al protocollo del richiedente registrato nel tipo record 1.","obbl"=>1),
        //CODICE FISCALE DEL BENEFICIARIO
        Array("da"=>37,"a"=>52,"len"=>16,"desc"=>"Codice Fiscale del beneficiario","tipo"=>"str","note"=>"Se numerico deve essere allineato a sinistra.Controllo di presenza e di correttezza formale del codice fiscale; controllo di allineamento a sinistra in caso di codice fiscale numerico; controllo della \"congruenza\" del codice fiscale rispetto ai dati anagrafici impostati;","obbl"=>1),
        //DATI IDENTIFICATIVI DEL BENEFICIARIO (PERSONA FISICA)
        Array("da"=>53,"a"=>78,"len"=>26,"desc"=>"Cognome","tipo"=>"int","note"=>"","obbl"=>0),//Tipo Record
        Array("da"=>79,"a"=>103,"len"=>25,"desc"=>"Nome","tipo"=>"int","note"=>"","obbl"=>0),//Tipo Record
        Array("da"=>104,"a"=>104,"len"=>1,"desc"=>"Sesso","tipo"=>"str","note"=>"Valori ammessi:M = Maschio, F = Femmina","obbl"=>0),//Tipo Record
        Array("da"=>105,"a"=>112,"len"=>8,"desc"=>"Data di nascita","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\"","obbl"=>0),//Tipo Record
        Array("da"=>113,"a"=>116,"len"=>4,"desc"=>"Codice catastale del Comune di nascita","tipo"=>"int","note"=>"Codice catastale del comune di nascita del soggetto obbligato","obbl"=>0),//Tipo Record
        //DATI IDENTIFICATIVI DEL BENEFICIARIO (PERSONA NON FISICA)
        Array("da"=>117,"a"=>176,"len"=>60,"desc"=>"Denominazione","tipo"=>"str","note"=>"Denominazione del soggetto obbligato"),//Tipo Record
        Array("da"=>177,"a"=>180,"len"=>4,"desc"=>"Codice catastale della sede legale","tipo"=>"str","note"=>"Codice catastale del comune della sede legale del soggetto obbligato.","obbl"=>0),//Tipo Record
        //QUALIFICA DEL BENEFICIARIO
        Array("da"=>181,"a"=>181,"len"=>1,"desc"=>"Qualifica del beneficiario","tipo"=>"str","note"=>"Valori Ammessi : 1 = Proprietario; 2 = Usufruttuario, 3 = Titolare di altro diritto sull'immobile, 4 = Rappresentante legale o volontario di uno degli aventi titolo sopra indicati","obbl"=>0),
        //CARATTERI DI CONTROLLO
        Array("da"=>182,"a"=>366,"len"=>185,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)
    ),
    //TRACCIATO RECORD DI DETTAGLIO - IDENTIFICAZIONE DEI DATI CATASTALI
    "3"=>Array(
        //IDENTIFICATIVO RECORD
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"3\"","obbl"=>1),//Tipo Record
        //DATI DI CONCATENZAZIONE
        Array("da"=>1,"a"=>16,"len"=>16,"desc"=>"Codice Fiscale del Richiedente","tipo"=>"str","note"=>"","obbl"=>1),//Tipo Record
        Array("da"=>17,"a"=>36,"len"=>20,"desc"=>"Numero di Protocollo","tipo"=>"str","note"=>"Campo obbligatorio. Rappresenta il numero di protocollo assegnato alla richiesta. Deve essere uguale al protocollo del richiedente registrato nel tipo record 1.","obbl"=>1),
        //DATI CATASTALI - IDENTIFICATIVO N.C.T.R. / N.C.E.U.
        Array("da"=>37,"a"=>37,"len"=>1,"desc"=>"Tipo Unità","tipo"=>"str","note"=>"Valori Ammessi : T = Terreni, F = Fabbricati","obbl"=>0),
        Array("da"=>38,"a"=>40,"len"=>3,"desc"=>"Sezione","tipo"=>"str","note"=>"Identificativo della Sezione desunto dai dati catastali del terreno o del fabbricato","obbl"=>0),
        Array("da"=>41,"a"=>45,"len"=>5,"desc"=>"Foglio","tipo"=>"str","note"=>"Identificativo del Foglio desunto dai dati catastali del terreno o del fabbricato","obbl"=>0),
        Array("da"=>46,"a"=>50,"len"=>5,"desc"=>"Particella","tipo"=>"str","note"=>"Identificativo della Particella desunto dai dati catastali del terreno o del fabbricato","obbl"=>0),
        Array("da"=>51,"a"=>54,"len"=>4,"desc"=>"Estensione Particella","tipo"=>"str","note"=>"Estensione della particella (solo per Comuni con sistema tavolare )","obbl"=>0),
        Array("da"=>55,"a"=>55,"len"=>1,"desc"=>"Tipo Particella","tipo"=>"str","note"=>"Solo per Comuni con sistema tavolare. Valori ammessi:F = Fondiario, E = Edificiale","obbl"=>0),
        Array("da"=>56,"a"=>59,"len"=>4,"desc"=>"Subalterno","tipo"=>"str","note"=>"Identificativo del Subalterno desunto dai dati catastali del terreno o del fabbricato","obbl"=>0),
        //CARATTERI DI CONTROLLO
        Array("da"=>60,"a"=>366,"len"=>307,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)
    ),
    //TRACCIATO RECORD DI DETTAGLIO - IDENTIFICAZIONE DEI PROFESSIONISTI
    "4"=>Array(
        //IDENTIFICATIVO RECORD
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"4\"","obbl"=>1),//Tipo Record
        //DATI DI CONCATENZAZIONE
        Array("da"=>1,"a"=>16,"len"=>16,"desc"=>"Codice Fiscale del Richiedente","tipo"=>"str","note"=>"","obbl"=>1),//Tipo Record
        Array("da"=>17,"a"=>36,"len"=>20,"desc"=>"Numero di Protocollo","tipo"=>"str","note"=>"Campo obbligatorio. Rappresenta il numero di protocollo assegnato alla richiesta. Deve essere uguale al protocollo del richiedente registrato nel tipo record 1.","obbl"=>1),
        //CODICE FISCALE DEL PROFESSIONISTA (PROGETTISTA/DIRETTORE LAVORI)
        Array("da"=>37,"a"=>52,"len"=>16,"desc"=>"Codice Fiscale del professionista","tipo"=>"str","note"=>"","obbl"=>1),
        Array("da"=>53,"a"=>53,"len"=>1,"desc"=>"Albo / Elenco Professionale","tipo"=>"str","note"=>"Valori Ammessi : 1 = Agronomo;2 = Architetto;3 = Geometra;4 = Ingegnere;5 = Perito;6 = Altro;","obbl"=>1),
        Array("da"=>54,"a"=>55,"len"=>2,"desc"=>"Provincia Albo","tipo"=>"str","note"=>"Sigla della provincia","obbl"=>1),
        Array("da"=>56,"a"=>65,"len"=>10,"desc"=>"Numero Iscrizione Albo / Elenco","tipo"=>"str","note"=>"","obbl"=>1),
        Array("da"=>66,"a"=>66,"len"=>1,"desc"=>"Qualifica del professionista","tipo"=>"str","note"=>"Valori Ammessi : 0 = Progettista;1 = Direttore dei lavori;2 = Progettista e Direttore dei lavori","obbl"=>1),
        //CARATTERI DI CONTROLLO
        Array("da"=>67,"a"=>366,"len"=>300,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)
    ),
    //TRACCIATO RECORD DI DETTAGLIO - IDENTIFICAZIONE DELLE IMPRESE
    "5"=>Array(
        //IDENTIFICATIVO RECORD
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"5\"","obbl"=>1),//Tipo Record
        //DATI DI CONCATENZAZIONE
        Array("da"=>1,"a"=>16,"len"=>16,"desc"=>"Codice Fiscale del Richiedente","tipo"=>"str","note"=>"","obbl"=>1),//Tipo Record
        Array("da"=>17,"a"=>36,"len"=>20,"desc"=>"Numero di Protocollo","tipo"=>"str","note"=>"Campo obbligatorio. Rappresenta il numero di protocollo assegnato alla richiesta. Deve essere uguale al protocollo del richiedente registrato nel tipo record 1.","obbl"=>1),
        // PARTITA IVA DELL'IMPRESA
        Array("da"=>37,"a"=>47,"len"=>11,"desc"=>"Partita IVA dell'impresa","tipo"=>"str","note"=>"Partita IVA dell'impresa esecutrice delle opere","obbl"=>1),
        //DATI IDENTIFICATIVI DELL'IMPRESA
        Array("da"=>48,"a"=>97,"len"=>50,"desc"=>"Denominazione o Ragione Sociale","tipo"=>"str","note"=>"","obbl"=>1),
        Array("da"=>98,"a"=>101,"len"=>4,"desc"=>"Codice catastale del comune della sede legale","tipo"=>"str","note"=>"","obbl"=>0),
        //CARATTERI DI CONTROLLO
        Array("da"=>102,"a"=>366,"len"=>300,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)
    ),
    "9"=>Array(
        //DATI IDENTIFICATIVI DELLA FORNITURA
        Array("da"=>0,"a"=>0,"len"=>1,"desc"=>"Tipo Record","tipo"=>"int","note"=>"Vale sempre \"9\"","obbl"=>1),//Tipo Record
        Array("da"=>1,"a"=>5,"len"=>5,"desc"=>"Codice identificativo della fornitura","tipo"=>"str","note"=>"Vale sempre \"DIAXX\"","obbl"=>1),//Tipo Record
        Array("da"=>6,"a"=>7,"len"=>2,"desc"=>"Codice numerico della fornitura","tipo"=>"int","note"=>"Vale sempre \"29\"","obbl"=>1),//Tipo Record
        //IDENTIFICATIVO FISCALE DEL SOGGETTO OBBLIGATO
        Array("da"=>8,"a"=>23,"len"=>13,"desc"=>"Codice fiscale","tipo"=>"int","note"=>"Se è numerico deve essere allineato a sinistra.Controllo di presenza e di correttezza formale del codice fiscale; controllo di allineamento a sinistra in caso di codice fiscale numerico; controllo della \"congruenza\" del codice fiscale rispetto ai dati anagrafici impostati;","obbl"=>1),//Tipo Record
        //DATI IDENTIFICATIVI DEL SOGGETTO OBBLIGATO (da impostare solo nel caso di persona fisica)
        Array("da"=>24,"a"=>49,"len"=>26,"desc"=>"Cognome","tipo"=>"str","note"=>"Cognome del soggetto obbligato","obbl"=>0),//Tipo Record
        Array("da"=>50,"a"=>74,"len"=>25,"desc"=>"Nome","tipo"=>"str","note"=>"Nome del soggetto obbligato","obbl"=>0),//Tipo Record
        Array("da"=>75,"a"=>75,"len"=>1,"desc"=>"Sesso","tipo"=>"str","note"=>"Valori ammessi:M = Maschio, F = Femmina","obbl"=>0),//Tipo Record
        Array("da"=>76,"a"=>83,"len"=>8,"desc"=>"Data di nascita","tipo"=>"str","note"=>"Da indicare nel formato \"GGMMAAAA\"","obbl"=>0),//Tipo Record
        Array("da"=>84,"a"=>87,"len"=>4,"desc"=>"Codice catastale del Comune di nascita","tipo"=>"int","note"=>"Codice catastale del comune di nascita del soggetto obbligato","obbl"=>0),//Tipo Record
        //DATI IDENTIFICATIVI DEL SOGGETTO OBBLIGATO (da impostare solo nel caso di persona giuridica)
        Array("da"=>88,"a"=>147,"len"=>60,"desc"=>"Denominazione","tipo"=>"int","note"=>"Denominazione del soggetto obbligato"),//Tipo Record
        Array("da"=>148,"a"=>151,"len"=>4,"desc"=>"Codice catastale della sede legale","tipo"=>"int","note"=>"Codice catastale del comune della sede legale del soggetto obbligato.","obbl"=>0),//Tipo Record
        //ESTREMI DELLA FORNITURA
        Array("da"=>152,"a"=>155,"len"=>4,"desc"=>"Anno di riferimento","tipo"=>"str","note"=>"Da indicare nel formato \"AAAA\"","obbl"=>0),//Tipo Record
        //CARATTERI DI CONTROLLO
        Array("da"=>156,"a"=>366,"len"=>211,"desc"=>"Filler","tipo"=>"str","note"=>"Da impostare a spazi","obbl"=>1),//Tipo Record
        Array("da"=>367,"a"=>367,"len"=>1,"desc"=>"Carattere di controllo","tipo"=>"str","note"=>"Vale sempre \"A\"","obbl"=>1),//Tipo Record
        Array("da"=>368,"a"=>369,"len"=>2,"desc"=>"Caratteri di fine riga","tipo"=>"str","note"=>"Caratteri ASCII \"CR\" e \"LF\" (valori esadecimali \"0D\" \"0A\")","obbl"=>1)

    )
);
class anagrafe_tributaria
{
    var $avvioproc;
    var $soggetti;
    var $lavori;
    var $cterreni;
    var $indirizzi;

    function __construct(){

    }

    function getDate($d){
        $d = str_pad($d, 8, "0", STR_PAD_LEFT);
        $date = implode("/",Array(substr($d,0,2),substr($d,2,2),substr($d,4,4)));
        return $date;
    }
    function setDate($d){
        return str_replace("/","",$d);
    }
    function readRecord($line){
        $recordType = $line[0];
        switch($recordType){
            case "1":
                $tipoRichiesta = $line[10];
                $tipoIntervento = $line[11];
                $protocollo = $line[12];
                $dataPresentazione = $this->getDate($line[14]);
                break;
            case "2":
                break;
            case "3":
                break;
            case "4":
                break;
            case "5":
                break;
            default:
                break;
        }
    }
    private  function rec1($line){

    }
    private  function rec2($line){

    }
    private  function rec3($line)
    {

    }
    private  function rec4($line){

    }
    private  function rec5($line){

    }
}
<?php
//VERIFICARE IN BASE AL TIPO DI UTENTE I SERVIZI DISPONIBILI
//se passo un idpratica punto alla pratica

include_once ("login.php");

$file = TAB_ELENCO."index.json";
$menu=0;
$last_change=shell_exec("git log -1 --pretty=format:'%ci'");
?>

<html>
<head>
<title>PraticaWeb: Servizi</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
	utils::loadJS();
	utils::loadCss();
?>
<SCRIPT language="javascript" >
    window.name='indexPraticaweb';
    var firstWin = window;
    var windows={};
    $(document).ready(function(){
        $('#index').tabs({
            
        });
    });
</SCRIPT>


</head>
<body>

<?php
include "./inc/inc.page_header.php";
?>

        <div id="index"style="height:600px;">
            <ul >
                <li><a href="#pratiche">Gestione Pratiche Edilizie</a></li>
                <li><a href="#vigilanza">Gestione Pratiche Vigilanza</a></li>
                <li><a href="#cartografia">Cartografia</a></li>
                <li><a href="#admin">Amministrazione</a></li>
            </li>
            <div id="pratiche">
                
            </div>
            <div id="vigilanza">
                
            </div>
            <div id="cartografia">
                
            </div>
            <div id="admin">
                
            </div>
        </div>


<P class=footer><IMG height=1 alt="" src="images/pixel.gif"  space=4><BR>
        <table class="footer" cellspacing="10"><tr><td>Ultima modifica: <a href="#"><?php echo $last_change?></a></td><td>Telefono : 010-2474491</td><td>email : <a href="mailto:assistenza@gisweb.it">gisweb</a></td></tr></table><BR>
      </P>
</BODY>
</HTML>

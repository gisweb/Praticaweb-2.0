<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';
?>
<html>
<head>
    <title>Scadenze</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::writeCSS();
    utils::writeJS();
?>
</head>
<body>
    <?php include "./inc/inc.page_header.php";?>
    <FORM id="ricerca" name="ricerca" method="post" action="pe.ricerca.php">
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
            <tr> 
                  <td> 
                  <!-- intestazione-->
                          <H2 class="blueBanner">Elenco Scadenze delle Pratiche</H2>
                  <!-- fine intestazione-->
                  </td>
            </tr>
            <tr> 
                  <td> 			
                          <!-- ricerca base pratica -->
                      <?php

                      $tabella=new tabella_v("pe/report_scadenze.tab",'standard');
                      //$tabella->set_db($db);	
                      //$tabella_avanzata=new tabella_v("$tabpath/ricerca_avanzata.tab",'standard');
                      //in avanzata devo settare il db perchÃš c'Ãš un elenco

                      $tabella->edita();?>
                          <!-- ricerca avanzata pratica -->


                  </td>
            </tr>
            <tr> 
                          <!-- riga finale -->
                          <td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
             </tr>  
        </table>
</body>
</html>
<?php
$utente = utils::getUser($_SESSION["USER_ID"]);
$scadenze = json_encode(appUtils::getScadenze($_SESSION["USER_ID"]));
$verifiche = json_encode(appUtils::getVerifiche($_SESSION["USER_ID"]));
$annotazioni = json_encode(appUtils::getNotifiche($_SESSION["USER_ID"]));

$script = <<<EOT
    <script language="javascript">
        var scadenze = $scadenze;
        var verifiche = $verifiche;
        var annotazioni = $annotazioni;
    </script>        
EOT;

echo $script;

?>

<!-- ### STANDARD  PAGE  HEADER  INIZIO ##################################################### -->
	<style>
		div#intestazione { background-image:url(images/sfondo.png); width:100%; border-right:1px solid #000000; height:100px; background-repeat:repeat-x; padding:0px; margin-top:0px; border-right:1px solid #000000; width:100%; }
		div#top_menu A:link	{COLOR: #FFFFFF; text-decoration:none; }
		div#top_menu A:visited {COLOR: #FFFFFF; text-decoration:none;}
		div#top_menu A:hover {Color: #FF8000;text-decoration:none;}
                .message-div{
                    color:#FFFFFF;
                    font-size:11px;
                    font-family:arial;
                }
                .scadenze-div{
                    color:#ff8000;
                    font-weight:bold;
                }
		.verifiche-div{
                    color:#ff8000;
                    font-weight:bold;
                }
                .annotazioni-div{
                    color:#ff8000;
                    font-weight:bold;
                }
	</style>
	<div id="intestazione">
		<div style="background-image:url(images/curva.png); float:left; width:43; height:100px"></div>
		<div style="float:left; margin-top:20px; font-family:tahoma, verdana, arial; font-size:25px; font-weight:normal; color:#FFFFFF; width:600px; height:30">
			<i>Pratica<b style="color:#FF8000">Web</b></i>
                        <div style="font-family:arial; font-size:12px; color:#FFFFFF; margin-top:8px; height:19; width:100%;padding-bottom: 7px;">
                            <?php 
                                echo "<b>".NOME_COMUNE."</b>";
                                $titoloPratica=$_SESSION["TITOLO_$idpratica"];
                                echo "<div style='font-family:arial; font-weight:bold; font-size:12px; color:#ff8000; float:right'>$titoloPratica</div>";

                            ?>
			</div>
                        

		</div>
                
			
		
		<div style="float:right; margin-top:26px; margin-right:16px; color:#FFFFFF; font-family:arial; font-weight:bold; font-size:16px; height:24; width:450px; text-align:right">
			<div id="top_menu">
                                <a href="javascript:NewWindow('https://indata.istat.it/pdc/','istatPraticaweb',0,0,'yes')">[ ISTAT ]</a>
				<a href="javascript:NewWindow('index.php','indexPraticaweb',0,0,'yes');window.close()">[Inizio]</a>
				<a href="javascript:NewWindow('pe.ricerca.php','ricercaPraticaweb',0,0,'yes')">[Ricerca]</a>
				<a href="#">[Guida]</a>
				<a href="javascript:window.print();">[Stampa]</a>
				<a href="javascript:closeWindow()">[Chiudi]</a>
				<?php if ($_SESSION['USER_ID']){?><a href="./admin/logout.php">[Esci]</a><?php }?>
			</div>
                     
			<div style="font-family:arial; font-weight:bold; font-size:12px; color:#ff8000; margin-top:19px; height:19; width:100%;padding-bottom: 5px;">
                            Gestione on-line delle pratiche edilizie
                        </div>
		</div>
                <div style="width:100%;height:100px;border:0px;margin-top:px;padding-top:80px;">
            
                    <TABLE style="margin-left:0px;margin-right:50px;display:inline-block;border:0px;">
                        <TR>
                            <TD STYLE="width:300px" style="float:right;"><div id="msg-utente" class="message-div">Utente : <?php print $utente ?></div></TD>
                            <TD STYLE="width:300px"><div id="msg-scadenze" class="scadenze-div message-div"></div></TD>
                            <TD STYLE="width:300px"><div id="msg-verifiche" class="message-div verifiche-div"></div></TD>
                            <TD STYLE="width:300px"><div id="msg-note" class="message-div annotazioni-div"></div></TD>

                        </TR>
                    </TABLE>
                </div>
                
	</div>
       <script src="js/notifiche.js?rand=345"></script>
        
        <div id="message-div" style="display:none;">
            
        </div>
<!-- ### STANDARD  PAGE  HEADER  FINE ##################################################### -->

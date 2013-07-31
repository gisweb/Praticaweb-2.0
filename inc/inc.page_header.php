<!-- ### STANDARD  PAGE  HEADER  INIZIO ##################################################### -->
	<style>
		div#intestazione { background-image:url(images/sfondo.png); width:100%; border-right:1px solid #000000; height:75px; background-repeat:repeat-x; padding:0px; margin-top:0px; border-right:1px solid #000000; width:100%; }
		div#top_menu A:link	{COLOR: #FFFFFF; text-decoration:none; }
		div#top_menu A:visited {COLOR: #FFFFFF; text-decoration:none;}
		div#top_menu A:hover {Color: #FF8000;text-decoration:none;}
		
	</style>
	<div id="intestazione">
		<div style="background-image:url(images/curva.png); float:left; width:43; height:75"></div>
		<div style="float:left; margin-top:20px; font-family:tahoma, verdana, arial; font-size:25px; font-weight:normal; color:#FFFFFF; width:250px; height:30">
			<i>Pratica<b style="color:#FF8000">Web</b></i>
			<div style="font-family:arial; font-size:12px; color:#FFFFFF; margin-top:8px; height:14; width:100%">
				<?=NOME_COMUNE?>
			</div>
		</div>
		<div style="float:right; margin-top:26px; margin-right:16px; color:#FFFFFF; font-family:arial; font-weight:bold; font-size:16px; height:24; width:450px; text-align:right">
			<div id="top_menu">
				<a href="javascript:NewWindow('index.php','indexPraticaweb',0,0,'yes');window.close()">[Inizio]</a>
				<a href="javascript:NewWindow('pe.ricerca.php','ricercaPraticaweb',0,0,'yes')">[Ricerca]</a>
				<a href="#">[Guida]</a>
				<a href="javascript:window.print();">[Stampa]</a>
				<a href="javascript:window.close()">[Chiudi]</a>
				<?if ($_SESSION['USER_ID']){?><a href="./admin/logout.php">[Esci]</a><?}?>
			</div>
                     
			<div style="font-family:arial; font-weight:bold; font-size:12px; color:#ff8000; margin-top:14px; height:14; width:100%">Gestione on-line delle pratiche edilizie</div>
		</div>
	</div>
<!-- ### STANDARD  PAGE  HEADER  FINE ##################################################### -->
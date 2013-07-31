<?
//VERIFICARE IN BASE AL TIPO DI UTENTE I SERVIZI DISPONIBILI
//se passo un idpratica punto alla pratica 
include_once ("login.php");

?>

<html>
<head>

<title>PraticaWeb: Servizi</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

</head>
<body onload = "javascript: window.name='indexPraticaweb'	;if (parseInt(navigator.appVersion) >= 4) { 
		window.focus(); 
	}" >

<?/*
	if($pratica){?>
		<script language="javascript">
			window.open('praticaweb.php?pratica=5772','Praticaweb', 'scrollbars=yes,menubar=no,status=no,left=0,screenX=0,screenY=0,resizable');
		</script>
	<?}*/?>
<TABLE id=header cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY> 
  <TR vAlign=top> 
    <TD align=left bgColor=#738ABD height=55 colspan="2"><A href="#" onclick=alert(window.name)><IMG height=55 alt=PraticaWeb src="images/praticaweb_logo.gif" width=161 border=0></A></TD>
    <TD align=right bgColor=#728bb8 colspan="2"></TD>
    <TD width="8%" bgColor=#728bb8>&nbsp;</TD>
  </TR>
  <TR vAlign=top> 
    <TD align=left width="22%" bgColor=#415578 height=20><A href="#"><IMG height=20 alt="Comune di .." src="images/header_band_comune.gif" width=242 border=0></A></TD>
    <TD align=left colspan="2" bgColor=#415578 height=20 valign="bottom"><font color="#FFFFFF" size="1"></font></TD>
    <TD align=right width="22%" bgColor=#415578><a href="#"><img height=20 alt="Gestione pratiche online" src="images/header_band_right.gif" width=178 border=0></a></TD>
    <TD width="8%" bgColor=#415578>&nbsp;</TD>
  </TR>
  </TBODY> 
</TABLE>
<TABLE id=main_layout cellSpacing=0 cellPadding=0 width="102%" border=0>
  <TBODY> 
  <TR vAlign=top align=left> 
    <TD width=15 rowspan="3"><IMG height=8 alt="" Src="images/pixel.gif" width=5></TD>
    <td width="768" height="30" valign="top">
    <H2 class=blueBanner>Sportello unico dell'Edilizia</H2>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione pratiche</b></font></td>
	</tr>
	</table>
		<UL>
		
		<LI>		
			<a href="javascript:NewWindow('pe.ricerca.php?new=1','ricercaPraticaweb',0,0,'yes')">Apri pratica esistente</A> - ModalitÃ  di ricerca di una pratica archiviata.
        <LI>
			<a href="javascript:NewWindow('pe.recenti.php','ricercaPraticaweb',0,0,'yes')">Pratiche recenti</A> - Elenco delle ultime 10 pratiche consultate 
			
		<LI>
			<a href="javascript:NewWindow('pe.avvioproc.php?mode=new','Praticaweb',0,0,'yes')">Nuova Pratica </A> - Inserisce una nuova pratica.
			
		<LI>
			<a href="#">Elenco delle pratiche ricevute</A> - Mostra l'elenco delle pratiche inviate all'ufficio urbanistica da professionisti esterni.
			
        <LI>
			<A href="#">Elimina Pratica</A> - Individua ed elimina la pratica scelta. 
			
        <LI>
			<A href="#">Aggiungi Pratica legge 77</A>
		 - Per inserire una pratica vecchio tipo 
		 
		</UL>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Commissione Edilizia</b></font></td>
	</tr>
		</table>		
		<UL>
        <LI><A href="javascript:NewWindow('commissione.php?pratica=0&comm=1','CommissioneEdilizia',0,0,'yes')">Nuova Commissione Edilizia</A> 
          - Gestione delle sedute di Commissione Edilizia, convocazioni, verbali.... 
        <LI><A href="javascript:NewWindow('ricerca_commissione.php','CommissioneEdilizia',0,0,'yes')">Vedi Commissione Edilizia</A> 
          - Gestione delle sedute di Commissione Edilizia, convocazioni, verbali.... 
        <LI><A href="javascript:NewWindow('ricerca_commissione.php?mode=cancella','CommissioneEdilizia',0,0,'yes')">Elimina Commissione Edilizia</A> 
          - Gestione delle sedute di Commissione Edilizia, convocazioni, verbali.... 
	    </UL>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Conferenza dei servizi</b></font></td>
	</tr>
		</table>
		<UL>		
        <LI><A href="#">Nuova Conferenza dei Servizi</A> 
          - Gestione CS. 
        <LI><A href="#">Vedi Conferenza dei Servizi</A> 
          - Gestione CS. 
        <LI><A href="#">Elimina Conferenza dei Servizi</A> 
          - Gestione CS. 
		</UL>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Pubblicazioni</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="#">Registro Pratiche</A>
		 - Stampa il registro delle pratiche relative al periodo selezionato.
        <LI><A href="#">Comunicazione ISTAT</A> 
          - Comunicazione ISTAT mensile.
        <LI><A href="#">Comunicazione ISTAT Anagrafe Tributaria</A> 
          - Comunicazione ISTAT annuale anagrafe tributaria.
       <LI><A href="#">Pubblicazione Albo Pretorio</A> 
          - Pubblicazione Albo Pretorio. 
         <LI><A href="#">Scadenziario Oneri</A> 
          - Registro delle prossime scadenze rate oneri
        <LI><A href="#">Scadenziario Lavori</A> 
          - Stampa l'elenco delle scadenze lavori: lavori iniziati dal al...... 
         <LI><A href="statistica.php?g=1">Statistica Pratiche</A> 
          - Crea il grafico dell'andamento delle pratiche per tipo. 
		 <LI><A href="statistica.php?g=2">Statistica Oneri</A> 
          - Suddivisione di oneri e costo. 
		 <LI><A href="statistica.php?g=3">Statistica Oneri</A> 
          - Grafico a barre per oneri. 
    </UL>
	  
	<H2 class=blueBanner>Sportello unico delle attivitÃ  produttive</H2>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione pratiche</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="#">Aggiungi Pratica </A>
		 - Per inserire una pratica.
        <LI><A href="#">Elenco delle pratiche ricevute</A>
		 - Mostra l'elenco delle pratiche inviate all'ufficio urbanistica da professionisti esterni.
        <LI><A href="#">Apri pratica esistente</A>
		 - ModalitÃ¯Â¿Ådi ricerca semplice di una pratica archiviata.
        <LI><A href="#">Ricerca Avanzata</A>
		 - Accesso alla ricerca.............. 
        <LI><A href="#">Elimina Pratica</A> 
          - Individua ed elimina la pratica scelta. 
        <LI><A href="#">Gestione Cartelle</A> 
          - Gestione dei gruppi di pratiche.        
		</UL>
		<H2 class=blueBanner>Servizio di Vigilanza Territoriale</H2>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione pratiche</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="http://">Aggiungi Pratica </A>
		 - Per inserire una pratica.
        <LI><A href="http://">Apri pratica esistente</A>
		 - ModalitÃ¯Â¿Ådi ricerca semplice di una pratica archiviata.
        <LI><A href="http://">Ricerca Avanzata</A>
		 - Accesso alla ricerca.............. 
        <LI><A href="http://">Elimina Pratica</A> 
          - Individua ed elimina la pratica scelta. 
        <LI><A href="http://">Registro Pratiche</A>
		 - Stampa il registro delle pratiche relative al periodo selezionato.
         <LI><A href="https//">Altro ??????</A> 
          - Da vedere. 
    </UL>  
			<H2 class=blueBanner>Cartografia</H2>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione pratiche</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="javascript:NewWindow('cdu.richiesta.php?mode=new','Praticaweb',0,0,'yes')">Certificato di Destinazione Urbanistica </A>
		<LI><A href="javascript:NewWindow('cdu.ricerca.php','Praticaweb',0,0,'yes')">Ricerca Certificato di Destinazione Urbanistica </A>
		 - Redazione automatica del Certificato di destinazione urbanistica.
        <LI><A href="javascript:openFrames(1010, 745, 2, '')">Stumento Urbanistico Generale </A>
		 - Elenco delle zone - Vista complessiva del SUG.
        <LI><A href="http://">Catastale</A>
		 - Quadro d'unione del catastale 
        <LI><A href="http://">Elenco Asservimenti</A> 
          - Gestione dell'elenco delle particelle asservite. 
		<LI><A href="http://">Cerca Particella</A> 
          - ModalitÃ¯Â¿Ådi ricerca di una particella catastale. 
        <LI><A href="http://">Cerca Indirizzo</A>
		 - Trova un civico o una via.
         <LI><A href="https//">Altro ??????</A> 
          - Da vedere. 
    </UL>  
      <H2 class=blueBanner>Amministrazione Sistema</H2>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione utenti</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="#" onclick="window.open('utenti.php?mode=new')">Aggiungi Utente </A>
		 - Inserisce un nuovo utente e assegna il profilo.
        <LI><A href="#" onclick="window.open('utenti.php?mode=update')">Modifica Profilo</A>
		 - Modifica un profilo utente.
        <LI><A href=""#" onclick="window.open('utenti.php?mode=delete')">Rimuovi</A>
		 - Elimina un Utente.
		</UL>
	 <table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
        <td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b>Gestione tabella</b></font></td>
	</tr>
		</table>
		<UL>
        <LI><A href="https//">Comuni</A> 
          - Elenco dei Comuni d'Italia. 
        <LI><A href="https//">Stradario</A> 
          - Elenco delle vie per Comune. 
         <LI><A href="https//">Tipo di Pratica</A> 
          - Elenco dei tipi di pratica. 
       <LI><A href="http://">Allegati</A> 
          - Elenco dei possibili allegati alla pratica.
       <LI><A href="http://">Oneri</A> 
          - Elenco tariffe impostate.
       <LI><A href="http://">Membri C.E.</A> 
          - Elenco membri commissione edilizia.
		</UL>

      <IMG height=1 src="images/gray_light.gif" width="100%"  vspace=1><BR>

      <P class=footer><IMG height=1 alt="" src="images/pixel.gif"  space=4><BR>
        Ultima modifica:<BR>
      </P>
 </TR>
  </TBODY> 
</TABLE>


</BODY>
</HTML>

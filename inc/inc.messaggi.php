
<table>
<tr>
<td>

<script type="text/javascript">

/***********************************************
* Fading Scroller- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var delay = 2000; //set delay between message change (in miliseconds)
var maxsteps=30; // number of steps to take to change from start color to endcolor
var stepdelay=40; // time in miliseconds of a single step
//**Note: maxsteps*stepdelay will be total time in miliseconds of fading effect
var startcolor= new Array(255,255,255); // start color (red, green, blue)
var endcolor=new Array(0,0,0); // end color (red, green, blue)

var fcontent=new Array();
begintag='<div style="font: normal 14px Arial; padding: 5px;">'; //set opening tag, such as font declarations
fcontent[0]='<font class="footerlinks"> <font size=+1 color=#728bb8><b>PraticaWeb</b></font> è un prodotto sviluppato da <b><color=#728bb8>Gis&Web S.r.l.</font></b>';
fcontent[1]='<font class="footerlinks"> PraticaWeb: collegamenti disponibili</font>';
fcontent[2]='<font class="footerlinks"> <a href="#"><b>Cartografia</b></a> </font>collegamento alla cartografia';
fcontent[3]='<font class="footerlinks"> <a href="#"><b>Leggi e Regolamenti</b></a></font> Collegamento al sito Bosetti e Gatti';
fcontent[4]='<font class="footerlinks"> <a href="#"><b>Leggi Regionali</b></a></font> Collegamento al sito';

fcontent[5]='<font class="footerlinks"><img src="images/various67.gif" width="32" height="32"> <a href="#"><b>Novità:</b></a></font>';
fcontent[6]='<font class="footerlinks">Nuova gestione della commissione edilizia</font>';
fcontent[7]='<font class="footerlinks">Guida, per il progettista, alla compilazione della domanda e individuazione delle informazioni </font>';
fcontent[8]='<font class="footerlinks">Accesso del progettista al form per la domanda on-line</font>';
fcontent[9]='<font class="footerlinks">Calcolo degli oneri diponibile al progettista con le stesse modalità dell\' Ufficio</font>';

fcontent[10]='<font class="footerlinks"><b>Attenzione:</b> questo spazio può essere utilizzato per la gestione di piu link<br>o di messaggi elaborati automaticamente (avvisi) <br> o ancora di messaggi inviati da un utente';

closetag='</div>';

var fwidth='550px'; //set scroller width
var fheight='30px'; //set scroller height

var fadelinks=1;  //should links inside scroller content also fade like text? 0 for no, 1 for yes.

///No need to edit below this line/////////////////


var ie4=document.all&&!document.getElementById;
var DOM2=document.getElementById;
var faderdelay=0;
var index=0;


/*Rafael Raposo edited function*/
//function to change content
function changecontent(){
  if (index>=fcontent.length)
    index=0
  if (DOM2){
    document.getElementById("fscroller").style.color="rgb("+startcolor[0]+", "+startcolor[1]+", "+startcolor[2]+")"
    document.getElementById("fscroller").innerHTML=begintag+fcontent[index]+closetag
    if (fadelinks)
      linkcolorchange(1);
    colorfade(1, 15);
  }
  else if (ie4)
    document.all.fscroller.innerHTML=begintag+fcontent[index]+closetag;
  index++
}

// colorfade() partially by Marcio Galli for Netscape Communications.  ////////////
// Modified by Dynamicdrive.com

function linkcolorchange(step){
  var obj=document.getElementById("fscroller").getElementsByTagName("A");
  if (obj.length>0){
    for (i=0;i<obj.length;i++)
      obj[i].style.color=getstepcolor(step);
  }
}

/*Rafael Raposo edited function*/
var fadecounter;
function colorfade(step) {
  if(step<=maxsteps) {	
    document.getElementById("fscroller").style.color=getstepcolor(step);
    if (fadelinks)
      linkcolorchange(step);
    step++;
    fadecounter=setTimeout("colorfade("+step+")",stepdelay);
  }else{
    clearTimeout(fadecounter);
    document.getElementById("fscroller").style.color="rgb("+endcolor[0]+", "+endcolor[1]+", "+endcolor[2]+")";
    setTimeout("changecontent()", delay);
	
  }   
}

/*Rafael Raposo's new function*/
function getstepcolor(step) {
  var diff
  var newcolor=new Array(3);
  for(var i=0;i<3;i++) {
    diff = (startcolor[i]-endcolor[i]);
    if(diff > 0) {
      newcolor[i] = startcolor[i]-(Math.round((diff/maxsteps))*step);
    } else {
      newcolor[i] = startcolor[i]+(Math.round((Math.abs(diff)/maxsteps))*step);
    }
  }
  return ("rgb(" + newcolor[0] + ", " + newcolor[1] + ", " + newcolor[2] + ")");
}

if (ie4||DOM2)
  document.write('<div id="fscroller" style="border:0px solid black;width:'+fwidth+';height:'+fheight+'"></div>');

if (window.addEventListener)
window.addEventListener("load", changecontent, false)
else if (window.attachEvent)
window.attachEvent("onload", changecontent)
else if (document.getElementById)
window.onload=changecontent

</script>

</td>
</tr>
</table>
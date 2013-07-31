// JavaScript Document
//DHTML Window script- Copyright Dynamic Drive (http://www.dynamicdrive.com)
//For full source code, documentation, and terms of usage,
//Visit http://www.dynamicdrive.com/dynamicindex9/dhtmlwindow.htm

var dragapproved=false
var minrestore=0
var initialwidth,initialheight
var ie5=document.all&&document.getElementById
var ns6=document.getElementById&&!document.all

function iecompattest(){
return (document.compatMode!="BackCompat")? document.documentElement : document.body
}

function drag_drop(e){
if (ie5&&dragapproved&&event.button==1){
document.getElementById("dwindow").style.left=tempx+event.clientX-offsetx+"px"
document.getElementById("dwindow").style.top=tempy+event.clientY-offsety+"px"
}
else if (ns6&&dragapproved){
("dwindow").style.left=tempx+e.clientX-offsetx+"px"
document.getElementById("dwindow").style.top=tempy+e.clientY-offsety+"px"
}
}

function initializedrag(e){
offsetx=ie5? event.clientX : e.clientX
offsety=ie5? event.clientY : e.clientY
document.getElementById("dwindowcontent").style.display="none" //extra
tempx=parseInt(document.getElementById("dwindow").style.left)
tempy=parseInt(document.getElementById("dwindow").style.top)

dragapproved=true
document.getElementById("dwindow").onmousemove=drag_drop
}

function loadwindow(url,width,height){

if (!ie5&&!ns6)
window.open(url,"","width=width,height=height,scrollbars=1")
else{
document.getElementById("dwindow").style.display=''
document.getElementById("dwindow").style.width=initialwidth=width+"px"
document.getElementById("dwindow").style.height=initialheight=height+"px"
document.getElementById("dwindow").style.left="145px"
document.getElementById("dwindow").style.top="135px"
//document.getElementById("dwindow").style.top=ns6? window.pageYOffset*1+30+"px" : iecompattest().scrollTop*1+30+"px"
document.getElementById("cframe").src=url
window.top
}
}

function maximize(){
if (minrestore==0){
minrestore=1 //maximize window
document.getElementById("maxname").setAttribute("src","restore.gif")
document.getElementById("dwindow").style.width=ns6? window.innerWidth-20+"px" : iecompattest().clientWidth+"px"
document.getElementById("dwindow").style.height=ns6? window.innerHeight-20+"px" : iecompattest().clientHeight+"px"
}
else{
minrestore=0 //restore window
document.getElementById("maxname").setAttribute("src","max.gif")
document.getElementById("dwindow").style.width=initialwidth
document.getElementById("dwindow").style.height=initialheight
}
document.getElementById("dwindow").style.left=ns6? window.pageXOffset+"px" : iecompattest().scrollLeft+"px"
document.getElementById("dwindow").style.top=ns6? window.pageYOffset+"px" : iecompattest().scrollTop+"px"
}

function closeit(){
document.getElementById("cframe").src=""//se è lento caricare di base un file di attesa
document.getElementById("dwindow").style.display="none"
}

function stopdrag(){
dragapproved=false;
document.getElementById("dwindow").onmousemove=null;
document.getElementById("dwindowcontent").style.display="" //extra
}


function visibile(sezione, div_c, div_o){
	sezione.style.display = ''
	div_c.style.display = ''
	div_o.style.display = 'none'
}
function invisibile(sezione, div_c, div_o, hid_val){
	sezione.style.display = 'none'
	div_c.style.display = 'none'
	div_o.style.display = ''
}



/*function calcola_cf(){
	
	opt=document.getElementById("sesso");
	loadwindow("calcolacf.php?comune=" + document.getElementById("comunato").value+"&nome="+document.getElementById("nome").value+"&cognome="+document.getElementById("cognome").value+"&datanascita="+document.getElementById("data").value+"&sesso="+opt[opt.selectedIndex].text,400,200);
}*/

function get_elenco(txt_campo){
	if(txt_campo.indexOf('#')>0){
		var arr=txt_campo.split('#');
		var pr =new Array();
		var campo = new Array();
		var schema = new Array();
		var val= new Array();
		for (i=0;i<arr.length;i++){
			if (arr[i].indexOf('.')>0){
				var tmp=arr[i].split('.');
				campo.push(tmp[0]);
				schema.push(tmp[1]);
				
			}
			else{
				campo.push(arr[i]);
				schema.push('pe');
			}
			var value=document.getElementById(campo[i]).value;
			val.push(value);
			pr.push('param[]='+campo[i]+'&val[]='+val[i]);
		}
		var param='campo='+campo[0]+'&s='+val[0]+'&schema='+schema[0]+'&'+pr.join('&');
	}
	else{
		if (txt_campo.indexOf('.')>0){
			var tmp=txt_campo.split('.');
			var campo=tmp[0];
			var schema=tmp[1];
			
		}
		else{
			var campo=txt_campo;
			var schema='pe';
		}
		var val=document.getElementById(campo).value;
		var param='campo=' + campo + '&s=' + val +'&schema='+schema;
	}
	//url='elenco.php?campo=' + campo + '&s=' + document.getElementById(campo).value+'&schema='+schema;
	loadwindow('elenco.php?'+param,300,400);
}

function get_file(txt_campo){
	loadwindow('carica_foto.php?campo=' + txt_campo + '&s=' + document.getElementById(txt_campo).value,600,300);
}


function NewWindow(url, winname, winwidth, winheight, scroll) {
	
	if (!winwidth)
		  winwidth =screen.availWidth-10;
	if (!winheight)
		  winheight = screen.availHeight-35;
	winprops = 'height='+winheight+',width='+winwidth+',scrollbars='+scroll+',menubar=no,top=0,status=no,left=0,screenX=0,screenY=0,resizable,close=no';
	
	
	win = window.open(url, winname, winprops)
	if (parseInt(navigator.appVersion) >= 4) { 
		win.window.focus(); 
	}
}

function OpenMapset (mapsetid,template,parameters){
		if(!template) template = 'gisclient';
		var winWidth = window.screen.availWidth-8;
		var winHeight = window.screen.availHeight-55;
		var winName = 'mapset_'+mapsetid;
		template="template/" + template;
		if(!parameters) parameters='';
		if(template.indexOf('?')>0)
			template=template + '&';
		else
			template=template + '?';
		var mywin=window.open('/gisclient21/' + template + "mapset=" + mapsetid + "&" + parameters, winName,"width=" + winWidth + ",height=" + winHeight + ",menubar=no,toolbar=no,scrollbar=auto,location=no,resizable=yes,top=0,left=0,status=yes");
		mywin.focus();
	}


function link0(){
  var args = link0.arguments;
  var numargs = args.length;
  var key=args[0];
  var pratica=args[1];
  var target=args[2];
  switch(target) {
  
	case 'cn.scheda_documento'://dettaglio del documento allegato (args=id documento)
		window.location=target+'.php?id='+key+'&pratica='+pratica;
	break;
	
	case 'cn.integrazioni'://dettaglio richeste integrazioni e integrazioni documenti(args=id integrazione o richiesta)
		iter=args[3];
		nomeiter=args[4];
		window.location=target+'.php?id='+key+'&pratica='+pratica+'&iter='+iter+'&nomeiter='+nomeiter;
	break;
	
	
	
	
   }

}
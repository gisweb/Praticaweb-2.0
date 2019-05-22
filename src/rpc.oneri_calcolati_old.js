function set_elenco(result){
	alert(result.length)
	for(i=0;i<result.length;i++){
		var res=result[i];
		var pratica=res[0];
		var obj=document.getElementById(res[1]);
		var valori=res[2];
		var testi=res[3];
		var campo=res[4];
		var val=res[5];
		
		
		for(j=0;j<obj.options.length;j++) obj.remove(j);	//Rimuovo tutte le opzioni dal select
		
		obj.options[0]=new Option('Seleziona ====>','');	//Aggiungo il Primo elemento
		
		for(j=1;j<=valori.length;j++){
			obj.options[j]=new Option(testi[j-1],valori[j-1]);	//Aggiungo le altre opzioni
			alert(testi[j-1])
		}
		
		setvalue=(campo=="id")?(valori):(testi);											//Verifico le l'elemento selezionato è da cercare tra gli id o le opzioni
		if (val) obj.value=val.toUpperCase();
		//load_elenco(obj,pratica);
		/*if (setvalue.length>0){
			for(j=1;j<=setvalue.length;j++) {
				if (setvalue[j-1].toUpperCase()==val.toUpperCase()) {
					obj.options[j].selected=1;
					//load_elenco(obj,pratica);												// Chiamata in cascata del RPC se ho selezionato un elemento
				}
			}
		}*/
	}
}

function load_elenco(obj,pratica){
	funzione="funz=crea_elenco&oggetto="+obj.name+"&pratica="+pratica;
	param="";
	
	switch (obj.name){
		case "init":
			var oggetti=new Array('anno','tabella','intervento','c1','c2','c3','c4','d1','d2');
			var tabelle=new Array('oneri.elenco_anno','oneri.elenco_funzione','oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2');
			var filtri=new Array(Array(),Array('anno'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'));
			break;
		case "anno":
			/*var oggetti=new Array('tabella','intervento','c1','c2','c3','c4','d1','d2');
			var tabelle=new Array('oneri.elenco_funzione','oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2');
			var filtri=new Array(Array('anno'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'));*/
			var oggetti=new Array('tabella');
			var tabelle=new Array('oneri.elenco_funzione');
			var filtri=new Array(Array('anno'));
			
			break;
		case "tabella":
			var oggetti=new Array('intervento','c1','c2','c3','c4','d1','d2');
			var tabelle=new Array('oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2');
			var filtri=new Array(Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'));
			break;
		default :
			break;
	}
	
	for(k=0;k<oggetti.length;k++){
		param+="&elenco[]="+oggetti[k]+"&tabella[]="+tabelle[k];
		for(j=0;j<filtri[k].length;j++){
			var val='';
			var o = document.getElementById(filtri[k][j]);
			switch (o.type){
				case "select-one":
					if(o.selectedIndex>=0)	val=o.options[o.selectedIndex].value
					break;
				case "checkbox":
					if (o.checked) val=o.value;
					break;
				default:
					val=o.value;
					break;
			}
			param+="&filtro["+oggetti[k]+"][]="+o.name+"&val_filtro["+oggetti[k]+"][]="+val;
		}
		
	}
	alert(funzione+param);
	makeRequest("rpc.php",funzione+param,'set_elenco','GET');
}

function init(pratica){
	var tmp=new Object();
	tmp.name="init";
	load_elenco(tmp,pratica);
	/*obj=document.getElementById('anno');
	param="funz=crea_elenco&oggetto[]="+obj.name+"&elenco[]="+obj.name+"&tabella[]=oneri.elenco_anno&pratica="+pratica;
	makeRequest("rpc.php",param,'set_elenco','GET');*/

}

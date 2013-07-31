var risultato=0;
function set_elenco(result){
	for(i=0;i<result.length;i++){
		var selezionato=0;
		var res=result[i];
		var pratica=res[0];
		var obj=(obj=='init')?(document.getElementById('anno')):(document.getElementById(res[1]));
		var valori=res[2];
		var testi=res[3];
		var campo=res[4];
		var val=res[5];
		var chiamante=res[8];
		
		
		for(j=0;j<obj.options.length;j++) obj.remove(j);	//Rimuovo tutte le opzioni dal select
		
		if (valori.length==1){
			obj.options[0]=new Option(testi[0],valori[0]);	//Aggiungo le altre opzioni
		}
		else{
			obj.options[0]=new Option('Seleziona ====>','');			//Aggiungo il Primo elemento
			for(j=1;j<=valori.length;j++) obj.options[j]=new Option(testi[j-1],valori[j-1]);	//Aggiungo le altre opzioni
			setvalue=(campo=="id")?(valori):(testi);											//Verifico le l'elemento selezionato � da cercare tra gli id o le opzioni
			if (setvalue.length>0){
				for(j=1;j<=setvalue.length;j++) {
					if (setvalue[j-1].toUpperCase()==val.toUpperCase()) {
						obj.options[j].selected=1;
						selezionato=1;
					}
				}
			}
		}
		
		//if (val) obj.value=val.toUpperCase();
		
		
		if ((selezionato==1) || (valori.length==1)){
			if (chiamante=='init'){
				var o=document.getElementById('anno');
				load_elenco(o,pratica);
			}
			else
				if (chiamante=='anno'){
				var o=document.getElementById('tabella');
				load_elenco(o,pratica);
			}
		}
	}
}

function load_elenco(obj,pratica){
	return;
	funzione="funz=crea_elenco&oggetto="+obj.name+"&pratica="+pratica;
	param="";
	switch (obj.name){
		case "init":
			/*var oggetti=new Array('anno','tabella','intervento','c1','c2','c3','c4','d1','d2');
			var tabelle=new Array('oneri.elenco_anno','oneri.elenco_funzione','oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2');
			var filtri=new Array(Array(),Array('anno'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'));*/
			var oggetti=new Array('anno');
			var tabelle=new Array('oneri.elenco_anno');
			var filtri=new Array(Array());
			
			break;
		case "anno":
			/*var oggetti=new Array('tabella','intervento','c1','c2','c3','c4','d1','d2');
			var tabelle=new Array('oneri.elenco_funzione','oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2');
			var filtri=new Array(Array('anno'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'),Array('tabella'));*/
			var oggetti=new Array('tabella');
			var tabelle=new Array('oneri.elenco_funzione');
			var filtri=new Array(Array());

			
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
	//alert(funzione+param);
	makeRequest("rpc.php",funzione+param,'set_elenco','GET');
	
}
function fillSelect(id,values){
    var options=['<option value="">Seleziona ====></option>'];
    var options = [];
    for(i=0;i<values.length;i++) {
        var sel=(typeof(values[i]['selected'])!='undefined' && values[i]['selected'])?('selected'):('');
        options.push('<option value="'+values[i]['value']+'" '+sel+'>'+values[i]['text']+'</option/>');
    }
    $('#'+id).html(options.join(''));
}
function reqOneriParams(obj){
    var pr = $('#pratica').val();
    var dataReq={
        'anno':{
            'field':obj.id,
            'data':{'anno' : $(obj).val()},
            'action':'oneri',
            'pratica':pr,
            'oggetti':['anno'],
            'tabelle':['oneri.elenco_funzione']
        },
        'tabella':{
            'field':obj.id,
            'data':{'anno':$('#anno').val(),'tabella':$(obj).val()},
            'action':'oneri',
            'pratica':pr,
            'oggetti':['intervento','c1','c2','c3','c4','d1','d2'],
            'tabelle':['oneri.elenco_interventi','oneri.elenco_c1','oneri.elenco_c2','oneri.elenco_c3','oneri.elenco_c4','oneri.elenco_d1','oneri.elenco_d2']
        },
        'init':{
            'field':'init',
            'action':'oneri',
            'pratica':pr,
            'oggetti':['anno'],
            'tabelle':['oneri.elenco_anno']
        }
    }
    $.ajax({
        url:'./services/xServer.php',
        data:dataReq[obj.id],
        type:'POST',
        dataType:'JSON',
        success:function(data, textStatus, jqXHR){
            if(typeof(data['child']=='object'))
                $.each(data['child'],function(k,v){
                    fillSelect(k,v);
                });
        },
        error:function(){}
    });
}
function init(pratica,modo){
	document.getElementById('perc').style.display='none'
	if (modo=='new'){
		var tmp=new Object();
		tmp.name="init";
		load_elenco(tmp,pratica);
	}
	/*obj=document.getElementById('anno');
	param="funz=crea_elenco&oggetto[]="+obj.name+"&elenco[]="+obj.name+"&tabella[]=oneri.elenco_anno&pratica="+pratica;
	makeRequest("rpc.php",param,'set_elenco','GET');*/

}

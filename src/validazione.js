/*
File JavaScript che gestisce la validazione di alcuni tipi di dato (Data-Numero-Valuta) di un form.
*/
function valida(){
	var list_dati=new Array();
	var flag=true;
	
	list_dati[0]=document.all.data;
	list_dati[1]=document.all.numero;
	list_dati[2]=document.all.valuta;
// Controllo quali tipi di dato sono presenti nel form	
	if (list_dati[0]==null){
		//alert("NESSUN CAMPO DATA");
		var len_date=0;
	}
	else {
		var len_date=list_dati[0].length;
		if (!len_date>0) len_date=1;
	}
	if (list_dati[1]==null){
		//alert("NESSUN CAMPO NUMERICO");
		len_num=0;
	}
	else {
		var len_num=list_dati[1].length;
		if (!len_num>0) len_num=1;
		//alert("Numero");
	}
	if (list_dati[2]==null){
		//alert("NESSUN CAMPO NUMERICO");
		len_val=0;
	}
	else {
		var len_val=list_dati[2].length;
		if (!len_val>0) len_val=1;
		//alert("Numero");
	}
// Validazione del dato	
	for(var i=1;i<=len_date;i++){
		if (len_date==1) {
			
			flag=flag && valida_dato(list_dati[0].value,"data");
			//if (!flag) list_dati[0].value="";
			break;
		}
		else {
			flag=flag && valida_dato(list_dati[0][i-1].value,"data");
			
			if (!flag) {
				//list_dati[0][i-1].value="";
				break;
			}
		}
	}
	for(i=1;i<=len_num;i++){
		
		if (len_num==1) {
			flag=flag && valida_dato(list_dati[1].value,"numero");
			break;
		}
		else {
			
			flag=flag && valida_dato(list_dati[1][i-1].value,"numero");
			if (!flag) break;
		}
	}
	for(i=1;i<=len_val;i++){
		
		if (len_val==1) {
			flag=flag && valida_dato(list_dati[2].value,"valuta");
			break;
		}
		else {
		
			flag=flag && valida_dato(list_dati[2][i-1].value,"valuta");
			if (!flag) break;
		}
	}
	
	//return flag;
	return true;
}

function valida_dato(dato,tipo)
{
	switch (tipo) {
		case "data" :
			var re=new RegExp("[^1234567890/.-]","g");
			if (dato.search(re)!=-1) {
				alert("Formato della data non corretto");
				return false;
			}
			return true;
			break;
		case "numero" :			
			var re=new RegExp("[^1234567890.,]","g");
			if (dato.search(re)==-1) return true;
			else {
				alert("Campo non Numerico maggiore di 0");
				return false;
			}
			break;
			case "valuta" :			
			var re=new RegExp("[^1234567890.,€ ]","g");
			if (dato.search(re)==-1) return true;
			else {
				alert("Formato valuta non corretto");
				return false;
			}
			break;			
		default :
		
	}
}


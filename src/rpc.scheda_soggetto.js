function calcola_cf(){
	var oggetti=Array('cognome','nome','comunato','datanato','sesso');
	var tipo=Array('testo','testo','testo','data','testo');
	var descrizione=Array('il cognome','il nome','il comune di nascita','la data di nascita','il sesso');
	var param="funz=codice_fiscale&oggetto=codfis";
	for(i=0;i<oggetti.length;i++){
		if(oggetti[i]=='datanato')
			var obj=document.getElementById('datanato');
		else
			var obj=document.getElementById(oggetti[i]);

		switch (obj.type){
			case "select-one":
				val=obj.options[obj.selectedIndex].text
				break;
			case "checkbox":
				if (obj.checked) val=obj.value;
				break;
			default:
				val=obj.value;
				break;
		}
		if (val.length==0) {
			alert('Inserire '+descrizione[i]);
			return;
		}
		else
			param+="&"+oggetti[i]+"="+val;
		
	}
	makeRequest("rpc.php",param,'set_codicefiscale','GET');
}
function set_codicefiscale(result){
	obj=document.getElementById(result[0][0]);
	obj.value=result[0][1];
}
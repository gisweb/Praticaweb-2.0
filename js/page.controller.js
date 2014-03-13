$(document).ready(function(){
   $.each($('.textbox-date'),function(k,v){
       $(v).datepicker({
            dateFormat:'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $(v).datepicker("option","defaultDate",$(v).attr('data-defaultDate'));
        $(v).datepicker("option","yearRange",$(v).attr('data-yearRange'));
   });
   $('[title]').tooltip({ tooltipClass: "textbox" });
   $.each($('[data-change]'),function(k,v){
       var fn = $(v).attr('data-change');
       $(v).bind('change', function(){
           eval(fn+'($(v));');
       });
   });
   $.each($('.stampe'),function(k,v){
       
       $(v).bind('click',function(event){
           event.preventDefault();
           var d=$(this).data();
           var url=d['url'];
           window.open(window.parent.url_documenti+url,'stampe');
       });
   });

    $("#btn_elenco_pratiche_indirizzi").button({
        icons: {
            primary: "ui-icon-gear"
        }
    }).bind('click',function(event){
       
		var baseURL='/elencopratiche_indirizzo.php'
		event.preventDefault();
		var via=$("#via").val();
		var civico=$("#civico").val();
		var interno=$("#interno").val();
		var url=baseURL + '?via='+encodeURIComponent(via)+'&civico='+encodeURIComponent(civico)+'&interno='+encodeURIComponent(interno);
		if (via){
			$('#waiting').dialog({
				width:400,
				height:200,
				title:'Messaggio'
			}).text('Attendere prego, caricamento dei dati in corso....');
			$('#result').html('<iframe frameBorder=0 style="width:99%;height:99%;OVERFLOW: visible;" marginWidth=0  marginHeight=0 src="' + url + '"></iframe>');
		}
		else
			$('<div>Selezionare una via</div>').dialog({
				width:300,
				height:200,
				title:'Attenzione'
			})
	});
        $(document).keypress(function(event){
            console.log($(document.activeElement));
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                if (!$(document.activeElement).is('textarea')){
                    event.preventDefault();
                    if ($("#azione-salva"))  $("#azione-salva").click(); 
                    else if ($("#print_btn")) $("#print_btn").click();
                    else if ($("#avvia-ricerca")) $("#avvia-ricerca").click();
                    else if ($("#print_btn")) $("#print_btn").click();
                    
                }
            }

        });
        
});


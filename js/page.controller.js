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
   $(".textbox").bind("keyup",function(event){
            if(event.keyCode == 13){
                $("#azione-salva").click();
            }
        });
});


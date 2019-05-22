$(document).ready(function(){
    var mode = $('#mode').val();
    if (mode=='view'){
        $.each($('a[data-selector="link"]'),function(k,v){
            var d = $(v).data();
            var url=sprintf('%(dir)s%(file)s',d); 
            $(v).bind('click',function(event){
                event.preventDefault();
                /*$("#normativa-div").dialog({modal:true,width:800,height:600});
                $('#norma-frame').attr('url',url);*/
                window.open(url,'normativa','',true);
            });
        });
    }
});

$(document).ready(function(){
    var mode = $('#mode').val();
    if (mode=='view'){
        $.each($('a[data-selector="link"]'),function(k,v){
            var d = $(v).data();
            var url=sprintf('%(dir)s%(file)s',d); 
            $(v).bind('click',function(event){
                event.preventDefault();
                
                window.open(url);
                
            });
        });
    }
});

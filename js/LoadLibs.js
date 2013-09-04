var libs = ['jquery-1.9.1','jquery-ui-1.10.2.min','jquery.ui.datepicker-it','jquery.dataTables.min','dataTables.date.order','window','praticaweb','page.controller'];
var libsSrc = ['window','iframe','x_core','http_request'];
var libcss = ['praticaweb/jquery-ui-1.9.1.custom','styles','TableTools','TableTools_JUI','demo_page','demo_table_jui','tabella_v','menu'];
var libcssprt = ['styles_print']
//document.write('<meta http-equiv="X-UA-Compatible" content="IE=edge" />');
for (i in libcss) document.write('<LINK media="screen" href="css/'+libcss[i]+'.css" type="text/css" rel="stylesheet"></SCRIPT>');
for (i in libcssprt) document.write('<LINK media="print" href="css/'+libcssprt[i]+'.css" type="text/css" rel="stylesheet"></SCRIPT>');
for (i in libs) document.write('<SCRIPT language="javascript" src="js/'+libs[i]+'.js" type="text/javascript"></SCRIPT>');
//for (i in libsSrc) document.write('<SCRIPT language="javascript" src="/dbmaciste/src/'+libsSrc[i]+'.js" type="text/javascript"></SCRIPT>');

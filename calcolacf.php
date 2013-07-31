<?include_once("login.php");?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

</head>
<body >
<script>

function loaddata(cf) 
{
	parent.document.getElementById('codfis').value=cf;
	parent.document.getElementById("dwindow").style.display="none"
	parent.document.getElementById("cframe").src=""
}
</script>

<FONT Verdana, Geneva, Arial, sans-serif size="-1">
<?
require_once("calcolacodicefiscale.php");

$cognome=$_GET['cognome'];
$nome=$_GET['nome'];
$sesso=$_GET['sesso'];
$comune=$_GET['comune'];
$datanascita=$_GET['datanascita'];
$r=new risultato;
$r=calcolacodicefiscale($cognome,$nome,$sesso,$comune,$datanascita);
if (sizeof($r->errori)){
	echo "Si sono verificati i seguenti errori:<br>";
	reset ($r->errori);
    while (list ($key, $val) = each ($r->errori)) {
        echo ($key+1)."- ".$val."<br>";
    }
} else {
    echo $r->codicefiscale;
}



?>
<input name="azione" type="button" value="OK" onClick="loaddata(<?echo "'$r->codicefiscale'"?>)">
</FONT>
</body>
</html>
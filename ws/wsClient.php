<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$libDir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."nusoap".DIRECTORY_SEPARATOR;
//require_once $libDir."nusoap.php";
//require_once $libDir."nusoapmime.php";
$url = "http://10.95.10.42/wsPECMail.asmx?WSDL";
require_once "/data/spezia/pe/praticaweb/lib/wsclient.mail.class.php";

$client = new SoapClient($url, array("trace" => 1, "exception" => 0)); 
//$client = new nusoap_client_mime($this->wsUrl,'wsdl');
$user = "gisweb_auth";
$raggruppamento = "1562321026";
$pratica = "556235";
$objId = sprintf("%s_%s",$pratica,$raggruppamento);

$ids = Array("555596_1559289827","555698_1558517984","551040_1549021639","555663_1559126638","550968_1549270281","555750_1559303960","555835_1559559988","551734_1553252140","551250_1552033619","551211_1550314821","551207_1550659877","551734_1553250237","551790_1553679157","550970_1554455573","551759_1554451047","550970_1554455573","550970_1554455573","550970_1554455573","551679_1555664286","552159_1556537543","551951_1557730280","552075_1555496015","551831_1556881070","555835_1559898486","552039_1558173936","552121_1558434187","555575_1558175456","555968_1560162863","555876_1560165836","556011_1560337674","556007_1560338879","555651_1558441768","551831_1557830315","556007_1560338879","555668_1558515492","556341_1562831380","556344_1562663362","556223_1561548004","556155_1561795810","555543_1561721058","552042_1561799724","556282_1561982048","556282_1561982433","556255_1561800737","556341_1562831380","556294_1562149376","555972_1562065872","556097_1562837148","555651_1562913961","556355_1562750467","556272_1562742654","556344_1562663362","556235_1562321026","556340_1562832795","556332_1562315154","556273_1562588671","552042_1562323841","556344_1562663362","556344_1562663362","556344_1562663362","548358_1562761855","548358_1562761802","556172_1562916371","556172_1562916371","556341_1562831380","556272_1562742654","556272_1562742654","556272_1562742654","556272_1562742654","556272_1562742654","556325_1562744166","556028_1562837085","556294_1562749873","556361_1562839890","550861_1563271324","556318_1562752427","556318_1562752427","556318_1562752427","556318_1562752427","556318_1562752427","556273_1562757855","556145_1562837250","548358_1562761802","556294_1562759657","556318_1562752427","556273_1562757986","556341_1562831380","556318_1562752427","556318_1562752427","556318_1562752427","556318_1562752427","556318_1562752427","556005_1562824099","552608_1562824652","555832_1562825923","551829_1562827704","556058_1562828798","556127_1562837200","548358_1562761855","556172_1562916371","556172_1562916371","556341_1562831380","556341_1562831380","556110_1563268310","550861_1563271324","556341_1562831380","556341_1562831380","556341_1562831380","555969_1562921595","556431_1563177720","556431_1563177720","556431_1563177720","556431_1563177720","556431_1563177720","556431_1563177720","556431_1563177720","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556305_1563265769","556339_1563280459","556389_1562918473","556489_1563360735","556005_1563433310");

foreach( $ids as $objId){
    print "ObjID : $objId\n";
    $res = wsClientMail::getInfoPEC($objId);
    print_r($res);
}
?>
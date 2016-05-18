<?php
include "login.php";
require_once "html_pdf.class.php";
$id=$_REQUEST["id_doc"];
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$sql="SELECT testohtml FROM stp.stampe WHERE id=$id";
$db->sql_query($sql);
$testo=$db->sql_fetchfield("testohtml");
$stp=new stampe_pdf($testo);
$stp->crea_pdf();
print_debug($stp,null,"pdf");
$stp->open_pdf();
?>
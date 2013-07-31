<html>
<head>
   <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body style="background-color:#FFFFFF">
<?$menu=0;include ("./inc/inc.page_header.php")?>


<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
  <TBODY>
  
  <TR>
	<td class="testo_login">
		
		Accesso consentito agli utenti autorizzati.
		
	</td>
	<TD colspan="3">
		<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" class="riquadro">
		  <table width="100%" border="0" align="center" cellpadding="4" cellspacing="0">
		      <td width="22%" class="label">Utente:</td>
		      <td width="78%"><input name="username" type="text" id="username"></td>
		    </tr>
		    <tr>
		      <td class="label">Password:</td> 
		      <td><input name="password" type="password" id="password"></td>
		    </tr>
		    <tr>
				<td></td>
		      <td align="right">
				<input type="submit" name="entra" value="Entra" style="width:80" >
			  </td>
		    </tr>
		  </table>
		</form>
	</TD>
  </TR>
  <TR><TD style="text-align:right; background-color:#728bb8; border-bottom:6px solid #415578; padding:6 6 1 6px" colspan="5"><a href="http://www.gisweb.it" target="_blank"><img src="images/logoblu.png" border="0"></a></td></TR>
  </TBODY> 
</TABLE>
<!--<div style="background-image:url(images/sfondo.png); background-repeat:repeat-x; width:100%; height:8"></div>-->
</body>
</html>

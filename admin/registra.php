<?
//include "./config/config.php";
//print_r($_POST);

if ($modo=="new" or $modo=="edit"){
   $nome = $arraydati['nome'] = $_POST['nome'];
   //$cognome = $arraydati['cognome'] =  $_POST['cognome'];
   $username =  $arraydati['username'] = $_POST['username'];
   $info = $arraydati['info'] =  $_POST['info'];
   $pwd =$arraydati['pwd']=$_POST['pwd'];
   $pwd1=$_POST['pwd1'];
   $livello_utente = $_POST['permessi'];
   $attivato=$arraydati['attivato']=$_POST['attivato'];
   $nome = stripslashes(htmlentities($nome));
   //$cognome = stripslashes(htmlentities($cognome));
   $username = stripslashes(htmlentities($username));
   $info=htmlentities(addslashes($info),ENT_QUOTES);
   echo $livello_utente."<br>";
   // Trasformo i permessi da stringa di testo in numeri
   switch ($livello_utente) {
      case 0 :
         $liv_utente="Amministratore del Sistema";
         break;
      case 1 :
         $liv_utente="Amministratore Locale";
         break;
      case 2 :
         $liv_utente="Utente";
         break;
   }
   $arraydati['permessi']=$liv_utente;
   (($attivato=="on")?($attivato=1):($attivato=0));
   // Controllo che le password siano uguali
   if ($pwd!==$pwd1){ 
      $errors["pwd"]="Inserire la medesima password";
      $errors["pwd1"]="Inserire la medesima password";
      $is_save_ok=0;
   }
   
   //Controllo che i campi siano riempiti tutti
   if((!$nome) || (!$username) || (!$pwd) || $livello_utente<$_SESSION["PERMESSI"]){
         if(!$nome){
                  $errors["nome"]="Il campo &egrave; obbligatorio";
         }/*
         if(!$cognome){
                  $errors["cognome"]="Il campo &egrave; obbligatorio";
         }*/
         if(!$username){
                  $errors["username"]="Il campo &egrave; obbligatorio";
         }
         if(!$pwd){
                  $errors["pwd"]="Il campo &egrave; obbligatorio";
         }
         if(!$pwd1){
                  $errors["pwd1"]="Il campo &egrave; obbligatorio";
         }
	 if ($livello_utente<$_SESSION["PERMESSI"]){
                  $errors["permessi"]="Non si dispone di permessi adeguati";
         }
         $is_save_ok=0;
   }
   // Controllo che i formati dei vari dati siano corretti
     
   //Controllo che l'utente non sia gia registrato
   $db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
   if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
   $sql="SELECT username FROM admin.users WHERE username='$username'";
   $db->sql_query($sql);
   if ($debug) echo "$sql<br>";
   $ris = $db->sql_fetchrowset();
   $nrec=$db->sql_numrows();
   
   if($nrec > 0 and !$_POST['id']>0){
         $errors["username"]="Questo nome utente &egrave; gi&agrave; in uso.";
         $is_save_ok=0;
   }
   
   // Inserisco i dati nel DB
   if (!($is_save_ok===0)){
      $db_password = md5($pwd);
      // Eseguo Query di inserimento
      $info2 = htmlspecialchars($info);
      if (!$_POST['id']>0) $sql="INSERT INTO admin.users(nome, username,  info, data_creazione,pwd,enc_pwd,permessi,attivato) VALUES('$nome', '$username', '$info2', now(),'$pwd','$db_password',$livello_utente,1)";
      elseif ($livello_utente>$_SESSION["PERMESSI"])
      	$sql="UPDATE admin.users SET nome='$nome', username='$username',  info='$info2', data_modifica=now(),pwd='$pwd',enc_pwd='$db_password',permessi=$livello_utente,attivato=$attivato WHERE userid=$id;";
      if(!$db->sql_query($sql)){
        	 echo 'Errore nella salvataggio dei dati dell\'utente.';
         	$is_save_ok=0;
         	//echo $sql;
      	}
	if ($debug) echo "$sql<br>";
   }
}
elseif ($modo=="delete"){
   $db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
   if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
   
   foreach($_POST as $key=>$val){
   	if ($val=="id" and $key!=$_SESSION["USER_ID"]){
		$sql="SELECT permessi FROM admin.users WHERE userid=$key;";
   		$db->sql_query($sql);
		$livello_utente=$db->sql_fetchfield("permessi");
	 	if ($livello_utente>=$_SESSION["PERMESSI"]) $cond.=" userid=$key OR";
	}
   }
   if ($cond){
      $cond=substr($cond,0,strlen($cond)-3);
      $sql="DELETE FROM admin.users WHERE $cond;";
      $db->sql_query($sql);
      if ($debug) echo "$sql<br>";
   }
}
?>
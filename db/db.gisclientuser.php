<?php
$sk="geoweb";
$db1 = new sql_db(DB_HOST,DB_USER,DB_PWD,'gisclient', false);
if(!$db1->db_connect_id)  die( "Impossibile connettersi al database");
//ACQUISISCO ID DEL PROGETTO
$sql="SELECT project_id FROM $sk.project WHERE project_name='".GC_PROJECT."';";
if(!$db1->sql_query($sql))
	print_array($db1->error_message);
$projectId=$db1->sql_fetchfield('project_id');
if($role==2){
	//VERIFICO SE ESISTE L0 USERGROUP praticaweb
	$sql="SELECT usergroup_id FROM $sk.usergroup WHERE project_id=$projectId and usergroup='".GC_ROLE."'";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$usergroupId=$db1->sql_fetchfield('usergroup_id');
	if(!$usergroupId){
		$sql="select $sk.new_pkey('usergroup','usergroup_id') as newid;";
		if(!$db1->sql_query($sql))
			print_array($db1->error_message);
		$newId=$db1->sql_fetchfield('newid');
		$sql="INSERT INTO $sk.usergroup(usergroup_id,project_id,usergroup,description) VALUES($newId,$projectId,'praticaweb','Utenti delle Pratiche Edilizie')";
		if(!$db1->sql_query($sql))
			print_array($db1->error_message);
		$usergroupId=$newId;
	}
	$sql="select $sk.new_pkey('user','user_id',1000) as newid;";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$newUserId=$db1->sql_fetchfield('newid');
	$sql="INSERT INTO $sk.user(user_id,username,pwd) VALUES($newUserId,'$username','$pwd')";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$sql="select $sk.new_pkey('user_project','user_project_id') as newid;";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$newId=$db1->sql_fetchfield('newid');
	$sql="INSERT INTO $sk.user_project(user_project_id,user_id,project_id,usergroup_id) VALUES($newId,$newUserId,$projectId,$usergroupId)";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
}
else{
	$sql="select $sk.new_pkey('user_admin','user_id',100) as newid;";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$newUserId=$db1->sql_fetchfield('newid');
	$sql="INSERT INTO $sk.user_admin(user_id,username,pwd,admintype_id) VALUES($newUserId,'$username','$pwd',2)";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$sql="select $sk.new_pkey('user_project','user_project_id') as newid;";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
	$newId=$db1->sql_fetchfield('newid');
	$sql="INSERT INTO $sk.user_project(user_project_id,user_id,project_id,usergroup_id) VALUES($newId,$newUserId,$projectId,2)";
	if(!$db1->sql_query($sql))
		print_array($db1->error_message);
}

?>
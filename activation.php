<?php

error_reporting(E_ALL);
ini_set('display_errors','1');

if($_GET['id']!="")
{
	//Connecting to The Database
	include_once"scripts/connect_to_mysql.php";
	
	$id=$_GET['id'];
	$hash=$_GET['sequence'];
	
	$id=mysql_real_escape_string($id);
	$id=eregi_replace("`","",$id);
	
	$hash=mysql_real_escape_string($hash);
	$hash=eregi_replace("`","",$hash);
	
	//Updating The Database
	
	$sql=mysql_query("UPDATE myMembers SET email_activated='1' WHERE id='$id' AND password='$hash'");
	
	//Checking updation
	
	$sql_check_double=mysql_query("SELECT * FROM myMembers WHERE id='$id' AND email_activated='1'");
	$sql_check=mysql_num_rows($sql_check_double);
	if($sql_check==0)
	{  
		$msgToUser="Your account cannot be activated<br/>
		Please Contact the admin";
		include"msgToUser.php";
		exit();
	}
	else
	{
		$msgToUser="Your account has been activated...You can login any time now";
		include"msgToUser.php";
		exit();
	}
	
echo"Essential data from the activation URL is missing! Close your browser, go back to your email inbox, and please use the full URL supplied in the activation link which is sent you.<br />
<br />
admin@clubnitt.com";	
	} 
?>
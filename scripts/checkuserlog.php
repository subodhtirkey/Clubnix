<?php

//////////////////////////////statrting the session////////////////////
session_start();
error_reporting(E_ALL);
ini_set('display_errors','1');

//////////////////////////connecting to the mysql database
include_once"scripts/connect_to_mysql.php";

$dyn_www=$_SERVER['HTTP_HOST'];
$toplinks=""; //initialing the variable to be displayed

//IF THE SESSION VARIABLE AND THE COOKIE VARIBLE ARE NOT SET

if(!isset($_SESSION['idx'])){
	if(!isset($_COOKIE['idCookie'])){
	
	$toplinks='<table id="menu" cellpadding="0" cellspacing="0" align="right">
    <tr>
	<td class="m7"><a href="http://' .$dyn_www. '/index.php">HOME</a></td>
    <td class="m2"><a href="http://' .$dyn_www. '/register.php">REGISTER</a></td>
    <td class="m3"><a href="http://' .$dyn_www. '/login.php">LOGIN</a></td>
    </tr>
    </table>';
	}
}

//IF SESSION IS SET FOR USER WITHOUT COOKIES REMEMBER ME FEATURE


if(isset($_SESSION['idx'])){
	$decryptedID=base64_decode($_SESSION['idx']);
	$id_array = explode("p3h9xfn8sq03hs2234", $decryptedID);
	$toplinks_id=$id_array[1];
	$toplinks_firstname=$_SESSION['firstname'];
	$toplinks_firstname=substr('' .$toplinks_firstname.'',0,15);
	
	//checking if the user has any message in his inbox
	
	$sql_pm_check=mysql_query("SELECT id FROM messages WHERE to_id='$toplinks_id' AND opened='0' ");
	$num_new_message=mysql_num_rows($sql_pm_check);
	$letter='<a href="http://' .$dyn_www. '/inbox.php" style="text-decoration:none;">' .$num_new_message. '</a>';
	
	//READY THE OUTPUT FOR THIS LOGIN USER
	
	$toplinks='<table id="menu" cellpadding="0" cellspacing="0" align="right">
    <tr>
	<td class="m1"><a href="http://' .$dyn_www. '/profile.php?id=' .$toplinks_id. '">PROFILE</a></td>
    <td class="m2"><a href="http://' .$dyn_www. '/edit_profile.php">ACCOUNT</a></td>
	<td class="m3"><a href="http://' .$dyn_www. '/inbox.php">INBOX</a></td>
	<td class="m4"><a href="http://' .$dyn_www. '/outbox.php">OUTBOX</a></td>
	<td class="m5"><a href="http://' .$dyn_www. '/logout.php">LOGOUT</a></td>
    </tr>
    </table>';
}

///IF ID COOKIE IS SET BUT NO SESSION ID IS SET YET WE SET IT BELOW AND UPDATE THE STUFF
else if(isset($_COOKIE['idCookie']))
{
	$decryptedID=base64_decode($_COOKIE['idCookie']);
	$id_array = explode("nm2c0c4y3dn3727553", $decryptedID);
	$userID=$id_array[1];
	$userPASS=$_COOKIE['passCookie'];
	
	//getting the user firstname to set into session variable
	
	$sql_uname=mysql_query("SELECT firstname,email FROM myMembers WHERE id='$userID' AND password='$userPASS' LIMIT 1");
	
	$numRows=mysql_num_rows($sql_uname);
	if($numRows==0)
	{
		echo 'Something appears wrong with your stored log in credientials <a href="login.php">Log in again here please</a>';
		exit();
	}	
	while($row=mysql_fetch_array($sql_uname))
	{
		$firstname=$row['firstname'];
		$useremail=$row['email'];
		
	}
	
	//now add the value we need to the session variable
	
	$_SESSION['id']=$userID;
	$_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$userID");
	$_SESSION['firstname']=$firstname;
	$_SESSION['useremail']=$useremail;
	$_SESSION['userpass']=$userPASS;
	
	$toplinks_id=$userID;
	$toplinks_firstname=$firstname;
	$toplinks_firstname=substr('' .$toplinks_firstname. '',0,15);
	
	//updatong the last login date field
	
	mysql_query("UPDATE myMembers SET last_log_date=now() WHERE id='$toplinks_id'");
	
	
	//checking if the user has any message in his inbox
	
	$sql_pm_check=mysql_query("SELECT id FROM messages WHERE to_id='$toplinks_id' AND opened='0' ");
	$num_new_message=mysql_num_rows($sql_pm_check);
	$letter='<a href="http://' .$dyn_www. '/inbox.php" style="text-decoration:none;">' .$num_new_message. '</a>';
	
	//READY THE OUTPUT FOR THIS LOGIN USER
	
	$toplinks='<table id="menu" cellpadding="0" cellspacing="0" align="right">
    <tr>
	<td class="m1"><a href="http://' .$dyn_www. '/profile.php?id=' .$toplinks_id. '">PROFILE</a></td>
    <td class="m2"><a href="http://' .$dyn_www. '/edit_profile.php">ACCOUNT</a></td>
	<td class="m3"><a href="http://' .$dyn_www. '/inbox.php">INBOX</a></td>
	<td class="m4"><a href="http://' .$dyn_www. '/outbox.php">OUTBOX</a></td>
	<td class="m5"><a href="http://' .$dyn_www. '/logout.php">LOGOUT</a></td>
    </tr>
    </table>';
	
	

}


?>
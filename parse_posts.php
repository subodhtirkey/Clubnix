<?php
include_once"scripts/checkuserlog.php";

//Checking the HTTP-REFERER FOR LIGHT level security

/*$ref=parse_url($_SERVER['HTTP-REFERER']);
$host=$ref["host"];
echo $host;
if($host != "www.clubnitt.org")
{
	echo "Dont try that.......";
	exit();
}*/

//making sure that all the user varibles are set.............

if(!isset($_SESSION['id']) || !isset($_SESSION['firstname']) || !isset($_SESSION['useremail']))
{
	echo 'Your session has timed out';
	exit();
}

//making sure that all the form variables are present to proceed.......

if(!isset($_POST['post_type']) || !isset($_POST['post_body']) || !isset($_POST['fsID']) || !isset($_POST['fsTitle']) || !isset($_POST['uid']) || !isset($_POST['upass']))
{
	echo 'Error: Important variables from the form are missing';
	exit();	
}

//filtering all the variables.............

$post_type=$_POST['post_type'];
$post_body=$_POST['post_body'];
$post_body=nl2br(htmlspecialchars($post_body));
$post_body=mysql_real_escape_string($post_body);

$forum_section_id=preg_replace('#[^0-9]#i','',$_POST['fsID']);
$forum_section_title=preg_replace('#[^A-Za-z 0-9]#i','',$_POST['fsTitle']);

$member_id=preg_replace('#[^0-9]#i','',$_POST['uid']);
$post_author=preg_replace('#[^A-Za-z0-9 ]#i','',$_SESSION['firstname']);
$member_password=mysql_real_escape_string($_POST['upass']);


//Making sure that posted variables match the sessions variable
if($_SESSION['id'] !=$member_id || $_SESSION['userpass'] != $member_password )
{
	echo 'Your id and your password is mismatch';
	exit();
}


//checking the database to make sure that the user's id , password , email and name are their in the database
$u_id=$member_id;
$u_name=mysql_real_escape_string($_SESSION['firstname']);
$u_email=mysql_real_escape_string($_SESSION['useremail']);
$u_pass=mysql_real_escape_string($_SESSION['userpass']);

$sql=mysql_query("SELECT * FROM myMembers WHERE id='$u_id' AND firstname='$u_name' AND email='$u_email' AND password='$u_pass'");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	echo 'ERROR: you do not exist in the system';
	exit();
}

//Checking the database to be sure that the forum section exist
$sql=mysql_query("SELECT * FROM mydep_sections WHERE id='$forum_section_id' AND title='$forum_section_title'");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	echo 'ERROR: That forum section does not exist';
	exit();
}

//Preventing the members to post more than 30 times a day
$sql1=mysql_query("SELECT id FROM mydep_posts WHERE post_author_id='$member_id' AND DATE(date_time)=DATE(NOW()) LIMIT 32");
$numRows1=mysql_num_rows($sql1);
if($numRows1>30)
{
	echo"Error: You can post only 30 times per day. You maximum has been reached";
	exit();
}

//Adding the post into the database depending upon the postt_type value (a or b)

//if the post_type is "a"

if($post_type=='a')
{
	$post_title=preg_replace('#[^A-Za-z 0-9]#i','',$_POST['post_title']);
	if($post_title=="")
	{
		echo 'The topic title is missing';
		exit();
	}
	if(strlen($post_title)<10)
	{
		echo 'Your topic title is less than 10 characters';
		exit();
	}
	$sql=mysql_query("INSERT INTO mydep_posts(post_author,post_author_id,date_time,type,section_title,section_id,thread_title,post_body) VALUES('$post_author','$member_id',now(),'a','$forum_section_title','$forum_section_id','$post_title','$post_body')") or die(mysql_error());
	$this_id=mysql_insert_id();
	
	
	
	
	header("location:view_thread.php?id=$this_id");
	exit();
}


//Only if the post_type is "b" 

if($post_type=='b')
{
	$this_id=preg_replace('#[^0-9]#i','',$_POST['tid']);
	if($this_id=="")
	{
		echo'The thread id is missing';
		exit();
	}
	
$sql1=mysql_query("INSERT INTO mydep_posts(post_author, post_author_id, otid, date_time, type, post_body) VALUES('$post_author','$member_id','$this_id', now(), 'b' , '$post_body')") or die(mysql_error());



$post_body=stripslashes($post_body);
echo $post_body;
}

?>
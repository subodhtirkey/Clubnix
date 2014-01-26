<?php
session_start();
error_reporting('E_ALL');
ini_set('display_errors', '1');

require"scripts/connect_to_mysql.php";

include_once"class_files/agoTimeFormat.php";
include_once"class_files/autoMakeLinks.php";
//creating the objects_____________________________
$activeLinkObject=new autoActiveLink;
$myObject= new convertToAgo;

//////////////////////////////////////////////////////////////////////////////////////////
if(!isset($_REQUEST['m1']) || !isset($_REQUEST['m2']))
{
	//echo 'Connection Not Set';
	//exit();
}


	$mem1=mysql_real_escape_string($_GET['m1']);
 	$mem2=mysql_real_escape_string($_GET['m2']);
	


//Querying the database to get all the conversation between two students

$sql1=mysql_query("SELECT * FROM `messages` WHERE (to_id='$mem1' OR from_id='$mem1'
) AND (to_id='$mem2' OR from_id='$mem2') ORDER BY time_sent DESC LIMIT 4"); 

$numRows=mysql_num_rows($sql1);
if($numRows<1)
echo("nothing");
else

while($raw=mysql_fetch_array($sql1))
{
	$mesid=$raw['id'];
	$m1=$raw['to_id'];
	$m2=$raw['from_id'];
	
	$time1=$raw['time_sent'];
	$convertedTime = ($myObject -> convert_datetime($time1));
	$time = ($myObject -> makeAgo($convertedTime));


	
	
	$message=$raw['message'];
	$message=nl2br(htmlspecialchars($message));
	//mechanism to display pic
	
	$check_pic="members/$m2/pic.jpg";
	$default_pic="members/0/pic.jpg";
	if(file_exists($check_pic))
	{
		$user_pic=' <img src="' .$check_pic. '"/ width="30">';	
	}
	else
	{
			$user_pic=' <img src="' .$default_pic. '" width="30">';	
	}
	
	//for dispalying the location information
	
//running inner sql
$sql2=mysql_query("SELECT firstname FROM myMembers WHERE id='$m1' LIMIT 1");
while($row1=mysql_fetch_array($sql2))
{
	$m1n=$row1['firstname'];
}

$sql2=mysql_query("SELECT firstname FROM myMembers WHERE id='$m2' LIMIT 1");
while($row1=mysql_fetch_array($sql2))
{
	$m2n=$row1['firstname'];
}	
	
$display.='
<table width="100%" align="center" bgcolor=""><tr><td align="center" width="20%" valign="top">' .$user_pic. '</td>
<td align="left" width="80%">
' .$m2n. ': 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=" font-size:14px">'.$time.'</span>
<br/><br/>
' .$message. '</td></tr></table>
<hr/>';
	
}

echo $display;

?>
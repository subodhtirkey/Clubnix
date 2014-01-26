<?php include_once"scripts/checkuserlog.php"; ?>
<?php
$display="";
//Checking  wheter session variable is set or not
if(!isset($_SESSION['id']))
{
	$msgToUser='You must <a href="login.php">Login</a> OR <a href="register.php"> Register</a>';
	include_once"msgToUser.php";
	exit();
}

//Checking whether posted variables are set or not
if(!isset($_POST['mem1_id']) || !isset($_POST['mem2_id']) || !isset($_POST['mem2_name']))
{
echo 'Important variables are missing';
exit();	
}


//Finally assigning the posted variables into the local php variables

$mem1=mysql_real_escape_string($_POST['mem1_id']);


$mem2=mysql_real_escape_string($_POST['mem2_id']);



$m2name=mysql_real_escape_string($_POST['mem2_name']);

//Querying the database to get all the conversation between two students

$sql1=mysql_query("SELECT * FROM `messages` WHERE (to_id='$mem1' OR from_id='$mem1'
) AND (to_id='$mem2' OR from_id='$mem2') ORDER BY time_sent DESC"); 

$numRows=mysql_num_rows($sql1);
if($numRows<1)
echo("no data");
else
while($raw=mysql_fetch_array($sql1))
{
	$mesid=$raw['id'];
	$m1=$raw['to_id'];
	$m2=$raw['from_id'];
	$time=$raw['time_sent'];
	$message=$raw['message'];
	
	//mechanism to display pic
	
	$check_pic="members/$m2/pic.jpg";
	$default_pic="members/0/pic.jpg";
	if(file_exists($check_pic))
	{
		$user_pic=' <img src="' .$check_pic. '"/ width="50">';	
	}
	else
	{
			$user_pic=' <img src="' .$default_pic. '" width="50">';	
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
<table width="100%" align="center" bgcolor="#0066CC"><tr><td align="center" width="20%"><a href="profile.php?id=' .$m2. '">' .$user_pic. '</td>
<td align="left" width="80%">
<a href="profile.php?id=' .$m2. '" id="link">
' .$m2n. '</a> said to 
<a href="profile.php?id=' .$m1. '" id="link">
' .$m1n. '</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=" font-size:14px">'.$time.'</span>
<br/><br/>
' .$message. '</td></tr></table>
<hr style="margin-right:20px; margin-left:20px" />';
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CONVERSATION</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
#link{
	text-decoration:none; 
	color:#999; 
	
}
#link:hover{
	text-decoration:none; 
	color:#FFFFFF; 
}

.button2{
	background-color:#060;
	border:0;
	padding:5px;
	box-shadow:#030;
	border-color:#003300;
	font-weight:bold;
	color:#FFFFFF;
	cursor:pointer;
}
.button2:hover{
	background-color:#00CC00;
	border:0;
	padding:5px;
	border-color:#003300;
	font-weight:bold;
	color:#000000;
	cursor:pointer;
}
</style>
</head>

<body>
<table width="100%" border="0" cellpadding="0" id="header">
  <tr>
    <td width="79%" align="left" style="padding-left:15px;"><span id="logo">clubnitt.org</span></td>
    <td width="39%">
   <?php echo $toplinks; ?>
    </td>
  </tr>
</table>

<table width="70%" align="center">
<tr>
<td>
<h3 align="center" style="color:#FFFFFF">Your Conversations</h3>
</td>
</tr>
</table>
<table width="70%" align="center" >
<tr>
<td>
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="profile.php" id="link">Profile </a> &larr; <a href="conversation.php" id="link">Conversations</a> &larr; Conversation List</p>
</td></tr></table>
<table width="70%" align="center" bgcolor="#0066CC" >
<tr>
<td>

<?php echo $display; ?>
</td>
</tr>
</table>
</body>
</html>
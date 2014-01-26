<?php include_once"scripts/checkuserlog.php"; ?>
<?php
$display="";
if(!isset($_SESSION['id']))
{
	$msgToUser='You must <a href="login.php">Login</a> OR <a href="register.php">Register</a>';
	include_once"msgToUser.php";
	exit();
}
$display="";
$myid=$_SESSION['id'];
$sql=mysql_query("SELECT * FROM `messages` WHERE (to_id='$myid' or from_id='$myid') GROUP BY (to_id +from_id) ORDER BY time_sent DESC");
$numrows=mysql_num_rows($sql);
if($numrows<1)
{
	$display='
	<table width="100%" align="center" bgcolor="#0066CC"><tr>
	You dont have any conversation yet
	</td></tr></table>';
}
else
{

while($row=mysql_fetch_array($sql))	
{
$msqid=$row['id'];
$mem1=$row['to_id'];
$mem2=$row['from_id'];
$time=strftime("%b %d, %Y", strtotime($row['time_sent']));
$message=$row['message'];
//mechanism to display pic
	
	$check_pic="members/$mem2/pic.jpg";
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
$sql2=mysql_query("SELECT firstname FROM myMembers WHERE id='$mem1' LIMIT 1");
while($row1=mysql_fetch_array($sql2))
{
	$m1name=$row1['firstname'];
}

$sql2=mysql_query("SELECT firstname FROM myMembers WHERE id='$mem2' LIMIT 1");
while($row1=mysql_fetch_array($sql2))
{
	$m2name=$row1['firstname'];
}


$display.='
<table width="100%" align="center" bgcolor="#0066CC"><tr><td align="center" width="20%"><a href="profile.php?id=' .$mem2. '">' .$user_pic. '</td>
<td align="left" width="80%">
<a href="profile.php?id=' .$mem2. '" id="link">
' .$m2name. '</a> said to 
<a href="profile.php?id=' .$mem1. '" id="link">
' .$m1name. '</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=" font-size:14px">'.$time.'</span>
<br/><br/>
' .$message. '</td></tr>
<tr><td align="center" width="20%">&nbsp;</td>
<td align="left" width="80%">
<form action="conv.php" method="post" enctype="multipart/form-data">
<input type="submit" name="btn" value=" View Conversations " class="button2" />
<input type="hidden" name="mem1_id" id="mem1_id" value="' .$mem1. '" />
<input type="hidden" name="mem2_id" id="mem2_id" value="' .$mem2.'" />
<input type="hidden" name="mem2_name" id="mem2_name" value="' .$m2name. '" />
</form>
</td></tr>
</table>
<hr style="margin-right:20px; margin-left:20px" />';		
	
}//closing while	
}//closing else
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
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="profile.php" id="link">Profile </a> &larr; Conversations</p>
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
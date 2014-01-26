<?php include_once"scripts/checkuserlog.php"?>
<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
//initializing variables

$errorMsg="";
$email="";
$pass="";
$remember="";
if(isset($_POST['email'])){
	$email=$_POST['email'];
	$pass=$_POST['password'];
	if(isset($_POST['remember'])){
		$remember=$_POST['remember'];
		}
	$email=stripslashes($email);
	$email=strip_tags($email);
	$pass=stripslashes($pass);
	$pass=strip_tags($pass);
	
	//Error Handling Conditional Check If
	if((!$email)||(!$pass)){
		$errorMsg="ERROR: You Need To Fill In Both The Fields..";
		}
	else{
		include "scripts/connect_to_mysql.php";
		
		$email=mysql_real_escape_string($email);
		
		//hashing the paswsword.......
		
		function enc($string)
		{
			$salt="@x2p";
			$hash=sha1(md5($salt.$string).md5($string).sha1(md5(md5($string))));
			return $hash;
		}
		
		$pass=enc($pass);
		//Make The Sql Query
		$sql=mysql_query("SELECT * FROM myMembers WHERE email='$email' AND password='$pass' AND email_activated='1'");
		$login_check=mysql_num_rows($sql);
		if($login_check>0){
			while($row=mysql_fetch_array($sql)){
				$id=$row['id'];
				$_SESSION['id']=$id;
				//creating the idx session
				$_SESSION['idx']=base64_encode("g4p3h9xfn8sq03hs2234$id");
				//creating the session variable for firstname
				
				$firstname=$row['firstname'];
				$_SESSION['firstname']=$firstname;
				
				//Creating the session variable for email
				$useremail=$row['email'];
				$_SESSION['useremail']=$useremail;
				
				//Creating tthe session variable for password
				
				$userpass=$row['password'];
				$_SESSION['userpass']=$userpass;
				
				mysql_query("UPDATE myMembers SET last_log_date=now() WHERE id='$id' LIMIT 1");
				}//closing while loop
	//Remember Me Section
	
	if($remember=="yes")
	{
		$encrptedID=base64_encode("g4enm2c0c4y3dn3727553$id");
		//Setting cookie to expiry date to 30 days
		setcookie("idCookie",$encrptedID,time()+60*60*24*100,'/');
		setcookie("passCookie",$pass,time()+60*60*24*100,'/');
		}//closing remember me section
		
	//All good then log in
	
	header("location:index.php?test=$id");
	exit();
	
}//closing login check
	else
	{
		$errorMsg="ERROR: Incorrect Login Data. Please Try Again.";
		}
		
		}//closing of else in line no 26
	
	}//closing post email
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LOGIN--CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />

<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m3{
	background:#590D17;
}
#click:hover{
	color:#FFF;
}

.email{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	}
	
.password{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	}
	
.button{
	display:block;
	background-color:#09C;
	color:#FFF;
	padding:10px;
	border:0;
}
	
.button:hover{
	display:block;
	background-color:#0033CC;
	color:#FFF;
	padding:10px;
	border:0;
}
</style>

<script type="text/javascript" src="js/jquery-1.4.2.js">
</script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	
	$('#messbox').fadeOut(10000);
    
});

</script>

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
<br/>


<table align="center" width="90%" bgcolor="#00CC00" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<table align="center" width="100%" border="0" bgcolor="#006600"><tr><td>
<p>&nbsp;</p>
<p align="center" class="color">Welcome To The Social Network Of NIT Trichy</p><p>&nbsp;</p>
</td></tr></table>
<table width="90%" align="center" cellpadding="">
<tr>
<td>
<p align="center" style="color:#000000; font-weight:bold;" id="messbox">
<?php echo "$errorMsg"; ?>
</p>
</td>
</tr>
</table>


<!-- form goes on here-->

<table cellpadding="5px" align="center" border="0" bordercolor="#FFFFFF";>
<form action="login.php" enctype="multipart/form-data" name="loginform" id="loginform" method="post">
<tr>
<td colspan="2">
<p class="color" align="center">LOGIN HERE</td>
</tr>
<tr>
<td>
<span class="color">Email:</span>
</td>
<td><input type="text" name="email" id="email" class="email" size="40"/>
</td>
</tr>
<tr>
<td>
<span class="color">Password:</span>
</td>
<td><input type="password" name="password" id="password" size="40" class="password" /> 
</td>
</tr>
<tr>
<td>&nbsp;
</td>
<td>
<input type="checkbox" checked="checked" value="yes" name="remember" id="remember" /><span class="color">Remember Me</span>
</td>
</tr>
<tr>
<td>&nbsp;

</td>
<td>
<input type="submit" name="button" id="button" class="button" value=" Login "  />
</td>
</tr>
<tr>
<td colspan="2">
<span class="color">Forgot Password?</span> <a href="http://www.clubnitt.org/forgot_pass.php" class="color1" style="text-decoration:none;" id="click">Click Here</a>
</td>
</tr>
<tr>
<td colspan="2">
<span class="color">Need An Account  </span><a href="http://www.clubnitt.org/register.php" class="color1" style="text-decoration:none;" id="click">Click Here</a>
</td>
</tr>
</form>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
<tr>
</table>






</body>
</html>
<?php include_once"scripts/checkuserlog.php"?>
<?php
$outputDisplay="";

if(isset($_POST['email']))
{
	$email=$_POST['email'];
	$email=stripslashes($email);
	$email=strip_tags($email);
	$email=mysql_real_escape_string($email);
	$email=eregi_replace('`','',$email);
	
	//connecting to the database
	
	$sql=mysql_query("SELECT * FROM myMembers WHERE email='$email' AND email_activated='1'");
	$sql_email_check=mysql_num_rows($sql);
	if($sql_email_check==0)
	{
		$outputDisplay="There is no account in our database with this email address.";
	}
	else if($sql_email_check>0)
	{
		//making password for the user
		$makepass=substr($email, 0, 4);
		$random=rand();
		$temp_pass="$makepass$random";
		
		//hashing the paswsword.......
		
		function enc($string)
		{
			$salt="@x2p";
			$hash=sha1(md5($salt.$string).md5($string).sha1(md5(md5($string))));
			return $hash;
		}
		
		$hash_pass=enc($temp_pass);
		
		@mysql_query("UPDATE myMembers SET password='$hash_pass' WHERE email='$email'") or die("Cannot Set Your New Password");
		
		$to="$email";
		$headers ="From: webmaster@clubnitt.org\n";
                $headers .= "MIME-Version: 1.0\n";
                $headers .= "Content-type: text/html\n";
                $subject ="Login Password Generated";

                $message="<div align=center><br>----------------------------- New Login Password --------------------------------<br><br><br>
                Your New Password for our site is: <font color=\"#006600\"><u>$temp_pass</u></font><br><br />
				</div>";

		
		if(mail($to,$subject,$message,$headers))
		{
			$outputDisplay="Your New Login password has been emailed to you.";	
		}
		else
		{
			$outputDisplay="Password Not Sent..";
			}
	}
	
	
}	
else
{
	 $outputDisplay = 'Enter your email address into the field below.';
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m3{
	background:#590D17;
}
#click:hover{
	color:#FFF;
}

.button123{
	display:block;
	background-color:#09C;
	color:#FFF;
	padding:10px;
	border:0;
}

.button123:hover{
	display:block;
	background-color:#0033CC;
	color:#FFF;
	padding:10px;
	border:0;
}
.field123{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	
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

<table width="600px" align="center">
<tr>
<td>
<p align="center" class="big">&nbsp;</p>
<p align="center" class="color">Get Your New Password Here...</p>
<hr width="600px" align="center"/>
</td>
</tr>
</table>

<table width="600" border="0" cellpadding="5px" align="center">
  <tr>
    <td><p class="color1"><?php echo"$outputDisplay";?></p></td>
  </tr>
</table>
<table width="600px" border="0" bordercolor="#FFFFFF" align="center" cellpadding="7px">
<form action="forgot_pass.php" name="myForm" id="myForm" enctype="multipart/form-data" method="post">
  <tr>
    <td><p class="color">Forgot or lost your password?</p></td>
    <td><p class="color"> Don't worry, you will be given a new password.</p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><p class="color">Enter your email address here :</p></td>
    <td>&nbsp;<input name="email" type="text" id="email" size="40px" maxlength="50" class="field123"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;<input type="submit" name="submit" value="Get New Password" class="button123"/></td>
  </tr>
 </form>
</table>


</body>
</html>
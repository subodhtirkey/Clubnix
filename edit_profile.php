<?php include_once"scripts/checkuserlog.php";?>

<?php
//member login check
if(!$_SESSION['idx'])
{
	$msgToUser='Only Site Members Can Do This <a href="register.php">Register Here</a>';
	include_once "msgToUser.php";
	exit();
}
else if($toplinks_id != $_SESSION['id'])
{
		$msgToUser='Only Site Member Can Do This <a href="register.php"> Register Here</a>';
		include_once "msgToUser.php";
	 	exit();	
}
//Ending Member Login Check
//Setting The owner Profile id

$id=$toplinks_id;

//Now Initializing the variable

$error_msg="";
$errorMsg="";
$success_msg="";
$firstname="";
$lastname="";
$country="";
$state="";
$city="";
$zip="";
$bio_body="";
$department="";
$user_pic="";
$password="";
$cacheBuster= rand(9999999,99999999999);

//If we are parsing any 
if(isset($_POST['parse_value']))
{
	//WIPIT
	$thisWipit=$_POST['thisWipit'];
	$sessWipit=base64_decode($_SESSION['wipit']);
	//
	
	if(!isset($_SESSION['wipit']) || !isset($_SESSION['idx']))
	{
		echo 'Error: Your session is expired from inactivity. Please <a href="index.php">click here to refresh it</a>';
		exit();	
	}
	//
	if($sessWipit!=$thisWipit)
	{
		echo'Your session is expired from inactivity. Please <a href="index.php">click here to refresh it</a>';
		exit();	
	}
	//
	if($thisWipit=="")
	{
		echo'Error: Missing data.... Click Back In Your Browser please';
		exit();	
	}
	
	//___parsing the uploaded picture
	
	if($_POST['parse_value']=='picbox')
	
	{
		if($_FILES['fileField']['tmp_name']!="")
		{
			$maxfilesize=1048576; //1mb
			if($_FILES['fileField']['size']>$maxfilesize)
			{ 
			$error_msg='Error: Your image size is too big.. Please use the image with small size'; 
			unlink($_FILES['fileField']['tmp_name']); 
			}
			else if($_FILES['fileField']['type'] !== 'image/jpeg')
			{
				$error_msg='Error: Your image was not of the accepted format. Please try again';
				unlink($_FILES['fileField']['tmp_name']);
			}
			else
			{
				$newname="pic.jpg";
				$place_file=move_uploaded_file($_FILES['fileField']['tmp_name'], "members/$id/".$newname);
				$success_msg="<img src='images/tick.png' width='20' height='20' alt='tick' />Your photo has been successfully updated";
				
			}
			
		}
	}//closing picbox
	
	
	//parsing personnal information
	
	if($_POST['parse_value']=='locationbox')
	{
		
		$firstname=$_POST['firstname'];
		$lastname=$_POST['lastname'];
		$department=$_POST['department'];
		$country=$_POST['country'];
		$state=$_POST['state'];
		$city=$_POST['city'];
		$zip=$_POST['zip'];
		
		//////////////////////////////////////////////////
		
		$firstname=preg_replace('#[^A-Za-z]#i','',$firstname);
		$lastname=preg_replace('#[^A-Za-z]#i','',$lastname);
		$department=preg_replace('#[^A-Za-z]#i','',$department);
		$country=preg_replace('#[^A-Za-z]#i','',$country);
		$country=strip_tags($country);
		$country=str_replace("'","&#39",$country);
		$country=str_replace("`","&#39",$country);
		$country=mysql_real_escape_string($country);
		$state=preg_replace('#[^A-Za-z]#i','',$state);
		$city=preg_replace('#[^A-Za-z]#i','',$city);
		
		if((!$firstname)||(!$lastname)||(!$department)||(!$country)||(!$state)||(!$city)||(!$zip))
		{
			
		$error_msg="<img src='images/arrow.png' width='20' height='20' alt='arrow' /> Error: You did not submit the following required information..<br/><br/>";
		
		if(!$firstname)
		{$error_msg.="*Firstname<br/>";}
		if(!$lastname)
		{$error_msg.="*Lastname<br/>";}
		if(!$department)
		{$error_msg.="*Department<br/>";}
		if(!$country)
		{$error_msg.="*Country<br/>";}
		if(!$state)
		{$error_msg.="*State<br/>";}
		if(!$city)
		{$error_msg.="*City<br/>";}
		if(!$zip)
		{$error_msg.="*Zip/Pincode<br/>";}
		
		}
		else
		
		{
		$sqlupdate=mysql_query("UPDATE myMembers SET firstname='$firstname', lastname='$lastname' ,department='$department', country='$country',state='$state', city='$city', zip='$zip' WHERE id='$id' LIMIT 1");
		
		if($sqlupdate)
		{
			$success_msg="<img src='images/tick.png' width='20' height='20' alt='tick' />Profile Information Has updated successfully";
		}
		else{
			$error_msg='Error: Problem arose during the information exchange please try again';
			}
				
		}
	}//closing personal information
	
	
	//Parsing the bio data
	
	if($_POST['parse_value']=='aboutbox')
	{
		$bio_body=$_POST['about'];
		$bio_body=str_replace("'","",$bio_body);
		$bio_body=str_replace("`","",$bio_body);
		$bio_body=mysql_real_escape_string($bio_body);
		$bio_body=nl2br(htmlspecialchars($bio_body));
		
		//updating the database
		
		$sqlupdate=mysql_query("UPDATE myMembers SET bio_body='$bio_body' WHERE id='$id' LIMIT 1");	
		if($sqlupdate)
		{
			$success_msg="<img src='images/tick.png' width='20' height='20' alt='tick' /> Your information has been updated successfully";
		}
		else
		{
			$error_msg='Error: Problem arose during the information exchange. Please try again.';
		}
	}//closing the bio data
	
	//Parsing the password
	
	if($_POST['parse_value']=='passwordbox')
	{
		$old_pass=$_POST['old_pass'];
		$new_pass=$_POST['new_pass'];
		$conf_pass=$_POST['conf_pass'];
		
		//Before feeding into the database
		$old_pass=stripslashes($old_pass);
		$old_pass=strip_tags($old_pass);
		
		$new_pass=stripslashes($new_pass);
		$new_pass=strip_tags($new_pass);
		
		$conf_pass=stripslashes($conf_pass);
		$conf_pass=strip_tags($conf_pass);
		//
		
		if((!$old_pass)||(!$new_pass)||(!$conf_pass))
		{
			$error_msg="Error: You did not submit the following required information..<br/><br/>";
			
			if(!$old_pass)
		{$error_msg.="*Original Password<br/>";}
		if(!$new_pass)
		{$error_msg.="*New Password<br/>";}
		if(!$conf_pass)
		{$error_msg.="*Comfirm New Password<br/>";}
		}
		else{
		if($new_pass != $conf_pass)
		{
			$error_msg='Error: New Password and Confirm New Password did not match';
			
		}
		
	else{
		
		//hashing the paswsword.......
		
		function enc($string)
		{
			$salt="@x2p";
			$hash=sha1(md5($salt.$string).md5($string).sha1(md5(md5($string))));
			return $hash;
		}
			
	 $hash_cur_pass = enc($old_pass);
	 $hash_new_pass = enc($new_pass);
	 //Checking the database
	 
	 $sql_check=mysql_query("SELECT * FROM myMembers WHERE id='$id' AND password='$hash_cur_pass' LIMIT 1");
	 $sql_check_double=mysql_num_rows($sql_check);
	if($sql_check_double==1)
	{
		$sqlupdate=mysql_query("UPDATE myMembers SET password='$hash_new_pass' WHERE id='$id' LIMIT 1");
		$success_msg="<img src='images/tick.png' width='20' height='20' alt='tick' /> Your password has been successfully updated";
	}
	else
	{
		$error_msg="Your Password did not match with what we have in our database";
	}
	
	}
	}//closing the first else
	}//closing the password
	
	//parsing the deleteaccount
	
	if($_POST['parse_value']=='deletebox')
	{
		$del_pass=$_POST['pass'];
		$del_pass=stripslashes($del_pass);
		$del_pass=strip_tags($del_pass);
		
		
		function enc($string)
		{
			$salt="@x2p";
			$hash=sha1(md5($salt.$string).md5($string).sha1(md5(md5($string))));
			return $hash;
		}
		
		$hash_del_pass=enc($del_pass);
		
		//Checking weather the given password is coorect or not
		
		$sql_check=mysql_query("SELECT * FROM myMembers WHERE id='$id' AND password='$hash_del_pass' LIMIT 1");
		$sql_check_double=mysql_num_rows($sql_check);
		if($sql_check_double>0)
		{
			$pic1 = ("members/$id/pic.jpg");
	 		if (file_exists($pic1)) {
		    unlink($pic1);
    	}
    $dir = "members/$id";
    rmdir($dir);
	// Connect to database
    include_once "scripts/connect_to_mysql.php";
    $sqlTable1 = mysql_query("DELETE FROM myMembers WHERE id='$id'"); 
    $sqlTable2 = mysql_query("DELETE FROM blabbing WHERE mem_id='$id'");
	session_destroy();
	$msgToUser = 'Your Account has been deleted';
    include_once 'msgToUser.php'; 
    exit(); 
	}
	else
	{
		$error_msg="Your Password did not match with what we have in our database";
	
	}
		
	}//closing delete box
}//closing the first block

////////___________________Establishing profile interaction token_________________//////////

if(!isset($_SESSION['wipit'])){//check to see if session wipit is set yet
	session_register('wipit'); //be sure to register the session if it is not set yet

}
	$thisRandNum = rand(9999999999999,999999999999999999);
	$_SESSION['wipit'] = base64_encode($thisRandNum);
// ------- END ESTABLISH THE PROFILE INTERACTION TOKEN ---------/////


//final default sql query that will refresh everything

$sql_default=mysql_query("SELECT * FROM myMembers WHERE id='$id'");

while($row=mysql_fetch_array($sql_default))
{
	$firstname = $row["firstname"];
	$lastname = $row["lastname"];
	$country = $row["country"];	
	$state = $row["state"];
	$city = $row["city"];
	$zip=$row["zip"];
	$bio_body = $row["bio_body"];
	$bio_body = str_replace("<br />", "", $bio_body);
	$bio_body = stripslashes($bio_body);
	$department=$row["department"];
	
	///////  Mechanism to Display Pic. See if they have uploaded a pic or not  //////////////////////////
	$check_pic = "members/$id/pic.jpg";
	$default_pic = "members/0/pic.jpg";
	if (file_exists($check_pic)) {
    $user_pic = "<img src=\"$check_pic?$cacheBuster\" width=\"50px\" />"; // forces picture to be 100px wide and no more
	} else {
	$user_pic = "<img src=\"$default_pic\" width=\"50px\" />"; // forces default picture to be 100px wide and no more
	}
	

} // close while loop

	
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ACCOUNT SETTINGS -- CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m2{
	background:#590D17;
}
.editlink{
	color:#FFFFFF;
	font-size:18px;
	padding:2px;
	text-decoration:none;
	}
.editlink:hover{
	color:#666;
}
.editbox{
	display:none;
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

.required{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	
}
</style>

<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script language="javascript" type="text/javascript">
function toggle(x)
{
	if ($('#'+x).is(":hidden")) {
			$('#'+x).slideDown(300);
		} else {
			$('#'+x).fadeOut(300);
		}
		$('.editbox').hide();
}
</script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	
	$('#messagebox').fadeOut(10000);
    
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

<!--message box for success and error-->
<table width="80%" align="center" cellpadding="5px" bgcolor="#0033FF">
<tr>
<td>
<table width="90%" align="center" cellpadding="5px" >
<tr>
<td>
<p style="font-weight:bold; color:#FFFFFF;">Account Settings</p>
<p style="font-weight:bold; color:#FFFFFF;" id="messagebox"><?php echo"$error_msg";?>
  
  <?php echo"$success_msg";?></p>
</td>
</tr>
</table>
</td>
</tr>
</table>
<!--end message box for success and error-->

<table width="80%" align="center" cellpadding="12px" cellspacing="12px" bgcolor="#00CC33"><tr><td>
<h2><a href="#" onclick="return false" onmousedown="javascript:toggle('picbox')" class="editlink">CHANGE PROFILE PICTURE</a></h2>

<!--editbox 1-->

<table class="editbox" id="picbox" width="100%" cellpadding="12px" align="center" bgcolor="#BDF">
<form action="edit_profile.php" enctype="multipart/form-data" method="post">
<tr>
<td align="center" width="30%"><?php echo"$user_pic";?>
</td>
<td align="center" width="50%"><input type="file" name="fileField" size="40" class="field123" />
</td>
<td align="center" width="20%"><input type="submit" value="Update" class="button123"/>
<input type="hidden" name="parse_value" value="picbox" />
<input name="thisWipit" type="hidden" value="<?php echo $thisRandNum;?>"/>
</td>
</tr>
</form>
</table>

<h2><a href="#" onclick="return false" onmousedown="javascript:toggle('locationbox')" class="editlink">CHANGE PROFILE INFORMATION</a></h2>

<!--editbox 2-->
<table width="100%" class="editbox" id="locationbox" align="center" bgcolor="#BDF" cellpadding="12px" ><tr><td>
<table align="center" cellpadding="7px" style="font-size:16px; font-weight:bold;">
<form action="edit_profile.php" enctype="multipart/form-data" method="post">
<tr>
<td>Firstname:
</td>
<td><input type="text" name="firstname" value="<?php echo"$firstname";?>" size="40" class="field123"/>
</td>
</tr>
<tr>
<td>Lastname:
</td>
<td><input type="text" name="lastname" value="<?php echo"$lastname";?>" size="40" class="field123"/>
</td>
<tr>
<td>Department:
</td>
<td>
<select name="department" id="departmwnt" title="" class="required" >
<option value="CSE" selected="selected" >CSE</option>
<option value="ECE" >ECE</option>
<option value="EEE" >EEE</option>
<option value="ARCHI" >Architecture</option>
<option value="CHEM" >Chemical</option>
<option value="CIVIL" >Civil</option>
<option value="MCA" >MCA</option>
<option value="ICE" >ICE</option>
<option value="MECH" >Mechanical</option>
<option value="META" >MME</option>
<option value="PROD" >Production</option>
</select>
</td>
</tr>
<tr>
<td>Country:
</td>
<td><input type="text" name="country" size="40" value="<?php echo "$country";?>" class="field123"/>
</td>
</tr>
<tr>
<td>State:
</td>
<td><input type="text" name="state" size="40" value="<?php echo "$state";?>" class="field123" />
</td>
</tr>
<tr>
<td>City:
</td>
<td><input type="text" name="city" size="40" value="<?php echo "$city";?>" class="field123"/>
</td>
</tr>
<tr>
<td>Zip/Pincode:
</td>
<td><input type="text" name="zip" size="40" value="<?php echo "$zip";?>" class="field123" />
</td>
</tr>
<tr>
<td><p>&nbsp;</p>
</td>
<td><input type="submit" name="button" value="Update" class="button123"/>
<input name="parse_value" value="locationbox" type="hidden" />
<input name="thisWipit" type="hidden" value="<?php echo $thisRandNum;?>"/>
</td>
</tr>
</form>
</table>
</td>
</tr>
</table>


<h2><a href="#" onclick="return false" onmousedown="javascript:toggle('aboutbox')" class="editlink">ABOUT YOU</a></h2>

<!--editbox 3-->

<table width="100%" class="editbox" id="aboutbox" align="center" bgcolor="#BDF" cellpadding="12px">
<form action="edit_profile.php" method="post" enctype="multipart/form-data">
<tr>
<td>
<table width="50%" align="center" cellpadding="7px">
<tr>
<td><textarea name="about" cols="70" rows="5" class="field123"><?php echo"$bio_body";?></textarea>
</td>
<td><input type="submit" name="button" value="Update" class="button123" />
<input type="hidden" name="parse_value" value="aboutbox" />
<input type="hidden" name="thisWipit" value="<?php echo $thisRandNum;?>" />
</td>
</table>
</td>
</tr>
</form>
</table>

<h2><a href="#" onclick="return false" onmousedown="javascript:toggle('passwordbox')" class="editlink">CHANGE PASSWORD</a></h2>

<!--editbox 4-->

<table width="100%" align="center" cellpadding="2px" bgcolor="#BDF"id="passwordbox" class="editbox" cellspacing="12px">
<tr>
<td>
<table width="50%" align="center" cellpadding="5px"  style="font-size:16px; font-weight:bold;">
<form action="edit_profile.php" method="post" enctype="multipart/form-data">
<tr>
<td>Original Password:
</td>
<td><input name="old_pass" type="password" value="<?php $old_pass; ?>" size="40" class="field123"/>
</td>
</tr>
<tr>
<td>New Password:
</td>
<td><input name="new_pass" type="password" value="<?php $new_pass; ?>" size="40" class="field123"/>
</td>
</tr>
<tr>
<td>Comfirm New Password:
</td>
<td><input name="conf_pass" type="password" value="<?php $conf_pass; ?>" size="40" class="field123"/>
</td>
</tr>
<tr>
<td><p>&nbsp;</p>
</td>
<td>
<input name="button" type="submit" value="Update" class="button123"/>
<input type="hidden" name="parse_value" value="passwordbox" />
<input type="hidden" name="thisWipit" value="<?php echo $thisRandNum;?>"/>
</td>
</tr>
</form>
</table>
</td>
</tr>
</table>


<h2><a href="#" onclick="return false" onmousedown="javascript:toggle('deletebox')" class="editlink">DELETE ACCOUNT</a>
<!--editbox 5--></h2>
<table width="100%" align="center" class="editbox" id="deletebox" bgcolor="#BDF" cellpadding="12px">
  <form action="edit_profile.php" enctype="multipart/form-data" method="post">
<tr>
<td>
<table align="center" width="100%" cellpadding="5px" style="font-size:16px; font-weight:bold;">
<tr>
<td align="center">Enter Your Current Password Here:
</td>
<td align="center"><input type="password" name="pass" size="40" class="field123" />
</td>
<td align="center"><input type="submit" name="buttondel" value="   DELETE ACCOUNT   " class="button123"/>
<input type="hidden" name="parse_value" value="deletebox" />
<input type="hidden" name="thisWipit" value="<?php echo $thisRandNum; ?>" />
</td>
</tr>
</table>
</td>
</tr>
</form>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td></tr></table>

</body>
</html>
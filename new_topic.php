<?php include_once"scripts/checkuserlog.php" ?>
<?php 
//checking if the user is logged in or not
if(!isset($_SESSION['userpass']) || $_SESSION['userpass']=="")
{
	$msgToUser="Please<a href='login.php' style='text-decoration:none;'> Login</a> or <a href='register.php' style='text-decoration:none;'> Register</a> to continue. ";
	include_once"msgToUser.php";
	exit();
	
	/*echo "Please<a href='login.php' style='text-decoration:none;'> Login</a> or <a href='register.php' style='text-decoration:none;'> Register</a> to continue. ";	
	exit();*/
}
//Assuming that the user is a member as his password session variable is set
//ccahecking the database whethter the user is coreect or not
$u_id=mysql_real_escape_string($_SESSION['id']);
$u_name=mysql_real_escape_string($_SESSION['firstname']);
$useremail=mysql_real_escape_string($_SESSION['useremail']);

$sql=mysql_query("SELECT * FROM myMembers WHERE id='$u_id' AND firstname='$u_name' AND email='$useremail'");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	echo "Error: You do not exixt in the system";
	exit();	
}
//checking to see whether sid and section title have been set
if(!isset($_POST['forum_id']) || $_POST['forum_id']=="" ||!isset($_POST['forum_title']) || $_POST['forum_title']=="")
{
	echo "ERROR: Important variables have not been set";
	exit();
}
//aquiring the variables and proceeding to show the user a form for creating a new topic
$forum_section_id=preg_replace('#[^0-9]#i','',$_POST['forum_id']);
$forum_section_title=preg_replace('#[^A-Za-z0-9 ]#i','',$_POST['forum_title']);

//Cheching if the section is ther in the database or not

$sql1=mysql_query("SELECT * FROM mydep_sections WHERE id='$forum_section_id' AND title='$forum_section_title'");
$numRows1=mysql_num_rows($sql1);
if($numRows1<1)
{
echo'That section does not exist';
exit();	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CLUBNITT DEPARTMENT FORUM</title>
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
}
.button2:hover{
	background-color:#00CC00;
	border:0;
	padding:5px;
	border-color:#003300;
	font-weight:bold;
	color:#000000;
}

</style>
<script type="text/javascript">
function validation()
{
var isvalid=true;
if(document.form1.post_title.value=="")
{
	alert("Please type title for the topic");
	isvalid=false;
}
else if(document.form1.post_title.value.length<10)
{
	alert("Your Title should be 10 characters long");
	isvalid=false;
}
else if(document.form1.post_body.value=="")
{
	alert("Please type in your topic");
	isvalid=false
}
return isvalid;
}
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
<table width="900" align="center" height="142" background="subodh/forumheader.png" border="0" cellpadding="0" cellspacing="0">
<tr><td></td></tr>
</table>
<table width="900" align="center" border="0" cellpadding="2" cellspacing="2"><tr><td>
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="index.php" id="link">Home</a> &larr; <a href="forum.php" id="link">Clubnitt dep. forum</a> &larr; <?php echo $forum_section_title; ?></p>
</td></tr></table>

<table width="900" align="center" cellpadding="5" cellspacing="5" border="0" bgcolor="#00CCFF"><tr><td>
<form action="parse_posts.php" method="post" name="form1" enctype="multipart/form-data">
<input name="post_type" type="hidden" value="a" />
Topic Author:<br/>
<input name="topic_author"  type="text" disabled="disabled" maxlength="64" style="width:96%" value="<?php echo $u_name; ?>" />
<br/><br/>
Please type in a title
<br/>
<input name="post_title" type="text" maxlength="60" style="width:96%" />
<br/><br/>
Please type in your topic body
<br/>
<textarea name="post_body" rows="15" style="width:96%" ></textarea>
<br/><br/>
<input name="btn" type="submit"  value=" Create a new thread " class="button2" onclick="javascript: return validation();" />

<input name="fsID" type="hidden" value="<?php echo $forum_section_id; ?>" />
<input name="fsTitle" type="hidden" value="<?php echo $forum_section_title; ?>" />
<input name="uid" type="hidden" value="<?php echo $_SESSION['id']; ?>" />
<input name="upass" type="hidden" value="<?php echo $_SESSION['userpass']?>" />
</form>
</td></tr></table>

</body>
</html>
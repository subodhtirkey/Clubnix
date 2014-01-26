<?php 
include_once("scripts/checkuserlog.php");

///////INCLUDING THE CLCASS FILES FOR AUTO MAKE LINKS OUT OF FULL URL FOR TIMEE AGO DATE FORMATTING___________________
include_once("class_files/autoMakeLinks.php");
include_once("class_files/agoTimeFormat.php");

////Creaating the two objects before we can use them below on this scripts 

$activeLinkObject=new autoActiveLink;
$myObject=new convertToAgo;

?>
<?php include_once"scripts/randMembers.php"; ?>
<?php
$sql_posts=mysql_query("SELECT id, mem_id, the_post, post_date FROM posting ORDER BY post_date DESC LIMIT 30");
//INITIALIZING THE POST DISPLAY LIST
$postDisplayList="";

while($row=mysql_fetch_array($sql_posts))
{
	$postid=$row['id'];
	$uid=$row['mem_id'];
	$the_post=$row['the_post'];
	//
	$the_post=($activeLinkObject -> makeActiveLink($the_post));
	$post_date=$row['post_date'];
	$convertedTime=($myObject -> convert_datetime($post_date));
	$whenPost=($myObject -> makeAgo($convertedTime));
	
	//inner sql query////////////
	$sql_mem_data=mysql_query("SELECT id,firstname, lastname FROM myMembers WHERE id='$uid' LIMIT 1 ");
	while($row=mysql_fetch_array($sql_mem_data))
	{
		$uid=$row['id'];
		$firstname=$row['firstname'];
		//mechanism to display pic
		
		$check_pic="members/$uid/pic.jpg";
		$default_pic="members/0/pic.jpg";
		
		if(file_exists($check_pic))
{
	$post_pic='<div style="overflow:hidden; height:40px"><a href="profile.php?id=' .$uid. '"><img src="' .$check_pic. '" width="40px" border="0"></a></div>';
}
else
{ 
$post_pic='<div style="overflow:hidden; height:40px"><a href="profile.php?id=' .$uid. '"><img src="' .$default_pic. '" width="40px" border="0"></a></div>';
}
		
		$postDisplayList.='
		<table id="postdis" width="100%" cellpadding="4" cellspacing="4"><tr>
	<td width="10%" valign="top" align="center">' .$post_pic. '
	</td>
	<td width="90%" valign="top">
	<span textsize="10">' .$whenPost.  '<a href="profile.php?id=' .$uid. '" id="link1"> '  .$firstname. '</a>  said:</span><br/>' .$the_post. '
	</td>
	<tr></table>';
		}
}


?>
<?php
//for google map----------------
$city="National Institute Of Technology";
$state="Trichy";
$country="Tamil Nadu";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Welcome to the social network of NIT Trichy...Connect with your college friends... Know your seniors.. Know your juniors.. Share your thoughts.... ..Only students of Nit Trichy are allowed to join this website........" />
<meta name="Keywords" content="clubnitt, clubnitt.org, nit trichy, social network, college, delta,engineering college club" />
<title>CLUBNITT -- NIT TRICHY</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m7{
	background:#590D17;
}
.block{
	cursor:pointer;
}
#link1{
	text-decoration:none;
	font-weight:bold;
}
#memtable{
	background-image:url(images/member_back1.png);
}
#memtable:hover{
	background-image:url(images/member_back2.png);
}

.devpic{
	background-image:url(images/dev1.png);
}
.devpic:hover{
	background-image:url(images/dev.jpg);
}


.collegepic{
	background-image:url(images/collegeweb.png);
}
.collegepic:hover{
	background-image:url(images/collegeweb1.png);
}
#aboutpic{
	background-image:url(images/about1.png);
}
#aboutpic:hover{
	background-image:url(images/about.png);
}
#postdis{
	background-color:#C66300;
}
#postdis:hover{
	background-color:#F63;
}
#forum{
	background-image:url(images/forum1.png);
}
#forum:hover{
	background-image:url(images/forum2.png);
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

<br/>
<!--container table 1-->
<table width="70%" align="center">
<tr><td>


<table width="1000" height="309" align="center" id="memtable" cellpadding="0" cellspacing="0" border="0" onClick="document.location.href='http://www.clubnitt.org/member_search.php';" style="cursor:pointer;cursor:hand" title="Click here to see members of clubnitt">
<tr>
<td align="center" height="309">
</td>
</tr>
</table>


<table width="1000" align="center" id="" cellpadding="0" cellspacing="0" border="0"><tr>
<td align="left" width="398" height="158" class="devpic" onClick="document.location.href='http://www.clubnitt.org/profile.php?id=7';" style="cursor:pointer;cursor:hand" title="Click here to visit the developer profile ">
</td>
<td align="right" width="602" height="158" class="collegepic" onClick="document.location.href='http://www.nitt.edu';" style="cursor:pointer;cursor:hand"title="Click here to visit NIT TRICHY official website ">
</td>
</tr></table>

<table width="1000" height="158" align="center" id="aboutpic" cellpadding="0" cellspacing="0" border="0" onClick="document.location.href='http://www.clubnitt.org/about.php';" style="cursor:pointer;cursor:hand" title="Click here to know about clubnitt.org "><tr>
<td align="center">
</td>
</tr></table>


<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#9933CC"><tr>
<tr><td width="1000" style="background-color:#0C0;font-weight:bold; color:#FFFFFF; padding:7px;" >
SOME RANDOM MEMBERS
</td></tr>
<td>
<?php echo $MemberDisplayList; ?>
</td></tr></table>
<table width="1000" align="center" id="google map" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="1000" style="background-color:#060;font-weight:bold; color:#FFFFFF; padding:7px;" >MAP VIEW OF NIT TRICHY</td>
</tr>
<tr><td>
<iframe width="1000" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo "$city,+$state,+$country";?>&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo "$city,+$state,+$country";?>&amp;z=12&amp;output=embed"></iframe>

</td></tr></table>

<table width="1000" height="158" align="center" id="forum" cellpadding="0" cellspacing="0" border="0" onClick="document.location.href='http://www.clubnitt.org/forum.php';" style="cursor:pointer;cursor:hand" title="Click here to visit the clubnitt.org department forum  "><tr>
<td align="center">
</td>
</tr></table>





<table width="1000" align="center" id="posttable" cellpadding="0" cellspacing="0" border="0"><tr>
<td width="1000" style="background-color:#0033FF; font-weight:bold; color:#FFFFFF; padding:7px;" >RECENT POSTS BY STUDENTS</td>
</tr>
<tr>
<td>
<?php echo $postDisplayList;?>
</td></tr></table>


<!--end container table 1-->
</td></tr></table>

<table width="1000" align="center" bgcolor="#000000"><tr><td>

<p align="center" style="font-weight:bold; color:#FFF;">clubnitt.org</p>

</td></tr></table>



</body>

</html>
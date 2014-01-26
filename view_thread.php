<?php 
session_start();
include_once"scripts/checkuserlog.php";
include_once"class_files/agoTimeFormat.php";

$myAgoObject=new convertToAgo;


//Getting the "id " url variable and querying the database for the orignal post of the thread

$thread_id=preg_replace('#[^0-9]#i','',$_GET['id']);
$sql=mysql_query("SELECT * FROM mydep_posts WHERE id='$thread_id' AND type='a' LIMIT 1");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	echo "ERROR: That tread does not exist";
	exit();
}

while($row=mysql_fetch_array($sql))
{
	$post_author=$row['post_author'];
	$post_author_id=$row['post_author_id'];
	$date_time=$row['date_time'];
	$date_time=strftime("%b %d, %Y", strtotime($date_time));
	$section_title=$row['section_title'];
	$section_id=$row['section_id'];
	$thread_title=$row['thread_title'];
	$post_body=$row['post_body'];
}
?>

<?php 
//NOw queying any post in the database and place in a dynamic list

$allresponse="";

$sql1=mysql_query("SELECT * FROM mydep_posts WHERE otid='$thread_id' AND type='b'");
$numRows1=mysql_num_rows($sql1);
if($numRows1<1)
{
	$allresponse='<div id="none_yet_div">Nobody has responded to this you can be the first </div>';
}

else
{
while($row=mysql_fetch_array($sql1))
{
	$reply_author=$row['post_author'];
	$reply_author_id=$row['post_author_id'];
	$date_n_time=$row['date_time'];
	$convertedTime=($myAgoObject -> convert_datetime($date_n_time));
	$whenReply=($myAgoObject -> makeAgo($convertedTime));
	$reply_body=$row['post_body'];
	$allresponse.='<div class="response_top_div">Re: ' .$thread_title. '
	&nbsp;&nbsp;&bull;&nbsp;' .$whenReply. ' <a href="profile.php?id=' .$reply_author_id. '" style="text-decoration:none;">' .$reply_author. ' </a> Said : </div>
	<div class="response_div">' .$reply_body. ' </div>';
}	
}
?>
<?php 
//Making sure that the user session variable are set in order to show them a reply button..........
$replyButton='You must <a href="login.php" style="text-decoration:none;"> Log In </a> to respond.';
if(isset($_SESSION['id']) && isset($_SESSION['firstname']) && isset($_SESSION['useremail']) && isset($_SESSION['userpass']))
{
	$replyButton='<input name="myBtn1" type="submit" value=" Post a response " class="button2" onmousedown="javascript: toggle(\'response_form\')" />';

}
//Checking the database to be sure that their id , password and email session all match in the database
if(isset($_SESSION['id']) && isset($_SESSION['firstname']) && isset($_SESSION['useremail']) && isset($_SESSION['userpass']))
{
$uid=mysql_real_escape_string($_SESSION['id']);
$uname=mysql_real_escape_string($_SESSION['firstname']);
$u_email=mysql_real_escape_string($_SESSION['useremail']);
$_upass=mysql_real_escape_string($_SESSION['userpass']);
$sql=mysql_query("SELECT * FROM myMembers WHERE id='$uid' AND firstname='$uname' AND email='$u_email' AND password='$_upass'");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	$replyButton='There is problem with your session variable' ;
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $thread_title; ?></title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<script src="js/jquery-1.4.2.js" type="text/javascript"></script>
<script type="text/javascript">
function toggle(x)
{
	if($('#'+x).is(":hidden"))
	{
		$('#'+x).slideDown(200);
	}
	else
	{
		$('#'+x).slideUp(200);
	}
}

$('#responseForm').submit(function(){$('input[type=submit]', this).attr('disabled', 'disabled');});
function parseResponse ( ) {
	  var thread_id = $("#thread_id");
	  var post_body = $("#post_body");
	  var fs_id = $("#forum_section_id");
	  var fs_title = $("#forum_section_title");
	  var u_id = $("#member_id");
	  var u_pass = $("#member_password");
	  var url = "parse_posts.php";
      if (post_body.val() == "") {
           $("#formError").html('<font size="+2">Please type something</font>').show().fadeOut(3000);
      } else if (post_body.val().length < 2 ) { 
	         $("#formError").html('<font size="+2">Your post must be at least 2 characters long').show().fadeOut(3000);
      } else {
		$("#myBtn1").hide();
		$("#formProcessGif").show();
        $.post(url,{ post_type: "b", tid: thread_id.val(), post_body: post_body.val(), fsID: fs_id.val(), fsTitle: fs_title.val(), uid: u_id.val(), upass: u_pass.val() } , function(data) {
			   $("#none_yet_div").hide();
			   var myDiv = document.getElementById('responses');
			   var magicdiv1 = document.createElement('div');
			   magicdiv1.setAttribute("class", "response_top_div");
			   magicdiv1.htmlContent = 'Re: <?php echo $thread_title ?>';
			   magicdiv1.innerHTML = 'Re: <?php echo $thread_title ?>';
			   myDiv.appendChild(magicdiv1);
			   var magicdiv = document.createElement('div');
			   magicdiv.setAttribute("class", "response_div");
			   magicdiv.htmlContent = data;
			   magicdiv.innerHTML = data;
			   myDiv.appendChild(magicdiv);
			   $('#response_form').slideUp("fast");
			   document.responseForm.post_body.value='';
			   $("#formProcessGif").hide();
			   $("#myBtn1").show();
         }); 
	  }
}
</script>
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

.topic_div {
	background-color: #D9F9FF;
	font-size:14px;
	padding:16px;
	border: #01B3D8 1px solid;
	margin-bottom:6px;
	font-weight: 500;
	color:#069;
}
.response_top_div {
	background-color: #E4E4E4;
	color: #666;
	font-size:12px;
	padding:4px;
	border: #CCC 1px solid;
	border-bottom:none;
	color: #999;
}
.response_div {
	background-color: #FFF;
	font-size:12px;
	padding:12px;
	border:#CCC 1px solid;
	margin-bottom:6px;
	
	overflow:hidden;
}
#none_yet_div {
	background-color: #E4E4E4;
	font-size:14px;
	padding:16px;
	border: #CCC 1px solid;
	margin-bottom:6px;
	color: #999;
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

<table width="900" align="center" height="142" background="subodh/forumheader.png" border="0" cellpadding="0" cellspacing="0">
<tr><td></td></tr>
</table>
<table align="center" width="900" border="0" cellpadding="2" cellspacing="2" bgcolor=""><tr><td>
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="index.php" id="link">Home</a> &larr; <a href="forum.php" id="link">Clubnitt dep. forum</a> &larr; <a href="section.php?id=<?php echo $section_id; ?>" id="link"> <?php echo $section_title; ?></a></p>
</td></tr></table>
<table width="900" align="center" bgcolor="#6666FF" cellpadding="5" cellspacing="5"><tr><td>
<span class="topicTitles"><?php echo $thread_title; ?></span><br /><br />
    Topic Started By: <a href="profile.php?id=<?php echo $post_author_id; ?>" style="text-decoration:none; color:#FFF;"><?php echo $post_author; ?></a>
    &nbsp; &nbsp; &nbsp; Created: <span class="topicCreationDate"><?php echo $date_time; ?></span>
    
    <div class="topic_div"><?php echo $post_body; ?></div>
<div id="responses"><?php echo $allresponse; ?></div>

<!-- START DIV that contains the form -->
<div id="response_form" style="display:none; background-color: #BAE1FE; border:#06C 1px solid; padding:16px;">
<form action="javascript:parseResponse();" name="responseForm" id="responseForm" method="post">
    Please type in your response here <?php echo $uname; ?>:<br /><textarea name="post_body" id="post_body" cols="64" rows="12" style="width:98%;"></textarea>
    <div id="formError" style="display:none; padding:16px; color:#F00;"></div>
    <br /><br /><input name="myBtn1" id="myBtn1" type="submit" value="Submit Your Response" style="padding:6px;" class="button2"/> <span id="formProcessGif" style="display:none;">LOADING...</span>
    or <a href="#" onclick="return false" onmousedown="javascript:toggle('response_form');" style="font-weight:bold; text-decoration:none;">Cancel</a>
    <input name="thread_id" id="thread_id" type="hidden" value="<?php echo $thread_id; ?>" />
    <input name="forum_section_id" id="forum_section_id" type="hidden" value="<?php echo $section_id; ?>" />
    <input name="forum_section_title" id="forum_section_title" type="hidden" value="<?php echo $section_title; ?>" />
    <input name="member_id" id="member_id" type="hidden" value="<?php echo $_SESSION['id']; ?>" />
    <input name="member_password" id="member_password" type="hidden" value="<?php echo $_SESSION['userpass']; ?>" />
</form>
</div>
<!-- END DIV that contains the form -->
<?php echo $replyButton; ?>
<br />
</td></tr></table>
</body>
</html>
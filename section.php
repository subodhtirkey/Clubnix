<?php include_once"scripts/checkuserlog.php"?>
<?php 

include_once"class_files/agoTimeFormat.php";
$myAgoObject=new convertToAgo;


//getting the session variable from the URL variable 

if(isset($_GET['id']) && $_GET['id']!="")
{
	$sid=preg_replace('#[^0-9]#i','',$_GET['id']);
}
else{
		echo "ERROR: Variable to run the script have been removed from the URL...";
		exit();
	}

//Querying the database for the section id and to make sure that it exists int the database 

$sql=mysql_query("SELECT * FROM mydep_sections WHERE id='$sid' LIMIT 1");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	echo "ERROR: That section does not exist in the database . You have tampered with the url";	
	exit();
}

while($row=mysql_fetch_array($sql))
{
	$section_title=$row['title'];	
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Now using the section id to query the mydep_posts table in database and to view all the threads.............

$sql=mysql_query("SELECT * FROM mydep_posts WHERE type='a' AND section_id='$sid' ORDER BY date_time DESC LIMIT 100");
$dynamicList="";

$numRows=mysql_num_rows($sql);

if($numRows<1)
{
	$dynamicList="There are no threads in the section yet you can be the first one to post.";
	
}
else{
		while($row=mysql_fetch_array($sql))
		{
			$thread_id=$row['id'];
			$post_author=$row['post_author'];
			$post_author_id=$row['post_author_id'];
			$date_time=$row['date_time'];
			
			$converted_time=($myAgoObject -> convert_datetime($date_time));
			$whenPost=($myAgoObject -> makeAgo($converted_time));
			$thread_title=$row['thread_title'];;
			
			$dynamicList.='' .$post_author. ' - <a href="view_thread.php?id=' .$thread_id. '" style="text-decoration:none;" id="link"> ' .$thread_title.' </a> - ' .$whenPost. ' <br/> ';	
		}
	}


 
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $section_title; ?></title>
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
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="index.php" id="link">Home</a> &larr; <a href="forum.php" id="link">Clubnitt dep. forum</a> &larr; <?php echo $section_title; ?></p>
</td></tr></table>

<table width="900" align="center"><tr><td>
<form action="new_topic.php" method="post" enctype="multipart/form-data" name="form123" id="form123">
<input type="hidden" name="forum_id" id="forum_id" class="forum_id" value="<?php echo $sid; ?>" />
<input type="hidden" name="forum_title" id="forum_id" class="forum_id" value="<?php echo $section_title; ?>" />
<input name="mybtn1" type="submit" value=" Create a new thread " class="button2" />
</form>
</td></tr></table>.
<table width="900" align="center"><tr><td>
<span style="font-weight:bold; color:#FFFFFF;"><?php echo $dynamicList; ?></span>
</td></tr></table>

</body>
</html>
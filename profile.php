<?php 
include_once"scripts/checkuserlog.php";
include_once"class_files/agoTimeFormat.php";
include_once"class_files/autoMakeLinks.php";

//creating the objects_____________________________
$activeLinkObject=new autoActiveLink;
$myObject= new convertToAgo;
?>
<?php
//initializing the variables________________________________
$id="";
$firstname="";
$lastname="";
$department="";
$country="";
$state="";
$city="";
$zip="";
$bio_body="";
$locationinfo="";
$user_pic="";
$postdisplaylist="";
$interactionbox="";
$cacheBuster= rand(999999999,9999999999999);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//establishing the page id according to the condition

if(isset($_GET['id']))
{
	$id=$_GET['id'];
}
else if(isset($_SESSION['id']))
{
	$id=$toplinks_id;
}
else
{
	header("location:index.php");
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//filtering the id and then querying the database____________________________________________________________

$id=preg_replace('#[^0-9]#i','',$id); //filtering the id

$sql=mysql_query("SELECT * FROM myMembers WHERE id='$id' LIMIT 1");


//making sure person exist in the database_________________________
$existcount=mysql_num_rows($sql);
if($existcount==0)
{
		header("location:index.php?msg=user_does_not_exist");
		exit();
}

//while loop for getting the user data__________________________________________

while($row=mysql_fetch_array($sql))
{
	$firstname=$row['firstname'];
	$lastname=$row['lastname'];
	$department=$row['department'];
	$country=$row['country'];
	$state=$row['state'];	
	$city=$row['city'];
	$zip=$row['zip'];
	$sign_up_date=$row['sign_up_date'];
	$sign_up_date=strftime("%b %d ,%Y",strtotime($sign_up_date));
	$last_log_date=$row['last_log_date'];
	$last_log_date=strftime("%b %d ,%Y",strtotime($last_log_date));
	$bio_body=$row['bio_body'];
	$bio_body=str_replace("&#39","'",$bio_body);
	$bio_body=stripslashes($bio_body);
	$friend_array=$row['friend_array'];
	//mechanism to display pic
	
	$check_pic="members/$id/pic.jpg";
	$default_pic="members/0/pic.jpg";
	if(file_exists($check_pic))
	{
		$user_pic=" <img src=\"$check_pic?$cacheBuster\"/ width=\"218px\">";	
	}
	else
	{
			$user_pic=" <img src=\"$default_pic\"/ width=\"218px\">";	
	}
	
	//for dispalying the location information
	
	$locationinfo="$city &middot; $state <br/>
					$country";
	if($bio_body=="")
	{
		$bio_body="";
	}
	else
	{
		$bio_body="<div class='info_body'>' .$bio_body. '</div>";
	}
}//closing the while loop

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//posting new post into the database..........

if(isset($_POST['post_field']) && $_POST['post_field'] !="")
{
	$postWipit=$_POST['postWipit'];
	$sessWipit=base64_decode($_SESSION['wipit']);
	
	if(!isset($_SESSION['wipit']))
	{
	}
	else if($postWipit==$sessWipit)
	{
		//Delete any memmber posts over 50 for the members
		$sqldeleteposts=mysql_query("SELECT * FROM posting  WHERE mem_id='$id' ORDER BY post_date DESC LIMIT 50");
		$bi=1;
		while($row=mysql_fetch_array($sqldeleteposts))
		{
			$postid=$row['id'];
			if($bi>20)
			{
				$deletepost=mysql_query("DELETE FROM posting WHERE id='$postid'");
				
			}$bi++;	
		}//closing the while loop
		
		
		$post_field=$_POST['post_field'];
		$post_field=stripslashes($post_field);
		$post_field=strip_tags($post_field);
		$post_field=mysql_real_escape_string($post_field);
		$post_field=str_replace("'","&#39",$post_field);

		$sql=mysql_query("INSERT INTO posting (mem_id,the_post,post_date) VALUES ('$id','$post_field',now())") or die(			mysql_error());

		
	}//closing else if($postWipit==$sessWipit) 
	
}//closing first
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////Displaying the post////////////////////////////////////////////

//member post output construct____________________

//____________________mechanism to display pic__________________________

if(file_exists($check_pic))
{
	$post_pic='<div style="overflow:hidden; height:40px"><a href="profile.php?id=' .$id. '"><img src="' .$check_pic. '" width="40px" border="0"></a></div>';
}
else
{ 
$post_pic='<div style="overflow:hidden; height:40px"><a href="profile.php?id=' .$id. '"><img src="' .$default_pic. '" width="40px" border="0"></a></div>';
}
//___________end mechanism to display pic____________

$sql_post=mysql_query("SELECT id, mem_id,the_post,post_date FROM posting WHERE mem_id='$id' ORDER BY post_date DESC LIMIT 20");
while($row=mysql_fetch_array($sql_post))
{
	$postid=$row['id'];
	$uid=$row['id'];
	$the_post=$row['the_post'];
	$the_post=($activeLinkObject -> makeActiveLink($the_post));
	$post_date=$row['post_date'];
	$convertedTime = ($myObject -> convert_datetime($post_date));
	$whenpost = ($myObject -> makeAgo($convertedTime));
	
	$postdisplaylist.='
	<table bgcolor="#C66300" width="680"><tr>
	<td width="10%" valign="top" align="center">' .$post_pic. '
	</td>
	<td width="90%" valign="top">
	<span textsizee="10">' .$whenpost.  '<a href="profile.php?id=' .$id. '" id="link1"> '  .$firstname. '</a>  said:</span><br/>' .$the_post. '
	</td>
	<tr></table>
	
	';
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//establishing the profile interaction token...............

$thisRandNum = rand(9999999999999,999999999999999999);
$_SESSION['wipit'] = base64_encode($thisRandNum); // Will always overwrite itself each time this script runs

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//evaluating what content to place in the member interaction boxx

$friendlink="";
$the_post_form="";
$interactionbox="";
$chatoption="";
//if session idx is set and it is equal to the profile oweners id

if(isset($_SESSION['idx']) && $toplinks_id==$id)
{
	$interactionbox='
	<div class="interactiondiv">
	<a href="#" onclick="return false" onmousedown="javascript:toggle(\'friend_request\')">Connection Request</a>
	<a href="#" onclick="return false" onmousedown="javascript:toggle(\'chatbox\')"> Chatbox </a>
	</div>';	
	$the_post_form='
	<div style="background-color:#BDF; border:#999 1px solid; padding:8px">
	<form action="profile.php" method="post" enctype="multipart/form-data" name="post_form">
	<textarea name="post_field" rows="3" style="width:99%"></textarea>
	<input name="postWipit" type="hidden" value="' .$thisRandNum. '"/>
	<strong>Post Here ' .$firstname.'</strong> (220 char max)
	<input name="submit" type="submit" value="  Post  " class="button2"/>
	</form>
	</div>';
}

//if session idx is set but is not equal to the profile owners id
else if(isset($_SESSION['idx']) && $toplinks_id!=$id)
{
	$sql_array=mysql_query("SELECT friend_array FROM myMembers WHERE id='" .$toplinks_id. "' LIMIT 1");
	while($row=mysql_fetch_array($sql_array))
	{
		$ifriend_array=$row['friend_array'];	
	}
	$ifriend_array=explode(",",$ifriend_array);
	if(in_array($id,$ifriend_array))
	{
		$friendlink='<a href="#" onclick="return false" onmousedown="javascript:toggle(\'remove_friend\')">Remove Connection</a>';
	}
	else
	{
		$friendlink='<a href="#" onclick="return false" onmousedown="javascript:toggle(\'add_friend\')">Send Connection Request</a>';
	}
	
	///////////////////////
	$interactionbox='<div class="interactiondiv">
	' .$friendlink. '
	<a href="#" onclick="return false" onmousedown="javascript:toggle(\'private_message\')">  Send Message  </a></div><br/>';
	$the_post_form='';

}
else //if no seesion id is set which means we have a person who is not logged in
{
	$interactionbox='<div style="border:#CCCC 1px; color:#FFFFFF"><a href="register.php" id="link1" style="color:#008200;">Sign Up</a> OR <a href="login.php" id="link1" style="color:#008200;">Login</a> to interact with ' .$firstname. '</div>';
	$the_post_form='
	<div style="background-color:#BDF, border:#999 1px solid; padding: 8px; color:#FFFFFF">
	<a href="register.php" id="link1" style="color:#008200;">Sign Up</a> OR <a href="login.php" id="link1" style="color:#008200;">Login</a> to interact with ' .$firstname. '</div>
	';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


///__________Dispalying list if the user have atleast one friend___________

$friendlist="";
$friendpopboxlist="";
if($friend_array!="")
{
	//Assembling friendlist and links  and view upto 6 on profile
	$friend_Array=explode(",",$friend_array);
	$friendcount=count($friend_Array);
	$friend_array6=array_slice($friend_Array,0,6);
	$friendlist.='
	<div class="infoheader">' .$firstname. '\'s Connection (<a href="#" onclick="return false" onmousedown="javascript: toggle(\'view_all_friends\'); " style="text-decoration:none; color:#FFFFFF;"> ' .$friendcount. '</a> )</div>';
	
	//creating a variable that will tell us how many items we looped over
	$i=0;
	
	$friendlist.='
	<div class="infobody">
	<table id="friendtable" align="center" cellspacing="4">
	<tr>';
	
	foreach($friend_array6 as $key => $value)
	{
		$i++; //incrementing $i as one each loop pass
		$check_pic='members/' .$value. '/pic.jpg';
		
		if(file_exists($check_pic))
		{
			$friend_pic='<a href="profile.php?id=' .$value.'"><img src="' .$check_pic. '" width="54px" border="1" /></a>';	
		}
		else
		{
			$friend_pic='<a href="profile.php?id=' .$value.'"><img src="members/0/pic.jpg" width="54px" border="1" /></a>';
		}
		
		$sqlName=mysql_query("SELECT firstname FROM myMembers WHERE id='$value' LIMIT 1") or die("Sorry we had mysql error !!");
		while($row=mysql_fetch_array($sqlName))
		{
			$friendFirstName=substr($row['firstname'],0,12);
		}
		if ($i % 6 == 4)
		{
				$friendlist .= '<tr><td><div style="width:56px; height:68px; overflow:hidden;" title="' . $friendFirstName . '">
				<a href="profile.php?id=' . $value . '" id="link1">' . $friendFirstName . '</a><br />' . $friend_pic. '
				</div></td>';  
		} else 
		{
				$friendlist .= '<td><div style="width:56px; height:68px; overflow:hidden;" title="' . $friendFirstName . '">
				<a href="profile.php?id=' . $value . '" id="link1">' . $friendFirstName . '</a><br />' . $friend_pic . '
				</div></td>'; 
		}
		
		
			
	}
	
	 $friendlist .= '</tr></table>
	 <div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggle(\'view_all_friends\');" id="link1">View all</a></div>
	 </div>'; 
	// END ASSEMBLE FRIEND LIST... TO VIEW UP TO 6 ON PROFILE
	
	
	$i = 0;
	$friend_Array50 = array_slice($friend_Array, 0, 50);
	$friendpopboxlist = '<table id="friendpopboxtable" width="100%" align="center" cellpadding="6" cellspacing="0">';
	
	foreach ($friend_Array50 as $key => $value)
	 { 
        $i++; // increment $i by one each loop pass 
		$check_pic = 'members/' . $value . '/pic.jpg';
		    if (file_exists($check_pic)) {
				$friend_pic = '<a href="profile.php?id=' . $value . '"><img src="' . $check_pic . '" width="54px" border="1"/></a>';
		    } else {
				$friend_pic = '<a href="profile.php?id=' . $value . '"><img src="members/0/pic.jpg" width="54px" border="1"/></a> &nbsp;';
		    }
			$sqlName = mysql_query("SELECT firstname, country, state, city FROM myMembers WHERE id='$value' LIMIT 1") or die ("Sorry we had a mysql error!");
			
			while ($row = mysql_fetch_array($sqlName)) 
			{
				 $funame = $row["firstname"]; 
				 $fcountry = $row["country"]; 
				 $fstate = $row["state"]; 
				 $fcity = $row["city"]; 
			}
			
	if ($i % 2) 
	{
					$friendpopboxlist .= '<tr bgcolor="#F4F4F4"><td width="14%" valign="top">
					<div style="width:56px; height:56px; overflow:hidden;" title="' . $funame . '">' . $friend_pic . '</div></td>
				     <td width="86%" valign="top"><a href="profile.php?id=' . $value . '">' . $funame . '</a><br /><font size="-2"><em>' . $fcity . '<br />' . $fstate . '<br />' . $fcountry . '</em></font></td>
				    </tr>';  
				} 
				else 
				{
				    $friendpopboxlist .= '<tr bgcolor="#E0E0E0"><td width="14%" valign="top">
					<div style="width:56px; height:56px; overflow:hidden;" title="' . $funame . '">' . $friend_pic . '</div></td>
				     <td width="86%" valign="top"><a href="profile.php?id=' . $value . '">' . $funame . '</a><br /><font size="-2"><em>' . $fcity . '<br />' . $fstate . '<br />' . $fcountry . '</em></font></td>
				    </tr>';  
				}
				
				} 
	$friendpopboxlist .= '</table>';
	
}//closing if($friend_array!="") 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////Displaying the ip address

//getting the ip address
		$ipaddress=getenv('REMOTE_ADDR');
		$ipdisplay="";
		$conv='';
if(isset($_SESSION['idx']) && $toplinks_id==$id)
{
	$ipdisplay='<div class="infoheader" align="center">
 Your IP Address</div>

<div class="infobody" align="center">
'.$ipaddress.'
</div>
';


//displaying the conversation icon

$conv="<tr>
  <td width='253' height='110' id='icon5' onClick='document.location.href=\"conversation.php\";' style='cursor:pointer;cursor:hand'>
</td></tr>";
}		

?>

<?php 
$message_inbox='';
if(isset($_SESSION['idx']) && $toplinks_id==$id)
{
	$message_inbox='
	<tr>
  <td width="253" height="110" id="inbox_display" align="center" >
  You have '.$letter.' unread messages
</td></tr>
	';
}
?>
<?php 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////CHAT BOX APPLICATION///////////////////////////////////////////



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Profile for <?php echo "$firstname"; ?>" />
<meta name="Keywords" content="<?php echo "$firstname, $city, $state, $country"; ?>" />
<meta name="rating" content="General" />
<meta name="ROBOTS" content="All" />
<title><?php echo $firstname; ?></title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m1{
	background:#590D17;
}
/*_____left column________*/
.photocontainer{
	background:#C66300;
	padding:5px;
	}
.biodata{
	background-image:url(images/menu-wrapper.png);
	padding:5px;
	color:#FFFFFF;
	}
.infoheader{
	background-color:#008200;
	padding:5px;
	font-weight:bold;
	color:#FFFFFF;
}
.infobody{
	background-color:#0066CC;
	padding:5px;
}

.linkmap{
	text-decoration:none;
	color:#FFFFFF;
	}
.linkmap:hover{
	color:#CCCCCC;
	}

/*_________middle column_______________*/
.mainname{
	font-weight:bold;
	color:#FFFFFF;
	font-size:25px;
	}
#google_map{
		
}
#link1{
	text-decoration:none;
	font-weight:bold;
}

/* ------- Interaction Links Class -------- */
.interactiondiv a {
    padding:5px; color:#FFF; font-size:11px; background-image:url(images/menu-wrapper.png); text-decoration:none; font-weight:bold;
}
.interactiondiv a:hover {
	 padding:5px; color:#FFF; font-size:11px; background-image:none; font-weight:bold;
}

/*---- Interaction box----*/
.interactcontainers{
	display:none;
	padding:8px;
	background-color:#BDF;
	border:#999 1px solid;
	}
#interactionresults{
	display:none;
	font-size:16px;
	padding:8px;
	}
	
.button1{
	background-color:#060;
	border:0;
	padding:10px;
	box-shadow:#030;
	border-color:#003300;
	font-weight:bold;
	color:#FFFFFF;
	
}
.button1:hover{
	background-color:#00CC00;
	border:0;
	padding:10px;
	border-color:#003300;
	font-weight:bold;
	color:#000000;
	
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

#icon1{
	background-image:url(images/member_back1icon.png);
}
#icon1:hover{
	background-image:url(images/member_back2icon.png);
}

#icon2{
	background-image:url(images/about1icon.png);
}
#icon2:hover{
	background-image:url(images/abouticon.png);
}

#icon3{
	background-image:url(images/home1icon.png);
}
#icon3:hover{
	background-image:url(images/home2icon.png);
}

#icon4{
	background-image:url(images/forum1icon.png);
}
#icon4:hover{
	background-image:url(images/forum2icon.png);
}

#icon5{
	background-image:url(images/conv1.png);
}
#icon5:hover{
	background-image:url(images/conv2.png);
}

#inbox_display{
	background-color:#FF00CC;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
}
#inbox_display:hover{
	background-color:#C39;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
}



#chatbox {
	
	height:500;
	width:500;
	padding:0px;
	position:fixed;
	bottom:5px;
	right:200px;
	display:;
	z-index:100;
	margin-left:0px;
	
	
	
}
#chats{
	overflow-style:auto;
}


.close{
	text-decoration:none;
	color:#0000FF;
	font-weight:bold;
	
	}
.close:hover{
	text-decoration:none;
	color:#0000FF;
	}

</style>
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" language="javascript">
function toggleviewmap(x)
{
	if ($('#'+x).is(":hidden")) {
			$('#'+x).fadeIn(200);
		} else {
			$('#'+x).fadeOut(200);
		}
}

function toggle(x)
{
	if ($('#'+x).is(":hidden")) {
			$('#'+x).slideDown(200);
		} else {
			$('#'+x).fadeOut(200);
		}
		$('.interactcontainers').hide();
}



</script>
<script type="text/javascript" language="javascript">

//friend adding and removal system

var thisRandNum = "<?php echo $thisRandNum; ?>";
var friendRequestURL="scripts_for_profile/request_as_friend.php";

function addasfriend(a,b)
{
	$("#add_friend_loader").show();
	$.post(friendRequestURL,{request:"requestFriendship", mem1:a, mem2:b, thisWipit:thisRandNum },
	function(data)
		{
			$("#add_friend").html(data).show().fadeOut(12000);
		});	
}



function removeasfriend(a,b)
{
	$("#remove_friend_loader").show();
	$.post(friendRequestURL,{request:"removeFriendship",mem1:a, mem2:b,thisWipit:thisRandNum},function(data)
		{
			$("#remove_friend").html(data).show().fadeOut(12000);	
		});
}


function acceptFriendRequest(x)
{
	$.post(friendRequestURL,{request:"acceptFriend", reqID:x, thisWipit:thisRandNum},function(data)
	{
		$("#req"+x).html(data).show()
		});
}

function denyFriendRequest(x)
{
	$.post(friendRequestURL,{request:"denyFriend", reqID:x, thisWipit:thisRandNum}, function(data)
	{
		$("#req"+x).html(data).show()
		});	
}

//ending friend adding and removal system
//starting private messaging stuff

$('#pmform').submit(function()
	{
	$('input[type=submit]',this).attr('disabled','disabled');
	});
function sendpm(){
	var pmSubject=$('#pmsubject');
	var pmTextArea=$('#pmtextarea');
	var senderName=$('#pm_sender_name');
	var senderId=$('#pm_sender_id');
	var recName=$('#pm_rec_name');
	var recId=$('#pm_rec_id');
	var pmWipit=$('#pmwipit');
	var url = "scripts_for_profile/private_msg_parse.php";
	if(pmSubject.val()=="")
	{
		$('#interactionresults').html('Please type a subject').show().fadeOut(6000);
	}
	else if(pmTextArea.val()=="")
	{
		$('#interactionresults').html('Please type a message').show().fadeOut(6000);
	}
	else
	{
		$("#formprocess").show();
		 $.post(url,{ subject: pmSubject.val(), message: pmTextArea.val(), senderName: senderName.val(), senderId: senderId.val(), rcpntName: recName.val(), rcpntID: recId.val(), thisWipit: pmWipit.val() } ,function(data) 
		 {
			   
			   $("#interactionresults").html(data).show().fadeOut(10000);
			   document.pmform.pmsubject.value='';
			   document.pmform.pmtextarea.value='';
			   $("#formprocess").hide();
			   $('#private_message').slideUp("fast");
           });
	}

}

//////////////////////////Chat application////////////////////////////////////

//FOR ONLINE MEMBERS..................................................................

$(document).ready(function() {
    $("#onlinemembers").load("online.php");
	var refreshid=setInterval(function(){
		$("#onlinemembers").load("online.php");},200);
		$.ajaxSetup({cache:false});
});
</script>

<script type="text/javascript" language="javascript">

/*function showchat()
{
	var mem1=$('#mem1');
	var mem2=$('#mem2');
	var mem2name=$('#mem2firstname');	
	var url="show_chat.php";
	
	var refreshid=setInterval(function()
	{
		$.post(url,{m1:mem1.val,m2:mem2.val,m2name:mem2name.val} ,function(data) 
		 {
			   
			   $("#chats").html(data).show();
			  	
           });
	},200);
	
	var to = document.getElementById('to_mem');
	to.value(oid);
	var from=document.getElementById('from_mem');
	from.value(myid);
	
}

*/

//using AJAX to display conversation...................
function showchat()
{
	var myid=document.forms["form1"]["mem1"].value;
	var oid=document.forms["form1"]["mem2"].value;
	var m2name=document.forms["form1"]["mem2firstname"].value;
	
	yes.m11=myid;
	yes.m22=oid;
	yes.m22name=m2name;	
	yes();

function yes()
{
	
	var xmlhttp; 
	if(window.XMLHttpRequest)
  		{
  			xmlhttp=new XMLHttpRequest();
  		}
		else
  		{
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  		}
  
		xmlhttp.onreadystatechange=function()
  		{
  			if (xmlhttp.readyState==4 && xmlhttp.status==200)
    			{
					document.getElementById("chats").innerHTML=xmlhttp.responseText;
					document.getElementById("mess").innerHTML="Chat to " +yes.m22name;
					
					var to = document.getElementById('to_mem');
					to.value(oid);
					var from=document.getElementById('from_mem');
					from.value(myid);
					
				}
  		}
		xmlhttp.open("GET","show_chat.php?m1="+yes.m11+"&m2="+yes.m22,true);
		xmlhttp.send();

/////////////////////////////////////////////


}

var refresh=setInterval("yes()",5000);
}
/*

//using AJAX to display conversation...................
function showchat()
{
	var myid=document.forms["form1"]["mem1"].value;
	var oid=document.forms["form1"]["mem2"].value;
	var memname=document.forms["form1"]["mem2firstname"].value;
	
	
	
	var xmlhttp; 
	alert("Hello "+myid +oid + memname);
	if(window.XMLHttpRequest)
  		{
  			xmlhttp=new XMLHttpRequest();
  		}
		else
  		{
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  		}
  
		xmlhttp.onreadystatechange=function()
  		{
  			if (xmlhttp.readyState==4 && xmlhttp.status==200)
    			{
					document.getElementById("chats").innerHTML=xmlhttp.responseText;
					document.getElementById("mess").innerHTML="Chat to " +memname;
					
					var to = document.getElementById('to_mem');
					to.value(oid);
					var from=document.getElementById('from_mem');
					from.value(myid);

					setInterval( "showchat()", 5000 );
				}
  		}
		xmlhttp.open("GET","show_chat.php?m1="+myid+"&m2="+oid,true);
		xmlhttp.send();

/////////////////////////////////////////////

}

*/

</script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" id="header" >
  <tr>
    <td width="79%" align="left" style="padding-left:15px;"><span id="logo">clubnitt.org</span></td>
    <td width="39%">
   <?php echo $toplinks; ?>
    </td>
  </tr>
</table>
<table width="88%" align="center" cellpadding="12px" >
<tr>
<td class="column1" width="20%" valign="top">

<div class="photocontainer" align="center">
<?php echo $user_pic; ?>
</div>

<div class="biodata">
<?php echo $bio_body; ?>
</div>
<div class="infoheader">
<?php echo $firstname;?>'s Information</div>

<div class="infobody">
<?php echo $department; ?><br/>
<?php echo $locationinfo; ?>&nbsp; &nbsp; &nbsp;
<a href="#" onclick="return false" onmousedown="javascript:toggleviewmap('google_map');" class="linkmap">Toggle View Map</a>
</div>
<br/>

<?php echo $friendlist; ?>

<div id="view_all_friends" style="display:none;">
        <div class="infoheader">All Conections </div>
         <div class="infoheader" align="right"><a href="#" onclick="return false" onmousedown="javascript:toggle('view_all_friends');" class="linkmap" >Close</a> </div>
        	<div class="infobody" style="height:300px; overflow:auto; overflow-x:hidden;">
        	<?php echo $friendpopboxlist; ?>
         	</div>
            <div style="padding:6px; background-color:#000; border-top:#666 1px solid; font-size:10px; color: #0F0;">
                       &nbsp;
              </div>
</div>




</td>
<td class="column2" width="60%" valign="top">
<span class="mainname"><?php echo "$firstname $lastname";?></span>
<br/><br/>
<?php echo $interactionbox; ?>
<br/>
<!--1st Interaction Box -->
<div class="interactcontainers" id="friend_request">
<div align="right">
<a href="#" onclick="return false" onmousedown="javascript:toggle('friend_request');" id="link1">Close Window</a>&nbsp;&nbsp;
</div>
<h3>The following people are requesting for connection</h3>

 <?php 
    $sql = "SELECT * FROM friends_request WHERE mem2='$id' ORDER BY id ASC LIMIT 50";
	$query = mysql_query($sql) or die ("Sorry we had a mysql error!");
	$num_rows = mysql_num_rows($query); 
	if ($num_rows < 1) {
		echo 'You have no Connection Requests this time.';
	} else {
        while ($row = mysql_fetch_array($query)) { 
		    $requestID = $row["id"];
		    $mem1 = $row["mem1"];
	        $sqlName = mysql_query("SELECT firstname FROM myMembers WHERE id='$mem1' LIMIT 1") or die ("Sorry we had a mysql error!");
		    while ($row = mysql_fetch_array($sqlName)) { $requesterUserName = $row["firstname"]; }
		    ///////  Mechanism to Display Pic. See if they have uploaded a pic or not  //////////////////////////
		    $check_pic = 'members/' . $mem1 . '/pic.jpg';
		    if (file_exists($check_pic)) {
				$lil_pic = '<a href="profile.php?id=' . $mem1 . '"><img src="' . $check_pic . '" width="50px" border="0"/></a>';
		    } else {
				$lil_pic = '<a href="profile.php?id=' . $mem1 . '"><img src="members/0/pic.jpg" width="50px" border="0"/></a>';
		    }
		    echo	'<hr />
<table width="100%" cellpadding="5"><tr><td width="17%" align="left"><div style="overflow:hidden; height:50px;"> ' . $lil_pic . '</div></td>
                        <td width="83%"><a href="profile.php?id=' . $mem1 . '">' . $requesterUserName . '</a> wants to connect with you!<br /><br />
					    <span id="req' . $requestID . '">
					    <a href="#" onclick="return false" onmousedown="javascript:acceptFriendRequest(' . $requestID . ');" id="link1" >Accept</a>
					    &nbsp; &nbsp; OR &nbsp; &nbsp;
					    <a href="#" onclick="return false" onmousedown="javascript:denyFriendRequest(' . $requestID . ');" id="link1">Deny</a>
					    </span></td>
                        </tr>
                       </table>';
        }	 
	}
    ?>




</div>

<!--2nd interaction box-->
<div class="interactcontainers" id="add_friend" >
<div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggle('add_friend');" id="link1">
Cancel</a><br/></div>
Add <?php echo $firstname;?> in your connection list? &nbsp; <a href="#" onclick="return false" onmousedown="javascript:addasfriend(<?php echo $toplinks_id; ?>,<?php echo $id; ?>);" id="link1">Yes</a>
<span id="add_friend_loader" style="display:none">LOADING...</span>
</div>


<!--3rd INTERACTION BOX-->

<div class="interactcontainers" id="remove_friend">
<div align="right"><a href="#" onclick="return false" onmousedown="javascript:toggle('remove_friend');" id="link1">Cancel</a><br/></div>

Remove <?php echo $firstname;?> from your connection list? &nbsp;
<a href="#" onclick="return false" onmousedown="javascript:removeasfriend(<? echo $toplinks_id; ?>,<?php echo $id;?>);" id="link1">Yes</a>
<span id="remove_friend_loader" style="display:none">LOADING...</span>
</div> 

<!--Starting div as the interaction status and only appears when we instruct it to-->
<div id="interactionresults" style="font-size:15px; padding:10px; background-color:#00FF66; font-weight:bold" ></div>
<!--Ending the div-->

<!--4th Interaction box-->
<div class="interactcontainers" id="private_message">
<form action="javascript:sendpm();" name="pmform" id="pmform" method="post">
<font size="+1">Sending message to <strong><em><?php echo $firstname;?></em></strong></font><br/><br/>
Subject:
<input name="pmsubject" id="pmsubject" type="text" maxlength="64" style=" width:98%" />
Message:
<textarea name="pmtextarea" id="pmtextarea" rows="8" maxlength="64" style="width:98%"></textarea>

<input name="pm_sender_id" id="pm_sender_id" type="hidden" value="<? echo $_SESSION['id'];?>" />
<input name="pm_sender_name" id="pm_sender_name" type="hidden" value="<? echo $_SESSION['firstname'] ?>" />
<input name="pm_rec_id" id="pm_rec_id" type="hidden" value="<? echo $id ?>" />
<input name="pm_rec_name" id="pm_rec_name" type="hidden" value="<? echo $firstname; ?>" />
<input name="pmwipit" id="pmwipit" type="hidden" value="<? echo $thisRandNum; ?>" />
<span id="pmstatus" style="color:#F00"></span><br/>
<input name="pmsubmit" type="submit" value="Submit" class="button1"/> OR <a href="#" onclick="return false" onmousedown="javascript:toggle('private_message');" id="link1">Close</a>
<span id="formprocess" style="display:none;">LOADING...</span>
</form>
</div> 










<div id="google_map" width="100%" align="left" >

<div align="right"style="padding:4px; background-color:;"><a href="#" onclick="return false" onmousedown="javascript:toggleviewmap('google_map');"></a></div>
<iframe width="680" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo "$city,+$state,+$country";?>&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo "$city,+$state,+$country";?>&amp;z=12&amp;output=embed"></iframe>
<div align=right"style="padding:4px; background-color:;"><a href="#" onclick="return false" onmousedown="javascript:toggleviewmap('google_map');" id="link1"></a></div>
</div>
<br/>

<?php echo $the_post_form; ?>


<?php echo $postdisplaylist;?>



<div id="chatbox">
<table  border="0" cellspacing="0" cellpadding=0 width="500" height="500" style=" background-color:#009900;">
<tr>
<td width="300" valign="top">

<table width="300" valign="top" align="center"><tr>
<td width="103"><a href="#" onclick="return false" onmousedown="javascript:toggle('chatbox')" class="close"> Close</a>
</td>
<td width="185" align="left">
<span style="font-weight:bold;">CHATBOX</span>
</td>
</tr></table>

<table width="300" valign="top" height="400" style="background-color:#FFFFFF;" cellpadding="2px" border="1" bordercolor="#006600" >
<tr><td valign="top" width="300" height="400" >
<div id="chats" width="300" height="400px" valign="top"></div>
</td></tr>
</table>

<table width="300" valign="top" height="100"  cellpadding="2px" border="0" >
<tr><td valign="top">

<form action="javascript:chat()" method="post">
<input type="text"size="43" name="chattext" />
<input type="submit" value="Send" name="sendbutton" class="button2"/>
<input type="hidden" name="to_mem" id="to_mem" value="" />
<input type="hidden" name="from_mem" id="from_mem" value="" />
</form>

<div id="mess"></div>
</td></tr>
</table>
</td>



<td width="200" valign="top">

<table width="200" valign="top" align="center" style=" background-color:"><tr><td align="center">
<span style="font-weight:bold;">ONLINE MEMBERS</span>
</td></tr></table>

<table width="200" height="507" style="overflow:scroll;background-color:#FFFFFF"><tr>
<td valign="top">
<div id="onlinemembers">

</div>
</td>
</tr></table>

</td>
</tr>
</table>
</div>
</td>

<td class="column3" width="20%" valign="top">
<table width="253" align="center">

<tr>
  <td width="253" height="110" id="icon1" onClick="document.location.href='http://www.clubnitt.org/member_search.php';" style="cursor:pointer;cursor:hand">
</td></tr>

<tr>
  <td width="253" height="110" id="icon4" onClick="document.location.href='http://www.clubnitt.org/forum.php';" style="cursor:pointer;cursor:hand">
</td></tr>


<tr>
  <td width="253" height="110" id="icon3" onClick="document.location.href='http://www.clubnitt.org/index.php';" style="cursor:pointer;cursor:hand">
</td></tr>

<?php echo $conv;?>


<tr>
  <td width="253" height="110" id="icon2" onClick="document.location.href='http://www.clubnitt.org/about.php';" style="cursor:pointer;cursor:hand;">
</td></tr>



</table>
<?php echo "$ipdisplay";?>

</td>
</tr>
</table>



</body>
</html>
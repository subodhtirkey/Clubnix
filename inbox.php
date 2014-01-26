<?php include_once("scripts/checkuserlog.php");?>
<?php 
if(!isset($_SESSION['idx']))
{
	echo 'Your session has timed out.';
	exit();	
}
//decoding the session idx and extracting the user's id from it

$decryptedID=base64_decode($_SESSION['idx']);
$id_array=explode("p3h9xfn8sq03hs2234",$decryptedID);
$my_id=$id_array[1];
$my_uname=$_SESSION['firstname'];

//establishing the interaction token....

$thisRandNum=rand(9999999999999,99999999999999999999);
$_SESSION['wipit']=base64_encode($thisRandNum);
?>
<?php
// Mailbox Parsing for deleting inbox messages
if (isset($_POST['deleteBtn'])) {
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
		if ($key != "deleteBtn") {
		   $sql = mysql_query("UPDATE messages SET recipientDelete='1', opened='1' WHERE id='$value' AND to_id='$my_id' LIMIT 1");
		   // Check to see if sender also removed from sent box, then it is safe to remove completely from system
		}
    }
	header("location: inbox.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INBOX -- CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m3{
	background:#590D17;
}
.button1{
	background-color:#060;
	border:0;
	padding:5px;
	box-shadow:#030;
	border-color:#003300;
	font-weight:bold;
	color:#FFFFFF;
}
.button1:hover{
	background-color:#00CC00;
	border:0;
	padding:5px;
	border-color:#003300;
	font-weight:bold;
	color:#000000;
}
.reply{font-weight:bold; color:#FFFFFF; text-decoration:none; }
.reply:hover{font-weight:bold; color:#000000; text-decoration:none;}
.hiddenDiv{display:none;}
#pmFormProcessGif{display:none;}
.msgDefault{font-weight:bold;}
.msgRead{font-weight:100; color:#666;}

</style>
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script language="javascript" type="text/javascript">
function toggleChecks(field) {
	if (document.myform.toggleAll.checked == true){
		  for (i = 0; i < field.length; i++) {
              field[i].checked = true;
		  }
	} else {
		  for (i = 0; i < field.length; i++) {
              field[i].checked = false;
		  }		
	}
		 
}
</script>
<script language="javascript" type="text/javascript">
$(document).ready(function() { 
$(".toggle").click(function () { 
  if ($(this).next().is(":hidden")) {
	$(".hiddenDiv").hide();
    $(this).next().slideDown("fast"); 
  } else { 
    $(this).next().hide(); 
  } 
}); 
});
function markAsRead(msgID) {
	$.post("scripts_for_profile/markAsRead.php",{ messageid:msgID, ownerid:<?php echo $my_id; ?> } ,function(data) {
		$('#subj_line_'+msgID).addClass('msgRead');
       
   });
}

function toggleReplyBox(subject,sendername,senderid,recName,recID,replyWipit) {
	$("#subjectShow").text(subject);
	$("#recipientShow").text(recName);
	document.replyForm.pmSubject.value = subject;
	document.replyForm.pm_sender_name.value = sendername;
	document.replyForm.pmWipit.value = replyWipit;
	document.replyForm.pm_sender_id.value = senderid;
	document.replyForm.pm_rec_name.value = recName;
	document.replyForm.pm_rec_id.value = recID;
    document.replyForm.replyBtn.value = "Send reply to "+recName;
    if ($('#replyBox').is(":hidden")) {
		  $('#replyBox').fadeIn(1000);
    } else {
		  $('#replyBox').hide();
    }      
}
function processReply () {
	
	  var pmSubject = $("#pmSubject");
	  var pmTextArea = $("#pmTextArea");
	  var sendername = $("#pm_sender_name");
	  var senderid = $("#pm_sender_id");
	  var recName = $("#pm_rec_name");
	  var recID = $("#pm_rec_id");
	  var pm_wipit = $("#pmWipit");
	  var url = "scripts_for_profile/private_msg_parse.php";
      if (pmTextArea.val() == "") {
		   $("#PMStatus").text("Please type in your message.").show().fadeOut(6000);
      } else {
		  $("#pmFormProcessGif").show();
		  $.post(url,{ subject: pmSubject.val(), message: pmTextArea.val(), senderName: sendername.val(), senderId: senderid.val(), rcpntName: recName.val(), rcpntID: recID.val(), thisWipit: pm_wipit.val() } ,  function(data) {
			   document.replyForm.pmTextArea.value = "";
			   $("#pmFormProcessGif").hide();
			   $('#replyBox').slideUp("fast");
			   $("#PMFinal").html("&nbsp; &nbsp;"+data).show().fadeOut(8000);
           });  
	  }
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
<br/>
<!--start outer table1-->
<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#00CC33" ><tr><td>
<!--start header table2-->
<table width="100%" align="center" cellpadding="5px" cellspacing="5px" bgcolor="#0066CC" ><tr><td style="font-weight:bold; color:#ffffff;">Your received messages</td></tr></table>
<!--End header table2-->

<!---------------------start the form--------------------------------------------->
<form name="myform"action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">


<!--starting the table 3-->
<table width="100%" align="center" cellpadding="5" border="0" cellspacing="5"  bgcolor="#00CC33" ><tr>
<td width="10%" valign="top" align="right"><img src="images/arrow.png" width="20" height="20" /></td>
<td width="75%" valign="top" align="left"><input type="submit" name="deleteBtn" id="deleteBtn" value="Delete" class="button1"/></td>
<td width="15%" valign="top" align="center">&nbsp;</td>
</tr></table>
<!--Ending Table3-->
<!--Starting table 4-->
<table width="100%" align="center" cellpadding="5" border="0" cellspacing="5" bgcolor="#006600"><tr>
<td width="10%" valign="top" align="right">
<input name="toggleAll" id="toggleAll" type="checkbox" onclick="toggleChecks(document.myform.cb)" />
</td>
<td width="20%" valign="top" align="left" style="font-weight:bold; color:#FFFFFF;">From </td>
<td width="55%" valign="top" align="left" style="font-weight:bold; color:#FFFFFF;">Subject</td>
<td width="15%" valign="top" align="center" style="font-weight:bold; color:#FFFFFF;">Date</td>
</tr></table>
<!--ending table 4-->

<?php 
//starting the sql query to collect all the messages

$sql = mysql_query("SELECT * FROM messages WHERE to_id='$my_id' AND recipientDelete='0' ORDER BY id DESC LIMIT 100");
while($row= mysql_fetch_array($sql))
{
	$date=strftime("%b %d, %Y", strtotime($row['time_sent']));
	if($row['opened']=="0")
	{
		$textWeight='msgDefault';
	}
	else
	{
		$textWeight='msgRead';
	}
	$fr_id=$row['from_id'];
	//sql_collect username for sender inside loop
	$ret=mysql_query("SELECT id, firstname FROM myMembers WHERE id='$fr_id' LIMIT 1");
	while($raw=mysql_fetch_array($ret))
	{
		$sid=$raw['id'];
		$sname=$raw['firstname'];
	}
	
?>

<!--starting the table 5-->
<table width="100%" align="center" cellpadding="5" border="0" cellspacing="5" bgcolor="#00CC33"><tr>
<td width="10%" valign="top" align="right">
<input type="checkbox" name="cb" id="cb<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>" />
</td>
<td width="20%" valign="top" align="left" ><a href="profile.php?id=<?php echo $sid; ?>" style="text-decoration:none; font-weight:bold;"><?php echo $sname; ?> </a></td>
<td width="55%" valign="top" align="left" > 
<span class="toggle" style="padding:3px;">
<a class="<?php echo $textWeight; ?>" id="subj_line_<?php echo $row['id']; ?>" style="cursor:pointer;" onclick="markAsRead(<?php echo $row['id']; ?>)"><?php echo stripslashes($row['subject']); ?></a>
</span>

<div class="hiddenDiv"> <br />
                <?php echo stripslashes(wordwrap(nl2br($row['message']), 54, "\n", true)); ?>
                <br /><br /><a href="javascript:toggleReplyBox('<?php echo stripslashes($row['subject']); ?>','<?php echo $my_uname; ?>','<?php echo $my_id; ?>','<?php echo $sname; ?>','<?php echo $fr_id; ?>','<?php echo $thisRandNum; ?>')" class="reply">REPLY</a><br />
              </div>


</td>
<td width="15%" valign="top" align="center" ><span style="font-size:10px;"><?php echo $date; ?></span></td>
</tr></table>
<hr style="margin-right:20px; margin-left:20px" />
<!--ending the table 5-->

<?php
} //Closing the while loop
?>


</form> 
<!--------------------End The Form-------------------------------------------------->



<!-- Start Hidden Container the holds the Reply Form -->            
<div id="replyBox" style="display:none; width:680px; height:300px; background-color: #005900; background-repeat:repeat; border: #333 1px solid; top:51px; position:fixed; margin:auto; z-index:50; padding:20px; color:#FFF;">
<div align="right"><a href="javascript:toggleReplyBox('close')"><font color="#00CCFF"><strong>CLOSE</strong></font></a></div>
<h2>Replying to <span style="color:#ABE3FE;" id="recipientShow"></span></h2>
Subject: <strong><span style="color:#ABE3FE;" id="subjectShow"></span></strong> <br>
<form action="javascript:processReply();" name="replyForm" id="replyForm" method="post">
<textarea id="pmTextArea" rows="8" style="width:98%;"></textarea><br />
<input type="hidden" id="pmSubject" />
<input type="hidden" id="pm_rec_id" />
<input type="hidden" id="pm_rec_name" />
<input type="hidden" id="pm_sender_id" />
<input type="hidden" id="pm_sender_name" />
<input type="hidden" id="pmWipit" />
<br />
<input name="replyBtn" type="button" onclick="javascript:processReply()" class="button1"/> &nbsp;&nbsp;&nbsp; <span id="pmFormProcessGif">Loading...</span>
<div id="PMStatus" style="color:#F00; font-size:14px; font-weight:700;">&nbsp;</div>
</form>
</div>
<!-- End Hidden Container the holds the Reply Form -->     
<!-- Start PM Reply Final Message box showing user message status when needed -->    
 <div id="PMFinal" style="display:none; width:652px; background-color:#005900; border:#666 1px solid; top:51px; position:fixed; margin:auto; z-index:50; padding:40px; color:#FFF; font-size:16px;"></div>
 <!-- End PM Reply Final Message box showing user message status when needed --> 


<!--end outer table1--->
</td></tr></table>


</body>
</html>
<?php include_once"scripts/checkuserlog.php";?>
<?php
if (!isset($_SESSION['idx'])) {
echo  '<br /><br />Your session has timed out';
exit(); 
}

$decryptedID = base64_decode($_SESSION['idx']);
$id_array = explode("p3h9xfn8sq03hs2234", $decryptedID);
$my_id = $id_array[1];

//--------Establishing the interaction token---------------------
$thisRandNum = rand(9999999999999,999999999999999999);
$_SESSION['wipit'] = base64_encode($thisRandNum);
//------------Ending the interaction token variable----------- 
?>

<?php
//message parsing for deleting the outbox messages
if (isset($_POST['deleteBtn'])) {
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
		if ($key != "deleteBtn") {
		   $sql = mysql_query("UPDATE messages SET senderDelete='1' WHERE id='$value' AND from_id='$my_id' LIMIT 1");
		   
		}
    }
	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OUTBOX -- CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />

<link rel="stylesheet" href="style.css" />
<script src="js/jquery-1.4.2.js" type="text/javascript"></script>
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
</script>
<style type="text/css">
.m4{
	background:#590D17;
}
.table1{
	font-weight:bold;
	padding:5px;
	
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
.white{
	font-weight:bold;
	color:#FFFFFF;
}
.hiddenDiv{
	display:none
	}
#pmFormProcessGif{
	display:none
	}
.msgDefault {
	font-weight:bold;
	}
.msgRead {
	font-weight:100;color:#666;
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

<table align="center" width="90%" bgcolor="#00CC33" border="0" cellpadding="0" cellspacing="0"><tr><td>
<!--table 1-->
<table width="100%" align="center" cellpadding="5px" cellspacing="5px" bgcolor="#0066CC" border="0" ><tr>
<td style="font-weight:bold; color:#ffffff;">Your sent messages</td></tr></table>
<!--end table 1-->

<!-- Starting the pm form and displaying the message form -->
<form name="myform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

<!--table 2-->
        <table width="100%" align="center" cellpadding="5" border="0" cellspacing="5"  bgcolor="#00CC33" >
          <tr>
            <td width="10%" align="right" valign="top"><img src="images/arrow.png" width="20" height="20" alt=" Messages" /></td>
            <td width="75%" valign="top"><input type="submit" name="deleteBtn" id="deleteBtn" value="Delete" class="button1"/>
              <span id="jsbox" style="display:none"></span>
            </td>
            <td width="15%" ></td>
          </tr>
      </table>
<!--end table 2-->


<!--Starting the table 3-->
<table table width="100%" align="center" cellpadding="5" border="0" cellspacing="5" bgcolor="#006600">
          <tr>
            <td width="10%" valign="top" align="right">
            <input name="toggleAll" id="toggleAll" type="checkbox" onclick="toggleChecks(document.myform.cb)" />
            </td>
            <td width="20%" valign="top" class="white">From</td>
            <td width="55%" valign="top" class="white"><span class="style2">Subject</span></td>
            <td width="15%" valign="top" class="white">Date</td>
          </tr>
        </table> 
<!--Ending the table 3-->


<?php
// SQL to gather their entire messages list_________________________________________________________
$sql = mysql_query("SELECT * FROM messages WHERE from_id='$my_id' AND senderDelete='0' ORDER BY id DESC LIMIT 100");

while($row = mysql_fetch_array($sql)){ 

    $date = strftime("%b %d, %Y",strtotime($row['time_sent']));
    $to_id = $row['to_id'];    
    // SQL - Collect username for Recipient 
    $ret = mysql_query("SELECT id, firstname FROM myMembers WHERE id='$to_id' LIMIT 1");
    while($raw = mysql_fetch_array($ret)){ $Rid = $raw['id']; $Rname = $raw['firstname']; }

?>

 <table width="100%" align="center" cellpadding="5" border="0" cellspacing="5" bgcolor="#00CC33">
          <tr>
            <td width="10%" valign="top" align="right">
            <input type="checkbox" name="cb<?php echo $row['id']; ?>" id="cb" value="<?php echo $row['id']; ?>" />
            </td>
            <td width="20%" valign="top"><a href="profile.php?id=<?php echo $Rid; ?>" style="text-decoration:none; font-weight:bold;"><?php echo $Rname; ?></a></td>
            <td width="55%" valign="top">
              <span class="toggle" style="padding:3px;">
              <a class="msgDefault" id="subj_line_<?php echo $row['id']; ?>" style="cursor:pointer;"><?php echo stripslashes($row['subject']); ?></a>
              </span>
              <div class="hiddenDiv"> <br />
                <?php echo stripslashes(wordwrap(nl2br($row['message']), 54, "\n", true)); ?>
                <br />
              </div>
           </td>
            <td width="15%" valign="top"><span style="font-size:10px;"><?php echo $date; ?></span></td>
          </tr>
        </table>
<hr style="margin-left:20px; margin-right:20px;" />


<?php
}// Close Main while loop
?>
</form>
<!-- Ending the messages for and displayiond the message list-->
</td></tr></table>
</body>
</html>
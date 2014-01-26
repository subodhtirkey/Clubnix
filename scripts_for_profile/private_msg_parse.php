<?php 
session_start();
//error handling and the low level security check
$thisWipit=$_POST['thisWipit'];
$sessWipit=base64_decode($_SESSION['wipit']);
if(!isset($_SESSION['wipit']) || !isset($_SESSION['id']))
{
	echo 'Your session expired from inactivity. Please refresh your browser and continue';
	exit();	
}
//else if session id is set and and is not equal to the posted variable for sender id 
else if($_SESSION['id'] != $_POST['senderId'])
{	echo'Forged Submission1'; exit();	}
//else if session wipit is not equal to the posted wipit variable
else if($sessWipit!=$thisWipit)
{echo 'Forged Submission2'; exit();}

//else if wipit varible is empty

if($sessWipit=="" || $thisWipit="")
{echo 'Mising data'; exit();}

//connectig to the database

require_once "../scripts/connect_to_mysql.php"; 


//Preventing double posting
$checkuser_id=$_POST['senderId'];
$prevent_dp=mysql_query("SELECT id FROM messages WHERE from_id='$checkuser_id' AND time_sent between subtime(now(),'0:0:20') and now()");
$nr=mysql_num_rows($prevent_dp);
if($nr>0)
{
	echo'You must wait 20 seconds between your private message ';
	exit();	
}

//preventing more than 30 messages in one day from a single user

$sql=mysql_query("SELECT id FROM messages WHERE from_id='$checkuser_id' AND DATE(time_sent)=DATE(NOW()) LIIMIT 40");
$numRows=mysql_query($sql);
if($numRows>30)
{
	echo'You can only send 30 private messages per day';
	exit();
}
//______________________Parsing the message________
//process the message
if(isset($_POST['message']))
{
$to	=preg_replace('#[^0-9]#i','',$_POST['rcpntID']);
$from	=preg_replace('#[^0-9]#i','',$_POST['senderId']);
$sub=htmlspecialchars($_POST['subject']);
$msg=htmlspecialchars($_POST['message']);
$sub=mysql_real_escape_string($sub);
$msg=mysql_real_escape_string($msg);

//handle all pm from specific errors checking

if(empty($to) || empty($to) || empty($from) || empty($msg))
{	
	echo 'Missing data to continue';
	exit();
}
else {
		//delete the message residing at the tail end of their list so they cannot archive more than 30 messages
		$sqldeleteTail=mysql_query("SELECT * FROM messages WHERE to_id='$id' ORDER BY time_sent DESC LIMIT 0,100 ");
		$dci=1;
		while($row=mysql_fetch_array($sqldeleteTail))
		{
			$pm_id=$row['id'];
			if($dci>99)
			{
				$deleteTail=mysql_query("DELETE FROM messages WHERE id='$pm_id'");
			}
			$dci++;
		}
	
//insert the data into your table now
$sql="INSERT INTO messages (to_id,from_id,time_sent,subject,message) VALUES ('$to','$from',now(),'$sub','$msg')";
if(!mysql_query($sql))
{
	echo 'Could Not send message';
	exit();
}
else{
	echo'<img src="../images/tick.png" height="20" width="20" />  Message sent successfully';
	}
}
}

?>
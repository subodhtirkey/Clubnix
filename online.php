<?php
session_start();
error_reporting('E_ALL');
ini_set('display_errors', '1');

require"scripts/connect_to_mysql.php";

//initializing some varaible
$id = "";
$username = "";
$friend_array = "";
$online = "";
$online_array = "";
$friendList = "";
$toplinks_id="";


//if the session idx is set
if (isset($_SESSION['idx'])) {

 $decryptedID = base64_decode($_SESSION['idx']);
 $id_array = explode("p3h9xfn8sq03hs2234", $decryptedID);
 $toplinks_id = $id_array[1];
 $toplinks_firstname = $_SESSION['firstname'];
 
 
 ////////// Update Last Login Date Field       ////////////////////////////////////////////
 mysql_query("UPDATE myMembers SET online='1', last_log_date=now() WHERE id='$toplinks_id'"); 

//geting the users id who are login i.e whose online field is set to one

$sql=mysql_query("SELECT * FROM myMembers WHERE online='1' AND id!='$toplinks_id'");
$numRows=mysql_num_rows($sql);
if($numRows<1)
{
	$online_members="No one is online now ";	
}
else
{
	while($row=mysql_fetch_array($sql))
	{
	$id_mem=$row['id'];
	$firstname_mem=$row['firstname'];
	
	//picture adding mechanism....................
	$userpic="members/$id_mem/pic.jpg";
	$defaultpic="members/0/pic.jpg";
	
	if(file_exists($userpic))
	{
		$mempic="<img src='" .$userpic. "' width='50px' style='overflow:hidden;'/>";
	}
	else
	{
		$mempic="<img src='" .$defaultpic. "' width='50px'  />";
	}
	
	$online_members.='<table width="200" cellpadding="0" cellspacing="0" border="1">
	<tr>
	<td valign="middle" width="50">' .$mempic. '</td>
	<td  align="center">  ' .$firstname_mem. '</td>
	<td align="left" width="20">
	
	<form  name="form1" id="form1" method="post">
	<input type="hidden" name="mem1" id="mem1" value="' .$toplinks_id. '" />
	<input type="hidden" name="mem2" id="mem2" value="' .$id_mem. '" />
	<input type="hidden" name="mem2firstname" id="mem2firstname" value="' .$firstname_mem. '" />
	<input type="button" value="Chat" class="button2" onclick="showchat()"/>
	</form>
	
	</td>
	<tr>
	</table>';
	
	}
}//closing else

}//closing if statement


mysql_query("UPDATE myMembers SET online='0' WHERE last_log_date<SUBTIME(NOW(),'0 0:2:0')");

echo $online_members;

?>
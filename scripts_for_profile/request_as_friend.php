<?php
session_start();
$thisWipit=$_POST['thisWipit'];
$sessWipit=base64_decode($_SESSION['wipit']);
//
if(!isset($_SESSION['wipit']) || !isset($_SESSION['idx']))
{
	echo 'Error: Your Session expired from inactivity. Please refresh your browser and continue.';
	exit();	
}

//
if($sessWipit != $thisWipit)
{
	echo'Forged submission';
	exit();
	
}

//
if ($thisWipit=="")
{
	echo 'Missing Data';
	exit();
		
}

//connecting to the database.........
include_once"../scripts/connect_to_mysql.php";
//_____Part 1_______________________________________________________________________________________________________


if($_POST['request']=='requestFriendship')
{
	
	$mem1=preg_replace('#[^0-9]#i','',$_POST['mem1']);
	$mem2=preg_replace('#[^0-9]#i','',$_POST['mem2']);
	//
	if((!$mem1) || (!$mem2) || (!$thisWipit) )
	{
		echo 'Error: Missing Data for mem1 and mem2';
		exit();	
	}
	
	//
	if($mem1 == $mem2)
	{
		echo 'Error:You cannot add yourself as a friend';
		exit();
	}
	
	$sql_frnd_array_mem1=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem1' LIMIT 1");
	while($row=mysql_fetch_array($sql_frnd_array_mem1))
	{
		$frnd_array_mem1=$row['friend_array'];
	}
	$friendArrayMem1=explode(",",$frnd_array_mem1);
	
	if(in_array($mem2,$friendArrayMem1))
	{
		echo'This member is already your friend.';
		exit();
	}
	
	
	$sql_frnd_array_mem2=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem2' LIMIT 1");
	while($row=mysql_fetch_array($sql_frnd_array_mem2))
	{
		$frnd_array_mem2=$row['friend_array'];
	}
	$friendArrayMem2=explode(",",$frnd_array_mem2);
	
	if(in_array($mem1,$friendArrayMem2))
	{
		echo'This member is already your friend.';
		exit();
	}
	
$sql=mysql_query("SELECT id FROM friends_request WHERE mem1='$mem1' AND mem2='$mem2' LIMIT 1");

$numRows=mysql_num_rows($sql);

if($numRows>0)
{
	echo'You have Connection Request pending for this member';
	exit();	
}	
	
	
$sql=mysql_query("SELECT id FROM friends_request WHERE mem1='$mem2' AND mem2='$mem1' LIMIT 1");
$numRows=mysql_num_rows($sql);

if($numRows>0)
{	
	echo'The User has requested you as a friend already. Check the request on your profile';
	exit();
}

//when everything is fine update the database

$sql=mysql_query("INSERT INTO friends_request (mem1, mem2 ,timedate) VALUES ('$mem1','$mem2',now())") or die(mysql_error("Friend Request Insertion Error"));

echo'<img src="../images/tick.png" height="20" width="20" alt="tick" />  Connection Request Sent Successfully. This Member must approve the request.';
exit();

}

//_____Part 2_______________________________________________________________________________________________________


if($_POST['request']=='acceptFriend')
{
	$reqID=preg_replace('#[^0-9]#i','',$_POST['reqID']);
	$sql="SELECT * FROM friends_request WHERE id='$reqID' LIMIT 1";
	$query=mysql_query($sql) or die ('We had a mysql error!');
	$num_rows=mysql_num_rows($query);
	if($num_rows<1)
	{
		echo 'An Error Occured';
		exit();
	}
	while($row=mysql_fetch_array($query))
	{
		$mem1=$row['mem1'];
		$mem2=$row['mem2'];
	}
	
	$sql_frnd_array_mem1=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem1' LIMIT 1");
	$sql_frnd_array_mem2=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem2' LIMIT 1");
	while($row=mysql_fetch_array($sql_frnd_array_mem1))
	{
		$frnd_array_mem1=$row['friend_array'];	
	}
	
	while($row=mysql_fetch_array($sql_frnd_array_mem2))
	{
		$frnd_array_mem2=$row['friend_array'];	
	}
	$friendArrayMem1=explode(",",$frnd_array_mem1);
	$friendArrayMem2=explode(",",$frnd_array_mem2);
	
	if(in_array($mem2,$friendArrayMem1))
	{
		echo'This Member is already your friend';
		exit();
	}
	
	if(in_array($mem1,$friendArrayMem2))
	{
		echo'This Member is already your friend';
		exit();
	}
	
	if($frnd_array_mem1 !="")
	{
		$frnd_array_mem1="$frnd_array_mem1,$mem2";
	}
	else
	{
		$frnd_array_mem1="$mem2";
	}
	
	if($frnd_array_mem2 !="")
	{
		$frnd_array_mem2="$frnd_array_mem2,$mem1";
	}
	else
	{
		$frnd_array_mem2="$mem1";
	}
	
	$updatearraymem1=mysql_query("UPDATE myMembers SET friend_array='$frnd_array_mem1' WHERE id='$mem1'") or die(mysql_error());
	$updatearraymem2=mysql_query("UPDATE myMembers SET friend_array='$frnd_array_mem2' WHERE id='$mem2'") or die(mysql_error());
	
	$deleteThisPendingRequest=mysql_query("DELETE FROM friends_request WHERE id='$reqID' LIMIT 1");
	
	echo'You are now friends with this member !';
	exit();
	
	
}

//_____Part 3_______________________________________________________________________________________________________

if($_POST['request']=='denyFriend')
{
	$reqID=preg_replace('#[^0-9]#i','',$_POST['reqID']);
	$deleteThisPendingRequest=mysql_query("DELETE FROM friends_request WHERE id='$reqID' LIMIT 1");
	echo'Request Denied'; 
}

//_____Part 4_______________________________________________________________________________________________________

if($_POST['request']=='removeFriendship')
{
	
	$mem1=preg_replace('#[^0-9]#i','',$_POST['mem1']);
	$mem2=preg_replace('#[^0-9]#i','',$_POST['mem2']);
	//
	if(!$mem1 || !$mem2 || !$thisWipit )
	{
		echo 'Error: Missing Data';	
		exit();
	}
	
	$decryptedID=base64_decode($_SESSION['idx']);
	$id_array=explode("p3h9xfn8sq03hs2234",$decryptedID);
	$mem1sessIDX=$id_array[1];
	if($mem1sessIDX !=$mem1)
	{
		exit();
	}
	//querying mem1 and mem2 friend_array out of database
	
	$sql_frnd_array_mem1=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem1' LIMIT 1");
	$sql_frnd_array_mem2=mysql_query("SELECT friend_array FROM myMembers WHERE id='$mem2' LIMIT 1");
	while($row=mysql_fetch_array($sql_frnd_array_mem1))
	{
		$frnd_array_mem1=$row['friend_array'];
	}
		while($row=mysql_fetch_array($sql_frnd_array_mem2))
	{
		$frnd_array_mem2=$row['friend_array'];
	}
	
	//Checking to see infact they are each others friend
	$friendArrayMem1=explode(",",$frnd_array_mem1);
	$friendArrayMem2=explode(",",$frnd_array_mem2);
	if(!in_array($mem2,$friendArrayMem1))
	{
		echo'This Member is not in your list';
		exit();
	}
	if(!in_array($mem1,$friendArrayMem2))
	{
		echo'This Member is not in your list';
		exit();
	}
	
	//here we remove them from each other array using unset on the key where value is found
	
	foreach($friendArrayMem1 as $key => $value)
	{
		if($value==$mem2)
		{
			unset($friendArrayMem1[$key]);
		}
	}
	
	foreach($friendArrayMem2 as $key => $value)
	{
		if($value==$mem1)
		{
			unset($friendArrayMem2[$key]);
		}
	}
	
	//now implode the adjusted arrays to make them strings again before going to the database
	$newstringforMem1=implode(",",$friendArrayMem1);
	$newstringforMem2=implode(",",$friendArrayMem2);
	//and now updationg the database
	$sql1=mysql_query("UPDATE myMembers SET friend_array='$newstringforMem1' WHERE id='$mem1'");
	$sql2=mysql_query("UPDATE myMembers SET friend_array='$newstringforMem2' WHERE id='$mem2'");
	echo'You are no longer friends with the member';
}
?>
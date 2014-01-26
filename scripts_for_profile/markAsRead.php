<?php 
session_start();
require_once".../scripts/connect_to_mysql.php";
$messageid=preg_replace('#[^0-9]#i','',$_POST['messageid']);
$ownerid=preg_replace('#[^0-9]#i','',$_POST['ownerid']);

//decoding the session idx variable and extract the user's id from it

$decryptedID=base64_decode($_SESSION['idx']);
$id_array=explode("p3h9xfn8sq03hs2234",$decrypted);
$my_id=$id_array[1];
if($ownerid !=$my_id)
{
	exit();
}
else
{
	mysql_query("UPDATE messages SET opened='1' WHERE id='$messageid' LIMIT 1");	
}
?>
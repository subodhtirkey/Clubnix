<?php
session_start();
error_reporting(E_ALL);
ini_set('dispaly-errors','1');

//Unsetting all the session variable

$_SESSION = array();


/////////////////////////////////////////////////////////
if(isset($_COOKIE['idCookie']))
{
	setcookie("idCookie","",time()-42000,'/');
	setcookie("passCookie","",time()-42000,'/');
}

//Destroying the session variable

session_destroy();

//Checking if the session variable is destroyed or not

if(!session_is_registered('firstname'))
{
		header("location:index.php");	
}
else{
		print"<h2>Could Not Log Out</h2>";
	}
?>
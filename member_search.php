<?php include_once"scripts/checkuserlog.php" ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
//CONNECTINHG TO THE DATABASE
include_once"scripts/connect_to_mysql.php";

if(isset($_POST['search']) == "")
{
//default query string
$querystring="WHERE email_activated='1' ORDER BY id ASC";
//QUERY MESAGE
$querymessage="Showing Members Oldest To Newest";
}

else if($_POST['search'] == "newest_members")
{
	$querystring="WHERE email_activated='1' ORDER BY id DESC";
	$querymessage="Showing Newest To Oldest Members";	
}
else if($_POST['search'] == "by_name")
{
	$name=$_POST['name'];
	$name=stripslashes($name);
	$name=strip_tags($name);
	$name=eregi_replace("`","",$name);
	$name=mysql_real_escape_string($name);
	$querystring="WHERE firstname LIKE '%$name%' AND email_activated='1'";
	$querymessage="Showing Members With The Name You Search For";
}
else if($_POST['search'] == "dep_search")
{
	$depart=$_POST['department'];
	$departt=preg_replace('#[^a-z]#i','',$depart);
	$querystring="WHERE email_activated='1' AND department='$depart'";
	$querymessage="Showing Members By Department You Selected For";	
}

//Now Querying The Member

$sql=mysql_query("SELECT id,firstname,lastname,department,country FROM myMembers $querystring");
//Output Section Buiding

$outputlist='';
while($row=mysql_fetch_array($sql))
{
$id=$row['id'];
$fname=$row['firstname'];
$lname=$row['lastname'];
$dep=$row['department'];
$country=$row['country'];

//Mechanism to display pic

$check_pic="members/$id/pic.jpg";
$default_pic="members/0/pic.jpg";

if(file_exists($check_pic))
{ $user_pic="<img src=\"$check_pic\" width=\"120px\ border=\"0\" />";}
else
{ $user_pic="<img src=\"$default_pic\" width=\"120px\ border=\"0\" />";}

//Displaying The members

$outputlist.='
	<table width="100%" bgcolor="#BDF">
                  <tr>
                    <td width="23%" rowspan="3"><div style=" height:120px; overflow:hidden;"><a href="http://'.$dyn_www.'/profile.php?id=' . $id . '">' . $user_pic . '</a></div></td>
                    <td width="14%" class="style7"><div align="right">Name:</div></td>
                    <td width="63%"><a href="http://'.$dyn_www.'/profile.php?id=' . $id . '" target="_blank">' . $fname . ' ' .$lname. '</a> </td>
                  </tr>

                  <tr>
                    <td class="style7"><div align="right">Department:</div></td>
                    <td>' . $dep . ' </td>
                  </tr>
                  <tr>
                    <td class="style7"><div align="right">Country:</div></td>
                    <td>' .$country. '</td>
                  </tr>
                  </table>
				  <hr />

';
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MEMBERS -- CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />

<link rel="stylesheet" href="style.css" />
<style type="text/css">
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
<br />
<table width="90%" cellpadding="8"  align="center" cellspacing="12" bgcolor="#0000FF">
<tr>
<td align="center">
<form action="member_search.php" method="post" enctype="multipart/form-data" name="form1">
<span class="color">Browse Newest Members  </span>
<input type="submit" value="  GO  " name="button1" class="button2"/>
<input type="hidden" name="search" value="newest_members" />
</form>
</td>
<td align="center">
<form action="member_search.php" method="post" enctype="multipart/form-data" name="form2">
<span class="color">Browse By Name </span>
<input type="text" size="30" name="name" class="name" id="name"/>
<input type="submit" value="  GO  " name="button2" class="button2"/>
<input type="hidden" name="search" value="by_name" />
</form>
</td>
<td align="center">
<form action="member_search.php" method="post" enctype="multipart/form-data" name="form3">
<span class="color">Browse By Department</span>
<select name="department" id="department" title="" class="required">
<option value="cse" selected="selected" >CSE</option>
<option value="ece" >ECE</option>
<option value="eee" >EEE</option>
<option value="archi" >Architecture</option>
<option value="chem" >Chemical</option>
<option value="civil" >Civil</option>
<option value="mca" >MCA</option>
<option value="ice" >ICE</option>
<option value="mech" >Mechanical</option>
<option value="meta" >MME</option>
<option value="prod" >Production</option>
</select>
<input type="submit" value="  GO  " name="button 3" class="button2"/>
<input type="hidden" name="search" value="dep_search" />
</form>
</td>
</tr>
</table>

<table width="90%" cellpadding="3px" align="center" bgcolor="#009900">
<tr>
<td>
<table width="70%" height="500px" cellpadding="3px" align="center" background="images/del.jpg">
<tr>
<td>
<div style="width:100%; height:500px; overflow:scroll; overflow-x:hidden; padding:5px">
<?php echo "$querymessage"; ?><br /><br />
<?php echo "$outputlist"; ?>
</div>
</td>
</tr>
</table>
</td>
</tr>

</table>







</body>
</html>
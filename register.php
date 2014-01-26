<?php 
include_once ("scripts/checkuserlog.php");
?>
<?php 
$from="";
//This Code Only runs when username is posted

if(isset($_POST['firstname']))
{
	$firstname=$_POST['firstname'];
	$lastname=$_POST['lastname'];
	$gender=$_POST['gender'];
	$b_m=$_POST['birth_month'];	
	$b_d=$_POST['birth_day'];
	$b_y=$_POST['birth_year'];
	$department=$_POST['department'];
	$country=$_POST['country'];
	$state=$_POST['state'];
	$city=$_POST['city'];
	$zip=$_POST['zip'];
	$email1=$_POST['email1'];
	$email2=$_POST['email2'];
	$pass1=$_POST['pass1'];
	$pass2=$_POST['pass2'];
	$about=$_POST['about'];
//Adding security....
	//for firstname  and last name
	$firstname=preg_replace('#[^A-Za-z0-9]#i','',$firstname);
	$lastname=preg_replace('#[^A-Za-z0-9]#i','',$lastname);
	//for gender
	$gender=preg_replace('#[^a-z]#i','',$gender);
	
	//For date of birth
	
	$b_m=preg_replace('#[^0-9]#i','',$b_m);
	$b_d=preg_replace('#[^0-9]#i','',$b_d);
	$b_y=preg_replace('#[^0-9]#i','',$b_y);
	
	//for department......
	
	$department=preg_replace('#[^A-Z]#i','',$department);
	
	//for location
	$country=preg_replace('#[^A-Za-z]#i','',$country);
	$state=preg_replace('#[^A-Za-z]#i','',$state);
	$city=preg_replace('#[^A-Za-z]#i','',$city);
	$zip=preg_replace('#[^A-Za-z0-9]#i','',$zip);
	
	//for email
	$email1=stripslashes($email1);
	$email1=strip_tags($email1);
	
	$email2=stripslashes($email2);
	$email2=strip_tags($email2);
	
	//for password
	
	$pass1=stripslashes($pass1);
	$pass1=strip_tags($pass1);
	
	
	$pass2=stripslashes($pass2);
	$pass2=strip_tags($pass2);
	
	//for about
	
	
	 	 $about = stripslashes($about);
	 	 $about = strip_tags($about);
	 	 $about = mysql_real_escape_string($about);
	 	 $about = str_replace("'", "&#39;", $about);
		 $about=str_replace("`", "&#39;", $about);
		 $about=nl2br(htmlspecialchars($about));
	
	//connecting to the database
	
	include_once"scripts/connect_to_mysql.php";
	$email_checker=mysql_real_escape_string($email1);
	$email_checker=str_replace('`','',$email_checker);
	
	//database duploicate email check
	
	$sql_email_check=mysql_query("SELECT email FROM myMembers WHERE email='$email_checker'");

	$email_check=mysql_num_rows($sql_email_check);
	
	//Error handling for missing data
	
	if((!$firstname)||(!$lastname)||(!$gender)||(!$b_m)||(!$b_d)||(!$b_y)||(!$department)||(!$country)||(!$state)||(!$city)||(!$zip)||(!$email1)||(!$email2)||(!$pass1)||(!$pass2)||(!$about))
	{
		$errorMsg="Error: You did not submit the following required information..<br/><br/>";
		
		if(!$firstname)
		{$errorMsg.="*Firstname<br/>";}
		if(!$lastname)
		{$errorMsg.="*Lastname<br/>";}
		if(!$gender)
		{$errorMsg.="*Gender<br/>";}
		if(!$b_m)
		{$errorMsg.="*Birth Date Month<br/>";}
		if(!$b_d)
		{$errorMsg.="*Birth Date Day<br/>";}
		if(!$b_y)
		{$errorMsg.="*Birth Date Year<br/>";}
		if(!$department)
		{$errorMsg.="*Department<br/>";}
		if(!$country)
		{$errorMsg.="*Country<br/>";}
		if(!$state)
		{$errorMsg.="*State<br/>";}
		if(!$city)
		{$errorMsg.="*City<br/>";}
		if(!$zip)
		{$errorMsg.="*Zip/Pincode<br/>";}
		if(!$email1)
		{$errorMsg.="*Email Address<br/>";}
		if(!$email2)
		{$errorMsg.="*Confirm Email Address<br/>";}
		if(!$pass1)
		{$errorMsg.="*Password<br/>";}
		if(!$pass2)
		{$errorMsg.="*Confirm Password<br/>";}
		if(!$about)
		{$errorMsg.="*About You<br/>";}
		
		}//ending error handling
		
		else if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email1))//validating email by regular expression
	{ 
	$errorMsg="Error:Your email is not valid</br>";
	}
	else if($email1 != $email2)
	{	
		$errorMsg="Error: Your Email Fields Below Do Not Match</br>";
	}	
	else if($pass1 != $pass2)
	{	
		$errorMsg="Error: Your Passwords Fields Below Do Not Match<br/>";
	}	
	else if(strlen($pass1)<3)
	{	
		$errorMsg="Error: Your Password is too small<br/>";
	}	
	else if($email_check >0)
	{	
		$errorMsg="Error: Your Email Address Is Already In Use Inside Our System<br/>";
	}
	else
	{
		$email1=mysql_real_escape_string($email1);
		$pass1=mysql_real_escape_string($pass1);
		
		//hashing the paswsword.......
		
		function enc($string)
		{
			$salt="@x2p";
			$hash=sha1(md5($salt.$string).md5($string).sha1(md5(md5($string))));
			return $hash;
		}
		
		$db_pass=enc($pass1);
		
		//converting birthday to date format
		
		$full_birthday="$b_y-$b_m-$b_d";
		
		//getting the ip address
		$ipaddress=getenv('REMOTE_ADDR');
		
		//Adding the user information into the database table
		
		$sql=mysql_query("INSERT INTO myMembers(firstname,lastname,gender,birthday,department,country,state,city,zip,email,password,ipaddress,bio_body,sign_up_date)
		VALUES('$firstname','$lastname','$gender','$full_birthday','$department','$country','$state','$city','$zip','$email1','$db_pass','$ipaddress','$about',now())") or die(mysql_error());
		
		$id=mysql_insert_id();
		//Creating folder
		
		mkdir("members/$id",0755);
		
		//Emailing User The Activation Link
		
		$to="$email1";
		$headers ="From: webmaster@clubnitt.org\n";
                $headers .= "MIME-Version: 1.0\n";
                $headers .= "Content-type: text/html\n";
                $subject ="COMPLETE YOUR clubnitt.org registration";
				
				 $message="<div align=center><br>----------------------------- New Login Password --------------------------------<br><br><br>
				 
				 Hi $firstname<br/>
			Click on the link to activate your account or copy and paste the complete url<br/>
			http://$dyn_www/activation.php?id=$id&sequence=$db_pass  <br/>
			Your Account:<br/> 
			emailid: $email1 <br/>
			password: $pass1
              
				</div>";
		

		
		//finally sending mail
		mail($to,$subject,$message,$headers);
		$msgToUser="Hi $firstname <br/>
		Activation link has been send to your email. Please check Your email....";
		include_once"msgToUser.php";
		exit();		
		}
}
else{
		//If form is not posted put the defaul entry variable
	$errorMsg="";	
	$firstname="";
	$lastname="";
	$gender="";
	$b_m="";	
	$b_d="";
	$b_y="";
	$department="";
	$country="";
	$state="";
	$city="";
	$zip="";
	$email1="";
	$email2="";
	$pass1="";
	$pass2="";
	$about="";
		
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REGISTER--CLUBNITT</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
.m2{
	background:#590D17;
}

.formFields{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	
	}
	
.formfield{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	
	}
.required{
	background-color:#96F;
	padding:5px;
	color:#FFFFFF;
	font-weight:bold;
	
	}
.button123{
	display:block;
	background-color:#09C;
	color:#FFF;
	padding:10px;
	border:0;
	
	}
	
.button123:hover{
	display:block;
	background-color:#0033CC;
	color:#FFF;
	padding:10px;
	border:0;
}
</style>
<script type="text/javascript" src="js/jquery-1.4.2.js">
</script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	
	$('#messbox').fadeOut(10000);
    
});

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
<table width="90%" align="center" bgcolor="#006600" cellspacing="5px" cellpadding="5px">
<tr>
<td>
<p align="center" class="big">Create Your account Here</p>
</td>
</tr>
<tr>
<td colspan="2">
<p id="messbox" class="color3"><?php echo"$errorMsg";?></p>
</td>
</tr>
</table>
<table align="center" width="90%" border="0" cellpadding="8px" cellspacing="0">
<tr>
<td bgcolor="#009900">

<!--form starts from here-->
<table width="600px"  cellpadding="3" align="center" bgcolor="#009900">
<form action="register.php" enctype="multipart/form-data" name="myForm" method="post">
  <tr>
    <td><span class="color">Firstname:</span></td>
    <td><input type="text" name="firstname" class="formfield" id="firstname" size="40" value="<?php echo"$firstname";?>" />
     &nbsp;</td>
  </tr>
  <tr>
    <td><span class="color">Lastname:</span></td>
    <td><p>
      <input type="text" name="lastname" class="formfield" id="lastname" size="40" value="<?php echo"$lastname";?>" />
    &nbsp;</td>
  </tr>
  <tr>
    <td><span class="color">Gender:</span></td>
    <td><input type="radio" name="gender" id="gender" value="m" checked="checked"/><span class="color2">Male</span>&nbsp;
      <input type="radio" name="gender" id="gender" value="f"/><span class="color2">Female</span>
    </td>
  </tr>
  <tr>
    <td><span class="color">Date Of Birth:</span></td>
    <td>
    <select name="birth_month" class="formFields" id="birth_month">
<option value="<?php print "$b_m"; ?>"><?php print "$b_m"; ?></option>
<option value="01" selected="selected">January</option>
<option value="02">February</option>
<option value="03">March</option>
<option value="04">April</option>
<option value="05">May</option>
<option value="06">June</option>
<option value="07">July</option>
<option value="08">August</option>
<option value="09">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>
</select>

<select name="birth_day" class="formFields" id="birth_day">
<option value="<?php print "$b_d"; ?>"><?php print "$b_d"; ?></option>
<option value="01" selected="selected">1</option>
<option value="02">2</option>
<option value="03">3</option>
<option value="04">4</option>
<option value="05">5</option>
<option value="06">6</option>
<option value="07">7</option>
<option value="08">8</option>
<option value="09">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select>

<select name="birth_year" class="formFields" id="birth_year">
<option value="<?php print "$b_y"; ?>"><?php print "$b_y"; ?></option>
<option value="2010" selected="selected">2010</option>
<option value="2009">2009</option>
<option value="2008">2008</option>
<option value="2007">2007</option>
<option value="2006">2006</option>
<option value="2005">2005</option>
<option value="2004">2004</option>
<option value="2003">2003</option>
<option value="2002">2002</option>
<option value="2001">2001</option>
<option value="2000">2000</option>
<option value="1999">1999</option>
<option value="1998">1998</option>
<option value="1997">1997</option>
<option value="1996">1996</option>
<option value="1995">1995</option>
<option value="1994">1994</option>
<option value="1993">1993</option>
<option value="1992">1992</option>
<option value="1991">1991</option>
<option value="1990">1990</option>
<option value="1989">1989</option>
<option value="1988">1988</option>
<option value="1987">1987</option>
<option value="1986">1986</option>
<option value="1985">1985</option>
<option value="1984">1984</option>
<option value="1983">1983</option>
<option value="1982">1982</option>
<option value="1981">1981</option>
<option value="1980">1980</option>
<option value="1979">1979</option>
<option value="1978">1978</option>
<option value="1977">1977</option>
<option value="1976">1976</option>
<option value="1975">1975</option>
<option value="1974">1974</option>
<option value="1973">1973</option>
<option value="1972">1972</option>
<option value="1971">1971</option>
<option value="1970">1970</option>
<option value="1969">1969</option>
<option value="1968">1968</option>
<option value="1967">1967</option>
<option value="1966">1966</option>
<option value="1965">1965</option>
<option value="1964">1964</option>
<option value="1963">1963</option>
<option value="1962">1962</option>
<option value="1961">1961</option>
<option value="1960">1960</option>
<option value="1959">1959</option>
<option value="1958">1958</option>
<option value="1957">1957</option>
<option value="1956">1956</option>
<option value="1955">1955</option>
<option value="1954">1954</option>
<option value="1953">1953</option>
<option value="1952">1952</option>
<option value="1951">1951</option>
<option value="1950">1950</option>
<option value="1949">1949</option>
<option value="1948">1948</option>
<option value="1947">1947</option>
<option value="1946">1946</option>
<option value="1945">1945</option>
<option value="1944">1944</option>
<option value="1943">1943</option>
<option value="1942">1942</option>
<option value="1941">1941</option>
<option value="1940">1940</option>
<option value="1939">1939</option>
<option value="1938">1938</option>
<option value="1937">1937</option>
<option value="1936">1936</option>
<option value="1935">1935</option>
<option value="1934">1934</option>
<option value="1933">1933</option>
<option value="1932">1932</option>
<option value="1931">1931</option>
<option value="1930">1930</option>
<option value="1929">1929</option>
<option value="1928">1928</option>
<option value="1927">1927</option>
<option value="1926">1926</option>
<option value="1925">1925</option>
<option value="1924">1924</option>
<option value="1923">1923</option>
<option value="1922">1922</option>
<option value="1921">1921</option>
<option value="1920">1920</option>
<option value="1919">1919</option>
<option value="1918">1918</option>
<option value="1917">1917</option>
<option value="1916">1916</option>
<option value="1915">1915</option>
<option value="1914">1914</option>
<option value="1913">1913</option>
<option value="1912">1912</option>
<option value="1911">1911</option>
<option value="1910">1910</option>
<option value="1909">1909</option>
<option value="1908">1908</option>
<option value="1907">1907</option>
<option value="1906">1906</option>
<option value="1905">1905</option>
<option value="1904">1904</option>
<option value="1903">1903</option>
<option value="1902">1902</option>
<option value="1901">1901</option>
<option value="1900">1900</option>
</select>
&nbsp;<abbr title="We can also use this information to alert your friends to when your birthday is."><span class="color1">why?</span></abbr>

    </td>
  </tr>
  <tr>
    <td><span class="color">Department:</span></td>
    <td>
    <select name="department" id="department" title="" class="required">
<option value="CSE" selected="selected" >CSE</option>
<option value="ECE" >ECE</option>
<option value="EEE" >EEE</option>
<option value="ARCHI" >Architecture</option>
<option value="CHEM" >Chemical</option>
<option value="CIVIL" >Civil</option>
<option value="MCA" >MCA</option>
<option value="ICE" >ICE</option>
<option value="MECH" >Mechanical</option>
<option value="META" >MME</option>
<option value="PROD" >Production</option>
</select>
    </td>
  </tr>
  <tr>
    <td><span class="color">Country:</span></td>
    <td><input name="country" type="text" id="country" size="40" value="<?php echo"$country";?>"class="formfield"/></td>
  </tr>
  <tr>
    <td><span class="color">State:</span></td>
    <td><input name="state" type="text" id="state" size="40" value="<?php echo"$state";?>" class="formfield" /></td>
  </tr>
  <tr>
    <td><span class="color">City:</span></td>
    <td><input type="text" name="city" id="city" size="40" value="<?php echo"$city";?>" class="formfield"/></td>
  </tr>
  <tr>
    <td><span class="color">Zip/Pincode:</span></td>
    <td><input type="text" name="zip" id="zip" size="40" value="<?php echo"$zip";?>" class="formfield" /></td>
  </tr>
  <tr>
    <td><span class="color">Email Address:</span></td>
    <td><input type="text" name="email1" id="email1" size="40" value="<?php echo"$email1";?>" class="formfield"/></td>
  </tr>
  <tr>
    <td><span class="color">Confirm Email:</span></td>
    <td><input type="text" name="email2" id="email2" size="40" value="<?php echo"$email2";?>" class="formfield"/></td>
  </tr>
  <tr>
    <td><span class="color">Create Password:</span></td>
    <td><input type="password" name="pass1" id="pass1" size="40" maxlength="16" class="formfield"/>&nbsp;<span class="color3"> Alphanumeric Characters Only</span>
    </td>
  </tr>
  <tr>
    <td><span class="color">Confirm Password:</span></td>
    <td><input type="password" name="pass2" id="pass2" size="40" maxlength="16" class="formfield" />&nbsp;<span class="color3"> Alphanumeric Characters Only</span>
    </td>
  </tr>
  <tr>
  <td>
  <span class="color">About You:</span>
  </td>
  <td>
  <textarea name="about" cols="37" rows="5" id="about" class="formfield"></textarea>
  </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
   <table width="100%" align="center"><tr>
   <td width="34%">
    <input type="submit" class="button123" value="Register" />
    
   </td>
   <td width="66%"><input type="reset" class="button123" value=" Reset " /></td>
   </tr></table>
    </td>
    
  </tr>
 </form>
</table>


</td>
</tr>
</table>


</body>
</html>
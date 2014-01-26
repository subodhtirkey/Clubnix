<?php include_once "scripts/checkuserlog.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CLUBNITT DEPARTMENT FORUM</title>
<link rel="icon" href="members/0/pic.jpg" type="image/x-icon" />
<link rel="stylesheet" href="style.css" />
<style type="text/css">
#cse{
	background-image:url(subodh/cse1.png);
	}
#cse:hover{
	background-image:url(subodh/cse2.png);
}
#ece{
	background-image:url(subodh/ECE1.png);
	}
#ece:hover{
	background-image:url(subodh/ECE2.png);
}
#eee{
	background-image:url(subodh/EEE.png);
	}
#eee:hover{
	background-image:url(subodh/EEE2.png);
}

#archi{
	background-image:url(subodh/ARCH1.png);
	}
#archi:hover{
	background-image:url(subodh/ARCH2.png);
}

#chem{
	background-image:url(subodh/CHEM1.png);
	}
#chem:hover{
	background-image:url(subodh/CHEM2.png);
}
#civil{
	background-image:url(subodh/CIVIL1.png);
	}
#civil:hover{
	background-image:url(subodh/CIVIL2.png);
}
#mca{
	background-image:url(subodh/MCA1.png);
	}
#mca:hover{
	background-image:url(subodh/MCA2.png);
}
#ice{
	background-image:url(subodh/ICE.png);
	}
#ice:hover{
	background-image:url(subodh/ICE2.png);
}
#mech{
	background-image:url(subodh/MECH1.png);
	}
#mech:hover{
	background-image:url(subodh/MECH2.png);
}
#meta{
	background-image:url(subodh/META1.png);
	}
#meta:hover{
	background-image:url(subodh/META2.png);
}
#prod{
	background-image:url(subodh/PROD1.png);
	}
#prod:hover{
	background-image:url(subodh/PROD2.png);
}
#link{
	text-decoration:none; 
	color:#999; 
	
}
#link:hover{
	text-decoration:none; 
	color:#FFFFFF; 
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


<table width="900" align="center" height="142" background="subodh/forumheader.png" border="0" cellpadding="0" cellspacing="0">
<tr><td></td></tr>
</table>

<table align="center" width="900" border="0" cellpadding="2" cellspacing="2" bgcolor=""><tr><td>
<p id="breadcrum" style="color:#FFFFFF; font-weight:bold"><a href="index.php" id="link">Home</a> &larr; Clubnitt dep. forum</p>
</td></tr></table>

<table width="900" cellpadding="0" cellspacing="2" border="0" align="center">
<tr>
<td width="300" height="200" id="cse" onClick="document.location.href='section.php?id=1';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="ece" onClick="document.location.href='section.php?id=2';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="eee" onClick="document.location.href='section.php?id=3';" style="cursor:pointer;cursor:hand"></td>
</tr>
<tr>
<td width="300" height="200" id="archi" onClick="document.location.href='section.php?id=4';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="chem" onClick="document.location.href='section.php?id=5';"style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="civil" onClick="document.location.href='section.php?id=6';" style="cursor:pointer;cursor:hand"></td>
</tr>
<tr>
<td width="300" height="200" id="mca" onClick="document.location.href='section.php?id=7';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="ice" onClick="document.location.href='section.php?id=8';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="mech" onClick="document.location.href='section.php?id=9';" style="cursor:pointer;cursor:hand"></td>
</tr>
<tr>
<td width="300" height="200" id="meta" onClick="document.location.href='section.php?id=10';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id="prod" onClick="document.location.href='section.php?id=11';" style="cursor:pointer;cursor:hand"></td>
<td width="300" height="200" id=""></td>
</tr>
</table>
</body>
</html>
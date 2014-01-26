<?php 
$MemberDisplayList = '<table border="0" align="center" cellpadding="6">
              <tr>  ';
$sql = mysql_query("SELECT id, firstname FROM myMembers WHERE email_activated='1' ORDER BY RAND() LIMIT 9");
while($row = mysql_fetch_array($sql)){
	$id = $row["id"];
	$firstname = $row["firstname"];
    $firstnameCut = substr($firstname, 0, 10);
	$check_pic = "members/$id/pic.jpg";
	if (file_exists($check_pic)) {
	    $user_pic = "<img src=\"members/$id/pic.jpg\" width=\"64px\" border=\"0\" />";
	} else {
		$user_pic = "<img src=\"members/0/pic.jpg\" width=\"64px\" border=\"0\" />";
	}
	$MemberDisplayList .= '<td><a href="profile.php?id=' . $id . '" title="' . $firstname . '" style="text-decoration:none;color:#FFFFFF;"><font size="-2">' . $firstnameCut . '</font></a><br />
	<div style=" height:64px; overflow:hidden;"><a href="profile.php?id=' . $id . '"  title="' . $firstname . '">' . $user_pic . '</a></div></td>';

} // close while loop

$MemberDisplayList .= '              </tr>
            </table>  ';
	

?>
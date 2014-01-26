<?php 

$db_host = "localhost";

$db_username = "clubnaew_subodh"; 

$db_pass = "Jesus123!"; 

$db_name = "clubnaew_clubnittmembers";

// Run the connection here 
mysql_connect("$db_host","$db_username","$db_pass") or die ("could not connect to mysql");

mysql_select_db("$db_name") or die ("no database");
?>
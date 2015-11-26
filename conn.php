<?php
// This file connects to the database using the mysqli protocol
// If it can connect, you can access the connection using the
// require_once('conn.php') line, then using the $mysqli variable.
// Change the UN (2), PW (3), and DB name (4) depending on the
// server you are on.
$mysqli = mysqli_connect('localhost', 'root','', 'instagram') or
	die("Could not connect: " . mysql_error());
?>

<?php
require_once("conn.php");
$un = $_POST['username'];
$pw = $_POST['password'];

if (($pw == NULL) || ($un == NULL))
{
    echo "failure";
    return;
}

$query = "SELECT name FROM user WHERE user_name='" . $un 
    . "' AND password='" . $pw . "';";

$result = $mysqli->query($query);
$rows = $result->num_rows;

//No rows returned, their credentials were wrong.
if ($rows == 0)
    echo "failure";
else 
    echo "success";
?>

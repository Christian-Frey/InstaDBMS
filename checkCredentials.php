<?php
require_once("conn.php");
$un = $_POST['username'];
$pw = $_POST['password'];

if (($pw == NULL) || ($un == NULL))
{
    echo "failure";
    return;
}
if (!($stmt = $mysqli->prepare("SELECT name FROM user WHERE user_name =
      ? AND password= ?")))
    echo $mysqli->error;

if (!$stmt->bind_param('ss', $un, $pw))
    echo $mysqli->error;

$stmt->execute();
$stmt->bind_result($name);

//No rows returned, their credentials were wrong.
if (!$stmt->fetch())
    echo "failure";
else
    echo "success";
?>

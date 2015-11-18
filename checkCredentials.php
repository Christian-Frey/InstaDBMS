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
{
    echo $mysqli->error;
}

if (!$stmt->bind_param("ss", $un, $pw))
{
    echo $mysqli->error;
}

$stmt->execute();
$result = $stmt->get_result();
$rows = $result->num_rows;

//No rows returned, their credentials were wrong.
if ($rows == 0)
    echo "failure";
else
    echo "success";
?>

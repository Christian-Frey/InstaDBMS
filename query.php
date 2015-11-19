<?php
require_once("conn.php");

switch ($_POST['query'])
{
	case 'newUser':
		/* lets add this new user. */
		if (!($stmt = $mysqli->prepare("INSERT INTO user (user_name, password,
			name, email, phone, bio, website, gender) VALUES (?, ?, ?, ?, ?,
				 ?, ?, ?)")))
		{
			echo $mysqli->error;
		}

		if (!$stmt->bind_param("ssssssss", $_POST['username'],
		    $_POST['password'], $_POST['name'], $_POST['email'],
			$_POST['phone'], $_POST['bio'], $_POST['website'],
		    $_POST['gender']))
		{
			echo $mysqli->error;
		}

		if ($stmt->execute())
			echo "success";
		else
			echo "failure";

		break;

	case("uniqueUserOrPw"):
		if (!($stmt = $mysqli->prepare("SELECT user_name, email FROM user WHERE
			user_name= ? OR email= ?")))
			echo $mysqli->error;

		if (!$stmt->bind_param('ss', $_POST['username'], $_POST['email']))
			echo $mysqli->error;

		$stmt->execute();
		$stmt->bind_result($un, $email);

		if (!$stmt->fetch()) /* no matchs, thats what we want */
			echo "success";
		else
			echo "failure";
		break;

	case("checkLogin"):
		$un = $_POST['username'];
		$pw = $_POST['password'];
		if (($pw == NULL) || ($un == NULL))
		{
	    	echo "failure";
	    	return;
		}
		if (!($stmt = $mysqli->prepare("SELECT user_id FROM user
			WHERE user_name = ? AND password= ?")))
		{
	    	echo $mysqli->error;
		}
		if (!$stmt->bind_param('ss', $un, $pw))
	    	echo $mysqli->error;

		$stmt->execute();
		$stmt->bind_result($uid);

			//No rows returned, their credentials were wrong.
		if (!$stmt->fetch())
	    	echo "failure";
		else
		{
	    	setcookie("instaDBMS", $uid);
	    	echo "success";
		}
		break;
		
	default:
		/* Not quite sure how we got here, but return failure to be safe. */
		echo "failure";
		return;
}
?>

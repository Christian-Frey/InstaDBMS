<?php
require_once("conn.php");

switch ($_POST['query'])
{
	case 'newUser':
		/* lets add this new user. */
		if (!($stmt = $mysqli->prepare("INSERT INTO USER (user_name, password,
			name, email, phone, bio, website, gender) VALUES
			(?, ?, ?, ?, ?, ?, ?, ?)")));
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
		{
			echo "success";
			$stmt->close();
			return;
		}
		else
		{
			echo "failure";
			return;
		}

		break;

	case("uniqueUserOrPw"):
		if (!($stmt = $mysqli->prepare("SELECT user_name, email from USER where
			user_name= ? OR email= ?")))
		{
			echo $mysqli->error;
		}

		if (!$stmt->bind_param('ss', $_POST['username'],
		$_POST['email']))
		{
			echo $mysqli->error;
		}

		if ($stmt->execute())
		{
			$result = $stmt->get_result();
		}

		if ($result->num_rows == 0)
		{
			/* no matchs, thats what we want */
			echo "success";
			return;
		}
		else
		{
			echo "failure";
			return;
		}
		break;
	default:
		/* Not quite sure how we got here, but return failure to be safe. */
		echo "failure";
		return;


}
?>

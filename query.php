<?php
require_once("conn.php");

switch ($_POST['query']) 
{
	case 'newUser':
		/* lets add this new user. */
		$query = "INSERT INTO USER (user_name, password, name, email, 
		phone, bio, website, gender) VALUES ('" . $_POST['username'] . "','" .
		$_POST['password'] . "','" . $_POST['name'] . "','" . $_POST['email'] .
		"','" . $_POST['phone'] . "','" . $_POST['bio'] . "','" . 
		$_POST['website'] . "','" . $_POST['gender'] . "');";
		
		$result = $mysqli->query($query);
		if ($result) 
		{
			echo "success";
			return;
		}
		else 
		{
			echo "failure";
			return;
		}
		
		break;
		
	case("uniqueUserOrPw"):
		$query = "SELECT user_name, email FROM user WHERE user_name ='" . 
			$_POST['username'] . "' OR email= '" . $_POST['email'] . 
			"';";
		$result = $mysqli->query($query);
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
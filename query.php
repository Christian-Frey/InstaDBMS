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


	case("addComment"):
		$user_name = $_POST['user_name'];
        $stmtUserID = $mysqli->prepare("SELECT user_id FROM user WHERE
        user_name = ?");
        $stmtInsert = $mysqli->prepare("INSERT INTO comment (photo_id, user_id,
            text, date) VALUES (?, ?, ?, ?)");

        $stmtUserID->bind_param('s', $user_name);
        $stmtUserID->execute();
        $stmtUserID->store_result();
        $stmtUserID->bind_result($uid);
        $stmtUserID->fetch();

        $date = date('Y-m-d H:i:s');
        $stmtInsert->bind_param('iiss', $_POST['photo_id'], $uid,
         $_POST['comment'], $date);
        $stmtInsert->execute();
        if ($stmtInsert->affected_rows != 1)
        {
            echo $mysqli->error;
            echo "failure";
            break;
        }
        echo "success";
        break;

    case("likePhoto"):
        // check if I like it first. And then do the opposite.
        $stmt = $mysqli->prepare("SELECT photolikes.user_id FROM photolikes
             WHERE photolikes.photo_id = ? AND photolikes.user_id = ?");
        $stmt->bind_param("ii", $_POST['photo_id'], $_COOKIE['instaDBMS']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == '1')
        {
            // Already in the table, remove.
            $stmtRemove = $mysqli->prepare("DELETE FROM photolikes WHERE
                photolikes.user_id = ? AND photolikes.photo_id = ?");
            $stmtRemove->bind_param('ii', $_COOKIE['instaDBMS'],
                $_POST['photo_id']);
            $stmtRemove->execute();

            echo 'unlike';
            break;
        }
        // not in the table, add to it.
        $date = date('Y-m-d H:i:s');
        $stmtLike = $mysqli->prepare("INSERT INTO photolikes (photo_id, user_id, time) VALUES (?, ?, ?)");
        $stmtLike->bind_param('sss', $_POST['photo_id'], $_COOKIE['instaDBMS'], $date);
        $stmtLike->execute();
        echo $mysqli->error;
        echo 'like';
        break;

    case("reportPhoto"):
        $stmt = $mysqli->prepare("INSERT INTO reported (photo_id, user_id,
             reason) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $_POST['photo_id'], $_COOKIE['instaDBMS'],
             $_POST['reason']);
        $stmt->execute();

        if (!$mysqli->error == "")
            echo 'success';
        break;

    case('addHashtag'):
        $stmt = $mysqli->prepare("INSERT INTO hashtag(photo_id, hashtag) VALUES (?, ?)");
        $stmt->bind_param('ss', $_POST['photo_id'], $_POST['hashtag']);
        $stmt->execute();
        if ($stmt->num_rows == 1)
        {
            echo "Hashtag Added";
        }
        break;

    default:
		/* Not quite sure how we got here, but return failure to be safe. */
		echo "failure";
		return;
}
?>

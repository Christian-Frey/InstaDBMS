<?php
/*
Name: query.php
Author: Christian & Hayly
Purpose: To provide one unified location to go to for all ajax
         SQL queries. The structure of the file is one massive switch
         statement. When formulating the ajax *POST* call, you need to include
         a key called 'query', and then some unique identifying value. Then,
         you can go in here and create a case with the identifying value,
         and do whatever you need to do.
*/
// Connecting to the server...*dial up noises*
require_once("conn.php");

switch ($_POST['query'])
{
    case('search'): // The user is searching for something
        // TODO: dont display a blocked user
        $sap = $_POST['search'];
        if ($sap{0} == '#')
            //They are searching for a hashtag (word[0] is a #)
            echo "hashtag";
        else
        {
            // They are searching for a user, try to match a user_id with
            // the name.
            $stmt = $mysqli->prepare(
                "SELECT user_id FROM user WHERE user_name = ?");
            $stmt->bind_param('s', $sap);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($uid);
            $stmt->fetch();
            // We found something, lets return it.
            if ($uid != '')
                echo $uid;
            else // No user found.
                echo 'failure';
        }
        break;

	case('promoteUser'): // Here you can promote a user to mod status
		if (!isset($_POST['user_id']) || !isset($_COOKIE['instaDBMS'])) {
			echo "failure";
			break;
		}
        $stmt = $mysqli->prepare("INSERT INTO moderator(mod_id,promoted_by,promoted_date) VALUES (?,?,NOW())");
        $stmt->bind_param("ii", $_POST['user_id'], $_COOKIE['instaDBMS']);
        $stmt->execute();
        $stmt->store_result();
        echo $mysqli->error;
        echo 'promoted';
        break;

	case ('newUser'): // We are adding a new user
        // insert the users values into the table.
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
        // if we have an error inserting the data, return failure.
		if ($stmt->execute())
			echo "success";
		else
			echo "failure";

		break;

    // Here we want to update the users profile.
	case ('updateUser'):
		if (!($stmt = $mysqli->prepare("SELECT user_name,email,user_id
            FROM user WHERE (user_name=? OR email=?) AND user_id != ?")))
		  echo $mysqli->error;

		if (!$stmt->bind_param('sss', $_POST['username'], $_POST['email'],
                $_COOKIE['instaDBMS']))
			echo $mysqli->error;

		$stmt->execute();
		$stmt->bind_result($un, $email, $uid);

		if ($stmt->fetch()) { // no matchs, thats what we want
			echo "userExists";
			break;
		}

		// Updating the users information.
		if (!($stmt = $mysqli->prepare("UPDATE user SET user_name=?, password=?,
			name=?, email=?, phone=?, bio=?, website=?, gender=? WHERE user.user_id=?")))
		{
			echo $mysqli->error;
		}

		if (!$stmt->bind_param("sssssssss", $_POST['username'],
		    $_POST['password'], $_POST['name'], $_POST['email'],
			$_POST['phone'], $_POST['bio'], $_POST['website'],
		    $_POST['gender'], $_COOKIE['instaDBMS']))
		{
			echo $mysqli->error;
		}

		if ($stmt->execute()) // checking if the update worked.
			echo "success";
		else
			echo "failure";

		break;

    // Making sure the username or e-mail submitted by the user are unique.
	case("uniqueUserOrPw"):
		if (!($stmt = $mysqli->prepare("SELECT user_name, email FROM user WHERE
			user_name= ? OR email= ?")))
			echo $mysqli->error;

		if (!$stmt->bind_param('ss', $_POST['username'], $_POST['email']))
			echo $mysqli->error;

		$stmt->execute();
		$stmt->bind_result($un, $email);

		if (!$stmt->fetch()) //no rows means the username and e-mail are unique
			echo "success";
		else
			echo "failure";
		break;

    // Checking to see if the credentials entered by the user are correct.
	case("checkLogin"):
		$un = $_POST['username'];
		$pw = $_POST['password'];
        // They need to enter something...
		if (($pw == NULL) || ($un == NULL))
		{
	    	echo "failure";
	    	return;
		}
		if (!($stmt = $mysqli->prepare("SELECT user_id, is_disabled, disabled_note
        FROM user WHERE user_name = ? AND password= ?")))
		{
	    	echo $stmt->errno;
		}
		if (!$stmt->bind_param('ss', $un, $pw))
	    	echo $stmt->errno;

		$stmt->execute();
		$stmt->bind_result($uid, $disabled, $note);

    if ($disabled == '1')
    {
      echo 'disabled';
      break;
    }
		//No rows returned, their credentials were wrong.
		if (!$stmt->fetch())
	    	echo "failure";

    else if ($disabled == '1')
    {
        echo $note;
        break;
    }

    else // We got a row, lets set a cookie to remember them.
		{
	    	setcookie("instaDBMS", $uid);
	    	echo "success";
		}
		break;

    // Adding a comment to the comment table.
	case("addComment"):
		$user_name = $_POST['user_name'];
        // We only have the username, but need the user_id for the
        // insert, so lets get that.
        $stmtUserID = $mysqli->prepare("SELECT user_id FROM user WHERE
        user_name = ?");
        // actually inserting the comment.5
        $stmtInsert = $mysqli->prepare("INSERT INTO comment (photo_id, user_id,
            text, date) VALUES (?, ?, ?, ?)");

        $stmtUserID->bind_param('s', $user_name);
        $stmtUserID->execute();
        $stmtUserID->store_result();
        $stmtUserID->bind_result($uid);
        $stmtUserID->fetch();

        // Creating a formatted date to give to MYSQL
        $date = date('Y-m-d H:i:s');
        $stmtInsert->bind_param('iiss', $_POST['photo_id'], $uid,
         $_POST['comment'], $date);
        $stmtInsert->execute();
        // Checking to make sure only one row has been affected.
        if ($stmtInsert->affected_rows != 1)
        {
            echo $mysqli->error;
            echo "failure";
            break;
        }
        echo "success";
        break;

    // Allows the user to like a photo
    case("likePhoto"):
        // check if the user likes it first. And then do the opposite.
        $stmt = $mysqli->prepare("SELECT photolikes.user_id FROM photolikes
             WHERE photolikes.photo_id = ? AND photolikes.user_id = ?");
        $stmt->bind_param("ii", $_POST['photo_id'], $_COOKIE['instaDBMS']);
        $stmt->execute();
        $stmt->store_result();
        // They already liked the photo.
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
        // not in the table, add the like to the table
        $date = date('Y-m-d H:i:s');
        $stmtLike = $mysqli->prepare("INSERT INTO photolikes
                (photo_id, user_id, time) VALUES (?, ?, ?)");
        $stmtLike->bind_param('sss', $_POST['photo_id'],
            $_COOKIE['instaDBMS'], $date);
        $stmtLike->execute();
        echo 'like';
        break;

    // The user wants to report the photo. We are not concerned if the
    // user has already reported it, reporting it again might give
    // them some satisfaction.
    case("reportPhoto"):
        $stmt = $mysqli->prepare("INSERT INTO reported (photo_id, user_id,
             reason) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $_POST['photo_id'], $_COOKIE['instaDBMS'],
             $_POST['reason']);
        $stmt->execute();
        // Checking for error
        if (!$stmt->errno == 0)
            echo 'success';
        break;

    case("followUser"):
        // check if the user is following first. And then do the opposite.
		if (!isset($_POST['friend_id']) || !isset($_COOKIE['instaDBMS'])) {
			echo "failure";
			break;
		}
        $stmt = $mysqli->prepare("SELECT friend.user_id FROM friend
             WHERE friend.user_id = ? AND friend.friend_id = ?");
        $stmt->bind_param("ii", $_COOKIE['instaDBMS'], $_POST['friend_id']);
        $stmt->execute();
        $stmt->store_result();
        // they have already followed the user.
        if ($stmt->num_rows == 1)
        {
            // Already in the table, remove.
            $stmtRemove = $mysqli->prepare("DELETE FROM friend WHERE
                friend.user_id = ? AND friend.friend_id = ?");
            $stmtRemove->bind_param('ii', $_COOKIE['instaDBMS'],
                $_POST['friend_id']);
            $stmtRemove->execute();

            echo 'unfollowed';
            break;
        }
        // a new follow, add to the table.
        $stmtLike = $mysqli->prepare("INSERT INTO friend (user_id, friend_id)
            VALUES (?, ?)");
        $stmtLike->bind_param('ss', $_COOKIE['instaDBMS'], $_POST['friend_id']);
        $stmtLike->execute();
        echo 'followed';
        break;

    // Adding a hashtag to the hashtag table
    case('addHashtag'):
        $stmt = $mysqli->prepare("INSERT INTO hashtag(photo_id, hashtag)
            VALUES (?, ?)");
        $stmt->bind_param('ss', $_POST['photo_id'], $_POST['hashtag']);
        $stmt->execute();
        if ($stmt->num_rows == 1) // making sure only 1 row was inserted.
        {
            echo "Hashtag Added";
        }
        break;

    // diables the user profile by setting is_hidden to 1.
    // NOTE: *DO NOT* insert any cases between this and ignoreReport.
    // It relies on falling through to the next case to cover all the
    // bases, especially removing the report once an action has been taken.
    case ('disableUser'):
        $date = date('Y-m-d H:i:s'); //MYSQL formatted date
        echo $_POST['photo_id'];
        // disabling the user
        // TODO: disable all photos
        $stmtDisable = $mysqli->prepare("UPDATE user SET is_disabled = 1,
            disabled_by = ?, disabled_date = ?, disabled_note = ?
            WHERE user_id = (SELECT user_id FROM photo WHERE photo_id = ?)");
        $stmtDisable->bind_param('ssss', $_COOKIE['instaDBMS'],
            $date, $_POST['msg'], $_POST['photo_id']);
        $stmtDisable->execute();

        // *****FALLING THROUGH*****

    // Removing the offending photo by setting hidden to 1.
    case('removePhoto'):
        echo $_POST['photo_id'];
        // removing the photo. No reason is required.
        $stmtRemove = $mysqli->prepare("UPDATE photo SET hidden = 1 WHERE
            photo_id = ?");
        $stmtRemove->bind_param('s', $_POST['photo_id']);
        $stmtRemove->execute();

        // *****FALLING THROUGH*****

    // Here we ignore the report by removing it from the reported table.
    case('ignoreReport'):
        echo $_POST['photo_id'];
        // Deleting all reports for that photo.
        $stmtIgnore = $mysqli->prepare("DELETE FROM reported WHERE
            photo_id = ?");
        $stmtIgnore->bind_param('s', $_POST['photo_id']);
        $stmtIgnore->execute();
        break; // Stopping at the bottom.

    // With this the user can upload files up to 16MB (2^24) in size.
    case('uploadPhoto'):
        // Getting the current date
        $date = date('Y-m-d H:i:s');
        $photoData = substr($_POST['image'], 23);
        // The insert statement.
        $stmt = $mysqli->prepare("INSERT INTO photo (user_id, image,
            upload_date, hidden) VALUES (?, ?, ?, 0)");
        $stmt->bind_param('sss', $_COOKIE['instaDBMS'], $photoData,
            $date);
        $stmt->execute();
        if (!$mysqli->error) // Making sure there are no errors.
            echo 'success';
        else
            echo $mysqli->error;
        break;

    default:
		//Not quite sure how we got here, but return failure to be safe.
		echo "failure";
		return;
}

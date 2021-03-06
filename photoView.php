<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
	  href="css/stylesheetHome.css" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src="js/homeListener.js"></script>
<script type='text/javascript' src="js/header.js"></script>
</head>
<body>
<?php
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');

    require_once('header.php');
    buildHeader();
    require_once('conn.php');

	 $viewingPhoto = '';
     // Checking if there is an available photo_id to display, and if so,
     // getting the photo_id.
	 if(isset($_GET['photo']))
	 	$viewingPhoto = $_GET['photo'];

 ?>
</div>
	<?php
	// This gets all the images that the logged in user and their friends have
	// posted. The first section gets the right data, and the second section
    // (After the join) describes what user_ids to search for.
	$stmtImage = $mysqli->prepare("SELECT photo.image, photo.photo_id,
		photo.upload_date, user.user_name, user.user_id, photo.hidden, user.is_disabled FROM photo
        INNER JOIN user on photo.user_id = user.user_id WHERE
        photo.photo_id=?");

    // Getting the number of likes on the photo using the COUNT function.
	// ? is the photo_id to get the likes of (from $stmtImage)
	$stmtCountLike = $mysqli->prepare("SELECT COUNT(photolikes.photo_id),
    photolikes.user_id FROM photolikes WHERE photolikes.photo_id = ?");

    // Getting the comments that are tied to a particular photo.
	// The first ? is the photo_id of the photo we got, and the
    // second ? is the user_id of the user to get the comment they made.
	$stmtComment = $mysqli->prepare("SELECT user.user_name, comment.text,
		comment.hidden
		FROM comment INNER JOIN user ON comment.user_id=user.user_id WHERE
		comment.photo_id=? ORDER BY comment.comment_id ASC");

    // Checking if we have the photo_id of the photo we want, then
    // getting the image and related data.
    if ($viewingPhoto != '') {
		$stmtImage->bind_param('i', $viewingPhoto);
		$stmtImage->execute();
		$stmtImage->store_result();
		$stmtImage->bind_result($image, $photo_id, $uploadDate,
								$pUsername, $pUser_id, $pHidden, $is_disabled);

		while ($stmtImage->fetch())
		{
			if ($pHidden === 1) {
				echo '<div class="stats">This image has been removed!</div></br>';
			} else if ($is_disabled === 1) {
				echo '<div class="stats">This user is disabled!</div></br>';
				echo '<div class="disabled_user"><img src="disabled.jpg"/></div>';
			} else {
				// The div is used to contain all the data about the photo.
				echo '<div class="photo_view' . $photo_id . '">';
				// We want to place the user who posted above the image.
				echo '<a href="profile.php?id=' . $pUser_id . '">
					  <span class="pUsername">' . $pUsername . '</span></a>';

				// We need the date for be formatted nicely. So lets do that.
				$timeSinceUpload = (time() - strtotime($uploadDate));

				// thanks to http://stackoverflow.com/a/2916189/5531440 for the help.
				$timeSeconds = array (
					31536000 => 'y',
					2592000 => 'm',
					604800 => 'w',
					86400 => 'd',
					3600 => 'h',
					60 => 'm'
				);
				// Getting the largest unit of time we can, then the number for an
				// aproximate time since when the image was posted.
				foreach ($timeSeconds as $time => $text)
				{
					if ($timeSinceUpload < $time) continue;
					$numUnits = floor($timeSinceUpload / $time);
					echo '<span class=timeSince>' . $numUnits . $text . '</span><br>';
					break;
				}

				// display the photo we got, assuming the photo is not hidden.
				echo '<img id="picture' . $photo_id . '" src="data:image/jpg;base64,' . $image . '"/>';
				

				// Now that we have the photo_id, we can get the comments and likes
				// that are tied to that photo.
				$stmtCountLike->bind_param('i', $photo_id);
				$stmtComment->bind_param('i', $photo_id);

				// Executing and storing the results.
				$stmtCountLike->execute();
				$stmtCountLike->store_result();
				$stmtComment->execute();
				$stmtComment->store_result();
				$stmtCountLike->bind_result($numLikes, $userLikes);
				$stmtComment->bind_result($user_name, $text, $cHidden);

				// using COUNT, we are guaranteed only one row, no loop needed.
				$stmtCountLike->fetch();
				echo '<p class="likes">' . $numLikes;
				echo (($numLikes == 1) ? ' like' : ' likes');
				echo '</p>';

				// While we have more comments, and the comments are not hidden, then
				// insert them below the picture.
				while ($stmtComment->fetch())
				{
					if ($cHidden === NULL)
					{
						echo '<span class="commentUser">' . $user_name .  " </span>";
						echo '<span class="comment">' . $text .'</span><br>';
					}
				}

				// A bit of legacy to provide the photo_id in the page so jQuery
				// can get at, before it was embedded into the div name.
				echo '<div id="photo_id" style="visibility: hidden">' . $photo_id . '</div>';

				// Adding in the comment insert field.
				echo
				'<div class="mCommentSect">
				<form onsubmit="return false;">';

				// Checking if the user likes this photo. and outputing the correct
				// value, allowing them to toggle between the two at will.
				$stmtUserLikes = $mysqli->prepare("SELECT user_id FROM photolikes where user_id = ?");
				$stmtUserLikes->bind_param('i', $_COOKIE['instaDBMS']);
				$stmtUserLikes->execute();
				$stmtUserLikes->store_result();
				$stmtUserLikes->bind_result($userLikes);

				// No rows returned, they must not have liked the photo yet.
				if ($stmtUserLikes->num_rows == 0)
					echo '<a href="javascript:;" class="heart">Not Liked</a>';
				else {
					echo '<a href="javascript:;" class="heart">Liked</a>';
				}

				// Providing the input comment field, and a button to report the
				// photo. Once the report button is clicked, a dropdown is added by
				// Javascript (homeListener.js) to allow the user to choose why
				// the image should be reported.
				echo '<input id="insertComment' . $photo_id . '" type="text" placeholder="comment">';
				echo '<a href="javascript:;" class="report">Report</a>';
				echo '</form></div>';
				echo '<div id="reportedPlaceholder"></div>';

				// and finally, close that photo div.
				echo '</div>';
			}
		}
		
	} else {
        // We don't have a photo, so tell them that.
		echo '<span class="notFound"> "PHOTO NOT FOUND" </span>';
	}
	?>
</body>

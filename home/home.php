<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
	  href="stylesheetHome.css" />

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="../jquery.min.js"></script>
<script type='text/javascript' src="../js/homeListener.js"></script>
</head>
<body>
<?php
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: ../index.php');
 ?>

 <!-- Lets Make the header of the page -->
 <div class=header>
	 <a href="home.php" id="projectName">instaDBMS</a>
	 <input id="searchSite" name='searchSite' type='text'
	        placeholder=" Search?">
     <a id=uploadPhoto href="uploadPhoto.php">Upload Photo</a>
	 <?php
	 require_once("../conn.php");
	 $stmtUN = $mysqli->prepare("SELECT user_name FROM user where user_id= ?");
     $stmtIsAMod = $mysqli->prepare("SELECT mod_id FROM
          moderator where mod_id = ?");
     $stmtIsAMod->bind_param('i', $_COOKIE['instaDBMS']);
     $stmtIsAMod->execute();
     $stmtIsAMod->store_result();
     if ($stmtIsAMod->num_rows == 1)
     {
         echo '<a id="viewReport" href="viewReports.php">View Reports</a>';
         echo '<a id="promoteMod" href="promoteMod.php">Promote Mod</a>';
     }
	 // We cant be sure the user hasn't modified the cookie.
	 $stmtUN->bind_param("s", $_COOKIE['instaDBMS']);
	 $stmtUN->execute();
	 $stmtUN->bind_result($un);
	 while ($stmtUN->fetch())
		echo "<a id=user_name href='../profile.php'>" . $un . "</a>";
 ?>
</div>
	<?php
	// This gets all the images that the logged in user and their friends have
	// posted.
	// The first section gets the right data, and the second section describes
	// what user_ids to search for.
	$stmtImage = $mysqli->prepare("SELECT photo.image, photo.user_id, photo.photo_id,
		photo.upload_date, user.user_name FROM photo INNER JOIN user on
		photo.user_id = user.user_id WHERE photo.user_id IN
		(SELECT user_id as user from user where user_id = ? UNION SELECT friend_id
	  AS user FROM friend JOIN user ON user.user_id = friend.user_id and
		user.user_id = ?)");

	// ? is the photo_id to get the likes of (from $stmtImage)
	$stmtCountLike = $mysqli->prepare("SELECT COUNT(photolikes.photo_id),
    photolikes.user_id FROM photolikes WHERE photolikes.photo_id = ?");

	// First ? is the photo_id of the photo we got
	// Second ? is the user_id of the user to get the comment they made.
	$stmtComment = $mysqli->prepare("SELECT user.user_name, comment.text
		FROM comment INNER JOIN user ON comment.user_id=user.user_id WHERE
		comment.photo_id = ? ORDER BY comment.comment_id ASC");

	$stmtImage->bind_param('ii', $cookie, $cookie);
	$stmtImage->execute();
	$stmtImage->store_result();
	$stmtImage->bind_result($image, $pUser_id, $photo_id, $uploadDate, $pUsername);

    // They only get one image per page for simplicity.
	while ($stmtImage->fetch())
	{
	echo '<div class="photo_view' . $photo_id . '">';
	echo '<a href="../profile.php?id=' . $pUser_id . '"><span class="pUsername">' . $pUsername . '</span></a>';

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
	foreach ($timeSeconds as $time => $text)
	{
		if ($timeSinceUpload < $time) continue;
		$numUnits = floor($timeSinceUpload / $time);
		echo '<span class=timeSince>' . $numUnits . $text . '</span><br>';
		break;
	}
	// display the photo we got
	echo '<a href="photoView.php?photo=' . $photo_id . '"><img id="picture' . $photo_id .
        '" src="data:image/jpg;base64,' . $image . '"/></a>';

	// Now that I have the photo_id, I can get the comments and likes
	// that are tied to that photo.
	$stmtCountLike->bind_param('i', $photo_id);
	$stmtComment->bind_param('i', $photo_id);

	$stmtCountLike->execute();
	$stmtCountLike->store_result();
	$stmtComment->execute();
	$stmtComment->store_result();

	$stmtCountLike->bind_result($numLikes, $userLikes);
	$stmtComment->bind_result($user_name, $text);

	// using COUNT, we are guanenteed only one row, no loop needed.
	$stmtCountLike->fetch();
	echo '<p class="likes">' . $numLikes;
	echo (($numLikes == 1) ? ' like' : ' likes');
	echo '</p>';

	while ($stmtComment->fetch())
	{
		echo '<span class="commentUser">' . $user_name .  " </span>";
		echo '<span class="comment">' . $text .'</span><br>';
	}
	echo '<div id="photo_id" style="visibility: hidden">' . $photo_id . '</div>';

	// Adding in the comment insert field.
	echo
	'<div class="mCommentSect">
	<form onsubmit="return false;">';

    $stmtUserLikes = $mysqli->prepare("SELECT user_id FROM photolikes where user_id = ?");
    $stmtUserLikes->bind_param('i', $_COOKIE['instaDBMS']);
    $stmtUserLikes->execute();
    $stmtUserLikes->store_result();
    $stmtUserLikes->bind_result($userLikes);
    if ($stmtUserLikes->num_rows == 0)
        echo '<a href="javascript:;" class="heart' . $photo_id . '">Not Liked</a>';
    else {
        echo '<a href="javascript:;" class="heart">Liked</a>';
    }

	echo '<input id="insertComment' . $photo_id . '" type="text" placeholder="comment">';
	echo '<a href="javascript:;" class="report">Report</a>';
	echo '</form></div>';
    echo '<div id="reportedPlaceholder"></div>';

	// and finally, close that div
	echo '</div>';
}
	?>

</body>

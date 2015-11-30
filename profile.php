<!--
Name: profile.php
Author: Hayly
Purpose: Displays the profile of the currently logged in user if
         no user is specified, or it goes to the user profile that
         was specified in the url.
-->

<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<!-- Including the required files -->
<link rel="stylesheet" type="text/css" media="screen"
	  href="css/profile.css" />
<link rel="stylesheet" type="text/css" media="screen"
	  href="css/stylesheetHome.css" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src="js/profileListener.js"></script>
<script type='text/javascript' src='js/header.js'></script>
</head>
<body>
<?php
    // Checking if the user is logged in.
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');
	$viewing = $cookie;
	if (isset($_GET['id']))
		$viewing = $_GET['id'];

    require_once('header.php');
    buildHeader();

    // Connecting to the server...*dial up noises*
    require_once("conn.php");
    // Getting all of the useful use information on a user, including
    // how many photos they have uploaded, how many friends and following,
    // and their website.
	$stmtProfile = $mysqli->prepare("SELECT
		user.user_name,user.name,user.bio,user.website,COUNT(distinct photo.photo_id),COUNT(distinct a.friend_id),COUNT(distinct b.friend_id), photo.hidden, user.is_disabled
		FROM user LEFT JOIN photo ON photo.user_id=? LEFT JOIN friend a ON a.friend_id=? LEFT JOIN friend b ON b.user_id=?
		WHERE user.user_id=?");

    // Binding the user_id to search for. The query requires it in
    // multiple places, so it gets included more than once.
	$stmtProfile->bind_param('iiii', $viewing, $viewing, $viewing, $viewing);
	$stmtProfile->execute();
	$stmtProfile->store_result();
	$stmtProfile->bind_result($user_name, $name, $bio, $website, $numPhotos,
        $numFollowers, $numFollowing, $pHidden, $is_disabled);

    // embedding the id of the searched for user in the page.
	echo '<div id="friend_id" style="visibility: hidden; height: 0px;">'
        . $viewing . '</div>';


	while ($stmtProfile->fetch())
	{
		
		if ($pHidden == '1') continue;
		echo '<div class="profile_view" user="' . $viewing . '">';
        // Displaying the username of the user.
		echo '<span class="user_name">' . $user_name . '</span></br>';

		if ($is_disabled == '1') {
			echo '<span class="stats">This user has been disabled.</span></br>';
		} else {
			// If they are not viewing their own profile, they have the
			// option to follow or unfollow that user.
			if ($viewing != $cookie) {
				$stmtFollow = $mysqli->prepare("SELECT user_id FROM friend where user_id = ? and friend_id = ?");
				$stmtFollow->bind_param('ii', $cookie, $viewing);
				$stmtFollow->execute();
				$stmtFollow->store_result();
				if ($stmtFollow->num_rows == 0)
					echo '<a href="javascript:;" class="follow">FOLLOW</a>';
				else
					echo '<a href="javascript:;" class="follow">FOLLOWING</a>';
			} else
				// They are viewing their own profile, so they can edit
				// their profile.
				echo '<a href="editProfile.php" class="follow">EDIT PROFILE</a>';

			// Some formatting for the users data.
			echo '<span class="name">' . $name . ' | ' . $bio . ' | '
				. $website .'</span></br>';
			echo '<span class="stats">' . $numPhotos . ' '. 'post' .
				($numPhotos==1 ? '':'s') . ' | ' . $numFollowers . ' '. 'follower' .
				($numFollowers==1 ? '':'s') . ' | ' . $numFollowing .
				' following</span>';
		}
	}
	if ($is_disabled != '1') {
		$stmtMod = $mysqli->prepare("SELECT mod_id FROM moderator where mod_id = ?");
		$stmtMod->bind_param('i', $cookie);
		$stmtMod->execute();
		$stmtMod->store_result();
		$stmtMod->bind_result($isModerator);
		if ($stmtMod->num_rows != 0) {
			$stmtPromote = $mysqli->prepare("SELECT mod_id FROM moderator where mod_id = ?");
			$stmtMod->bind_param('i', $viewing);
			$stmtMod->execute();
			$stmtMod->store_result();
			$stmtMod->bind_result($isViewingModerator);
			if ($stmtMod->num_rows == 0)
				echo '<a href="javascript:;" class="moderatorPromote">PROMOTE TO MODERATOR</a>';
			else
				echo '<a href="javascript:;" class="moderatorPromote">MODERATOR</a>';
		}
		// Getting the photos uploaded by the requested user.
		$stmtPhotos = $mysqli->prepare("SELECT photo_id,image FROM photo WHERE
			photo.user_id=? ORDER BY photo.photo_id DESC");

		$stmtPhotos->bind_param('i', $viewing);

		$stmtPhotos->execute();
		$stmtPhotos->store_result();
		$stmtPhotos->bind_result($photo_id, $image);

		echo '<div>';
		$count = 0;
		while ($stmtPhotos->fetch())
		{
			// Displaying all of the users images.
			echo '<div class="prof_pics">';
			echo '<a href="photoView.php?photo=' . $photo_id . '">
				<img src="data:image/jpg;base64,' . $image . '"/></a>';
			$count = $count + 1;
			// Making sure we only display three photos per line at maximum.
			if ($count == 3)
			{
				echo '</br>';
				$count = 0;
			}
			echo '</div>';
		}
		echo '</div>';
	} else {
		echo '<div class="disabled_user"><img src="disabled.jpg"/></div>';
	}
?>
</body>

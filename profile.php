<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
	  href="profile.css" /><link rel="stylesheet" type="text/css" media="screen"
	  href="home/stylesheetHome.css" />

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src="js/profileListener.js"></script>
</head>
<body>
<?php
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');
	$viewing = $cookie;
	if (isset($_GET['id']))
		$viewing = $_GET['id'];
 ?>

 <!-- Lets Make the header of the page -->
 <div class=header>
	 <p id="projectName"><a href='home/home.php'>instaDBMS</a></p>
	 <!-- TODO: Add search functionality
	 	  if search starts with # -> only search hashtag table
		  otherwise -> search both users and hashtags -->
	 <input id="searchSite" name='searchSite' type='text'
	        placeholder=" Search?">
 	<p id=user_name><a href='../DBMS'>Log out</a></p>
</div>
	<?php
	// TODO: add support for moderator buttons.
	// If the user is a mod, add view Reports and Promote Moderator button.

	// This gets all the images that the logged in user and their friends have
	// posted.
	// The first section gets the right data, and the second section describes
	// what user_ids to search for.
	require_once("conn.php");
	$stmtProfile = $mysqli->prepare("SELECT 
		user.user_name,user.name,user.bio,user.website,COUNT(distinct photo.photo_id),COUNT(distinct a.friend_id),COUNT(distinct b.friend_id) 
		FROM user LEFT JOIN photo ON photo.user_id=? LEFT JOIN friend a ON a.friend_id=? LEFT JOIN friend b ON b.user_id=?
		WHERE user.user_id=?");

	//die($mysqli->error);
	$stmtProfile->bind_param('iiii', $viewing, $viewing, $viewing, $viewing);
	$stmtProfile->execute();
	$stmtProfile->store_result();
	$stmtProfile->bind_result($user_name, $name, $bio, $website, $numPhotos, $numFollowers, $numFollowing);

	echo '<div id="friend_id" style="visibility: hidden; height: 0px;">'. $viewing . '</div>';
	
    // They only get one image per page for simplicity.
	while ($stmtProfile->fetch())
	{
		echo '<div class="profile_view">';
		echo '<span class="user_name">' . $user_name . '</span></br>';

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
			echo '<a href="editProfile.php" class="follow">EDIT PROFILE</a>';
		
		echo '<span class="name">' . $name . ' | ' . $bio . ' | ' . $website .'</span></br>';
		echo '<span class="stats">' . $numPhotos . ' '. 'post' . ($numPhotos==1 ? '':'s') . 
			 ' | ' . $numFollowers . ' '. 'follower' . ($numFollowers==1 ? '':'s') . ' | ' . $numFollowing . ' following</span>';
	}
		
	// $stmtPromote = $mysqli->prepare("SELECT user_id FROM photolikes where user_id = ?");
    // $stmtPromote->bind_param('i', $_COOKIE['instaDBMS']);
    // $stmtPromote->execute();
    // $stmtPromote->store_result();
    // $stmtPromote->bind_result($userLikes);
    // if ($stmtPromote->num_rows == 0)
        // echo '<a href="javascript:;" class="heart">Not Liked</a>';
    // else {
        // echo '<a href="javascript:;" class="heart">Liked</a>';
    // }
	
	$stmtPhotos = $mysqli->prepare("SELECT photo_id,image FROM photo WHERE photo.user_id=? ORDER BY photo.photo_id DESC");
	$stmtPhotos->bind_param('i', $viewing);

	$stmtPhotos->execute();
	$stmtPhotos->store_result();
	$stmtPhotos->bind_result($photo_id, $image);

	echo '<div>';
	$count = 0;
	while ($stmtPhotos->fetch())
	{
		echo '<div class="prof_pics">';
		echo '<img src="data:image/jpg;base64,' . $image . '"/>';
		$count = $count + 1;
		if ($count == 3)
		{
			echo '</br>';
			$count = 0;
		}
		echo '</div>';
	}
	echo '</div>';
	
	?>

</body>
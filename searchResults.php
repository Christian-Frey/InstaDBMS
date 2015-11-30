<!--
Name: searchResults.php
Author: Hayly
Purpose: Provides the user with nicely sorted pictures whenever they
         search for a hashtag. The user can then click on an image
         to view the full page.
-->
<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<!-- Including the required files -->
<link rel="stylesheet" type="text/css" media="screen" href="css/profile.css" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHome.css" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src="js/homeListener.js"></script>
<script type='text/javascript' src='js/header.js'></script>
</head>
<body>
<?php
    // Checking if the user is logged in.
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');
    // Checking if we have a hashtag to search for.
	$viewing = '';
	if (isset($_GET['search']))
		$viewing = "#" . $_GET['search'];

    require_once('header.php');
    buildHeader();
    // Connecting to the server...*dial up noises*
	require_once("conn.php");

    // Getting all of the photos that have been tagged with the hashtag
    // the user searched for.
	$stmtPhotos = $mysqli->prepare("SELECT photo_id, image, hidden FROM photo
        WHERE photo.photo_id IN (SELECT hashtag.photo_id FROM hashtag WHERE
        hashtag.hashtag=?) ORDER BY photo.photo_id DESC");
	$stmtPhotos->bind_param('s', $viewing);

	$stmtPhotos->execute();
	$stmtPhotos->store_result();
	$stmtPhotos->bind_result($photo_id, $image, $hidden);

    // Letting the user know what they searched for.
	echo '<div class="searchHeader">' . $viewing . ' </br>' ;
	$count = 0;
	while ($stmtPhotos->fetch())
	{   // Display the photos
		echo '<div class="prof_pics">';
		// The user can click on a photo, and then it brings them to that
        // photos page.
        if ($hidden == 1) continue;
        echo '<a href="photoView.php?photo=' . $photo_id . '">
            <img src="data:image/jpg;base64,' . $image . '"/></a>';
		$count = $count + 1;
        // Only allowing 3 images per line on the screen.
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

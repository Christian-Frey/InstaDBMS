<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
	  href="profile.css" /><link rel="stylesheet" type="text/css" media="screen"
	  href="home/stylesheetHome.css" />

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src="js/homeListener.js"></script>
</head>
<body>
<?php
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');
	$viewing = '';
	if (isset($_GET['search']))
		$viewing = "#" . $_GET['search'];
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

	require_once("conn.php");
	$stmtPhotos = $mysqli->prepare("SELECT photo_id,image FROM photo WHERE photo.photo_id IN 
		(SELECT hashtag.photo_id FROM hashtag WHERE hashtag.hashtag=?) ORDER BY photo.photo_id DESC");
	$stmtPhotos->bind_param('s', $viewing);

	$stmtPhotos->execute();
	$stmtPhotos->store_result();
	$stmtPhotos->bind_result($photo_id, $image);


	echo '<div class="searchHeader">' . $viewing . ' </br>' ;
	$count = 0;
	while ($stmtPhotos->fetch())
	{
		echo '<div class="prof_pics">';
		echo '<a href="home/photoView.php?photo=' . $photo_id . '"><img src="data:image/jpg;base64,' . $image . '"/></a>';
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

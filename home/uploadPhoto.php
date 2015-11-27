<!--
Name: uploadPhoto.php
Author: Christian
Purpose: It allows the user to upload a photo to instaDBMS
-->
<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
	  href="stylesheetHome.css" />
<link rel="stylesheet" href="stylesheetUpload.css" media="screen"
      type='text/css'/>

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="../jquery.min.js"></script>
<!-- The upload listener handles all the user input for the page -->
<script type='text/javascript' src='../js/uploadListener.js'></script>
</head>
<body>
<?php
    // Checking if the user is logged in. If not, it kicks them out.
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: ../index.php');
 ?>

 <!-- Lets make the header of the page -->
 <div class=header>
	 <a href="home.php" id="projectName">instaDBMS</a>
	 <input id="searchSite" name='searchSite' type='text'
	        placeholder=" Search?">
	 <?php
     // Connecting to the server...*dial up noises*
require_once('../conn.php');

     // Getting the username of the user based on their user_id.
	 $stmtUN = $mysqli->prepare("SELECT user_name FROM user where user_id= ?");
     // Checking if the user is a moderator based on the user_id.
     $stmtIsAMod = $mysqli->prepare("SELECT mod_id FROM
          moderator where mod_id = ?");
     $stmtIsAMod->bind_param('i', $_COOKIE['instaDBMS']);
     $stmtIsAMod->execute();
     $stmtIsAMod->store_result();

     // They are a mod, so lets give them special mod buttons.
     if ($stmtIsAMod->num_rows == 1)
     {
         echo '<a id="viewReport" href="viewReports.php">View Reports</a>';
         echo '<a id="promoteMod" href="promoteMod.php">Promote Mod</a>';
     }
     // Now we can get the users name to display on their page.
	 $stmtUN->bind_param("s", $_COOKIE['instaDBMS']);
	 $stmtUN->execute();
	 $stmtUN->bind_result($un);
	 while ($stmtUN->fetch())
		echo "<a id=user_name href='../profile.php'>" . $un . "</a>";
 ?>
</div> <!-- HEADER END -->
<div id='uploadBox'>
<!-- give them a file input box (uses HTML5) -->
<input type='file' id='image' name='Photo' />
<button id='upload'>Upload Photo</button><br>
<!-- The preview will let them see it before they upload it.
     Some images are displayed sideways, other not. Rotating in
     windows does nothing. -->
<img src='' height=200 alt='Image Preview' />
</div>
</body>

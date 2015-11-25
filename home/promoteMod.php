<head>
<meta charset="utf-8">
<title>InstaDBMS</title>

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="../jquery.min.js"></script>
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
		echo "<a id=user_name href='profile.php'>" . $un . "</a>";
 ?>

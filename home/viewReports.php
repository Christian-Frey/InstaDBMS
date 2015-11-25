<head>
<meta charset="utf-8">
<title>InstaDBMS</title>

<link rel="stylesheet" href="stylesheetHome.css" type="text/css"
      media="screen" />
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
 </div>
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
     $stmtUN->store_result();
	 $stmtUN->bind_result($un);
	 $stmtUN->fetch();
	echo "<a id=user_name href='profile.php'>" . $un . "</a>";
    echo '</div>';
	// TODO: Implement View Reports

    $stmtImage = $mysqli->prepare("SELECT photo.image, photo.photo_id, photo.upload_date, user.user_name FROM photo INNER JOIN user on
		photo.user_id = user.user_id WHERE photo.user_id IN
		(SELECT reported.user_id FROM reported)");
	$stmtImage->execute();
	$stmtImage->store_result();
	$stmtImage->bind_result($image, $photo_id, $uploadDate, $pUsername);

    // They only get one image per page for simplicity.
	while ($stmtImage->fetch())
	{
    	echo '<div class="photo_view' . $photo_id . '">';
    	echo '<span class="pUsername">' . $pUsername . '</span>';

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
    	echo '<img id="picture' . $photo_id .
            '" src="data:image/jpg;base64,' . $image . '"/>';

        $stmtRptCmt = $mysqli->prepare("SELECT user.user_name, photo_id, reason FROM reported JOIN user ON user.user_id = reported.user_id WHERE photo_id = ?");
        $stmtRptCmt->bind_param("i", $photo_id);
        $stmtRptCmt->execute();
        $stmtRptCmt->bind_result($rUN, $rPid, $reason);
        while ($stmtRptCmt->fetch())
        {
          echo '<span class="rUsername">' . $rUN .  " </span>";
          switch ($reason) {
            case '1':
              echo '<span class="reason">I do not like this photo</span><br>';
              break;
            case '2':
              echo '<span class="reason">Picture is spam or a scam</span><br>';
              break;

            case '3':
              echo '<span class="reason">This photo puts people at risk</span><br>';
              break;

            case '4':
              echo '<span class="reason">This photo should not be on
                InstaDBMS</span><br>';
              break;

            default:
              echo '<span class="reason">A good reason</span><br>';
              break;
          }
        }

        echo '</div>';
    }
?>
</body>

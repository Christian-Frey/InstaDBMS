<!--
Name: viewReports.php
Written By: Christian
Purpose: To allow Moderators to manage reports submitted by users and allows
         them to take appropriate action.
-->
<head>
<meta charset="utf-8">
<title>InstaDBMS</title>

<link rel="stylesheet" href="css/stylesheetHome.css" type="text/css"
      media="screen" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/header.js'></script>
<!-- Provides all the listeners required for the page. -->
<script type='text/javascript' src="js/viewReportsListener.js"></script>
</head>
<body>

<?php
    // Checking if the user is logged in. If not, it kicks them out.
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');

    require_once('header.php');
    buildHeader();
    require_once('conn.php');

    // Getting all of the photos that have ever been reported. We get
    // the photo itself, id, date, and the user who posted it.
    $stmtImage = $mysqli->prepare("SELECT photo.image, photo.photo_id,
      photo.upload_date, user.user_name FROM photo INNER JOIN user on
	  photo.user_id = user.user_id WHERE photo.photo_id IN
	  (SELECT reported.photo_id FROM reported)");
	$stmtImage->execute();
	$stmtImage->store_result();

    // Nothing has been reported yet, thankfully.
    if ($stmtImage->num_rows == 0)
    {
        echo '<br><br><p id="nothing">Nothing has been reported yet.
            Come back later.</p>';
        exit(1);
    }
    // Binding the results in the order they were asked for to variables.
    $stmtImage->bind_result($image, $photo_id, $uploadDate, $pUsername);

    // Getting each of the offending images, one at a time.
	while ($stmtImage->fetch())
	{
        // The div is used to contain all the data about the photo.
    	echo '<div class="photo_view' . $photo_id . '">';
        // We want to place the user who posted above the image.
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
        // Getting the largest unit of time we can, then the number for an
        // aproximate time since when the image was posted.
    	foreach ($timeSeconds as $time => $text)
    	{
    		if ($timeSinceUpload < $time) continue;
    		$numUnits = floor($timeSinceUpload / $time);
    		echo '<span class=timeSince>' . $numUnits . $text . '</span><br>';
    		break;
    	}
    	// display the photo we got, ignoring if its been hidden.
    	echo '<img id="picture' . $photo_id .
            '" src="data:image/jpg;base64,' . $image . '"/>';

        // Now we get all of the reports associated with the picture. A
        // user can report each photo for each reason once. With 4 reasons,
        // that means the user can report once for each reason. Every additional
        // report is ignored by the system.
        $stmtRptCmt = $mysqli->prepare("SELECT user.user_name, photo_id, reason
         FROM reported JOIN user ON user.user_id = reported.user_id WHERE
         photo_id = ?");
        $stmtRptCmt->bind_param("i", $photo_id);
        $stmtRptCmt->execute();
        $stmtRptCmt->bind_result($rUN, $rPid, $reason);

        // Outputting all of the reports, with the user and why
        // they reported it. We can store up to 99 reasons.
        while ($stmtRptCmt->fetch())
        {
          echo '<span class="rUsername">' . $rUN .  " </span>";
          switch ($reason) {
            case '1':
              echo '<span class="reason">This picture is stolen</span><br>';
              break;
            case '2':
              echo '<span class="reason">This picture is spam</span><br>';
              break;

            case '3':
              echo '<span class="reason">This photo violates instaDBMS
                    rules</span><br>';
              break;

            case '4':
              echo '<span class="reason">I don\'t like this photo</span><br>';
              break;
            // In case we add reasons and it doesn't instantly get updated,
            // some default text.
            default:
              echo '<span class="reason">A good reason</span><br>';
              break;
          }

        }
        ?>
        <!-- Allowing the mod to choose what to do with each picture.
             disable disables the user, removes the photo, and ignores
             the report (which just removes the reports from the queue).
             Similarly, the remove hides the photo, and ignores the report.
             See query.php (near the bottom) for more details. -->
        <input type='button' id='ignore' value='Ignore' />
        <input type='button' id='remove' value='Remove' />
        <input type='button' id='disable' value='Disable User' />
        <input type='text' id='msg' placeholder='Why Disabled?' />
        <?php
        echo '</div>';
    }
?>
</body>

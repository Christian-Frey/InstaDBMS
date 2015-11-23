<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen" href="stylesheet.css" />

<script type='text/javascript' src="jquery.min.js"></script>

<script type='text/javascript' src='js/login.js'></script>
</head>
<body>

<div id="login_pics">
	<?php
		//We are going to add in three randomly chosen photos to the login page.
		require_once("conn.php");
		//Determining how many pictures there are in photo
		$result = $mysqli->query("select count(photo_id) from photo");
		$row = mysqli_fetch_assoc($result);
		$length = $row['count(photo_id)'];
		$result->free();

		//We will have three randomly selected images from the database.
		$rand = array();
		do {
			$rand[0] = rand(1, $length);
			$rand[1] = rand(1, $length);
			$rand[2] = rand(1, $length);
			$pruned_rand = array_unique($rand);
        } while (count($rand) != count($pruned_rand));

        //Querying the database for the three images.
        $query =  "select image from photo where photo_id=". $rand[0].
            " or photo_id=". $rand[1]. " or photo_id=". $rand[2];
		$result = $mysqli->query($query);

		while($row = mysqli_fetch_assoc($result))
        {
            //And adding the images to the html.
			$base64 = 'data:image/jpg;base64, '. $row['image'];
            echo '<img alt="Embedded Image" src="' . $base64 . '" />';
            //The EOL puts each img tag on a new time for readability.
			echo PHP_EOL;

		}
		$mysqli->close();
		?>
</div>
<div id="login_fields">
	<form id="login" onsubmit="return false;">
		<input type="text" name="username" id="loginUN" placeholder="Username"><br>
        <input type="password" name="password" id="loginPW" placeholder="Password"><br>
        <!-- This will hold any error messages that may arrise -->
        <div id="failure"></div>
		<input id="login_button" type="submit" value="Log In">
	</form>
	<input id="new_account" type="button" value="Create Account">

</div>
</body>

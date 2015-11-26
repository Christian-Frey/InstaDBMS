<!--
Name: index.php
Author: Christian
Purpose: Provides a landing page when the user navigates to our website.
         It gives them some random photos, and provides a login field
         where they can enter their credentials, or create a new account.
-->

<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<!-- including all of the required files. -->
<link rel="stylesheet" type="text/css" media="screen" href="stylesheet.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/login.js'></script>
</head>

<body>
<div id="login_pics">
	<?php
		// Connecting to the server...*dial up noises*
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
        // Since there are only 3 numbers, it runs until if finds 3 unique on
        // on the first try, and the average running time goes down as
        // more pictures are uploaded (Less chance of collision)
        } while (count($rand) != count($pruned_rand));

        //Querying the database for the three random images.
        $query =  "select image from photo where photo_id=". $rand[0].
            " or photo_id=". $rand[1]. " or photo_id=". $rand[2];
		$result = $mysqli->query($query);

		while($row = mysqli_fetch_assoc($result))
        {
            //adding the images to the html.
			$base64 = 'data:image/jpg;base64, '. $row['image'];
            echo '<img alt="Embedded Image" src="' . $base64 . '" />' . PHP_EOL;

		}
		$mysqli->close();
		?>
</div>
<!--
    providing a login area for the user to enter their username and password.
-->
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

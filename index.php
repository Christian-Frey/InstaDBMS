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
<link rel="stylesheet" type="text/css" media="screen"
			href="css/stylesheet.css" />
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

    //Querying the database for the three random images.
		$query =  "SELECT image FROM photo WHERE hidden <> 1
		 					 ORDER BY RAND() LIMIT 3";
		$result = $mysqli->query($query);
		while($row = mysqli_fetch_assoc($result))
    {
        //adding the images to the html.
				$base64 = 'data:image/jpg;base64, '. $row['image'];
        	echo '<img alt="Embedded Image" src="' . $base64 . '" />'
								. PHP_EOL;

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
			<!-- TODO: fix login not working after a failed login -->
			<!-- TODO: alert not working, change to insert. -->
      <input id="new_account" type="button" value="Create Account">
    </div>
</body>

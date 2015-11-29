<!--
Name: uploadPhoto.php
Author: Christian
Purpose: It allows the user to upload a photo to instaDBMS
-->
<head>
<meta charset="utf-8">
<title>InstaDBMS</title>
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<link rel="stylesheet" href="css/stylesheetUpload.css" media="screen"
      type='text/css'/>

<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/header.js'></script>
<!-- The upload listener handles all the user input for the page -->
<script type='text/javascript' src='js/uploadListener.js'></script>
</head>
<body>
<?php
    // Checking if the user is logged in. If not, it kicks them out.
	$cookie = $_COOKIE['instaDBMS'];
	if (!isset($_COOKIE['instaDBMS']))
		header('Location: index.php');
    require_once('header.php');
    buildHeader();
?>
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

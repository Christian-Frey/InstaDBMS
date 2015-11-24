<head>
<meta charset="utf-8">
<title>InstaDBMS</title>

<link rel="stylesheet" type="text/css" media="screen"
	  href="stylesheetHome.css" />
<!-- pulls the jquery file from the directory above this one -->
<script type='text/javascript' src="../jquery.min.js"></script>
</head>
<body>
<?php
if (!isset($_COOKIE['instaDBMS']))
    header('../index.php');
$uid = $_COOKIE['instaDBMS'];
?>
<div class=header>
    <!--TODO: Replace text with an image -->
    <p id="projectName">instaDBMS</p>
    <!-- TODO: Add search functionality
         if search starts with # -> only search hashtag table
         otherwise -> search both users and hashtags -->
    <input id="searchSite" name='searchSite' type='text'
           placeholder=" Search?">
    <?php
    require_once("../conn.php");
    $stmtUN = $mysqli->prepare("SELECT user_name FROM user where user_id= ?");
    // We cant be sure the user hasn't modified the cookie.
    $stmtUN->bind_param("s", $_COOKIE['instaDBMS']);
    $stmtUN->execute();
    $stmtUN->bind_result($un);
    while ($stmtUN->fetch())
       echo "<p id=user_name>" . $un . "</p>";
?>
</body>

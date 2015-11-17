<head>
<meta charset="utf-8">
<title>Instagram</title>
<link rel="stylesheet" type="text/css" media="screen" href="stylesheet.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/newUserValidation.js'></script>
</head>

<body>
<?php
require_once("conn.php");
?>

<form id="newUser" onsubmit="return false;">
	<p>* Required</p>
    <input type='text' id='username' placeholder='Username'>*<br>
    <input type='password' id='password' placeholder='Password'>*<br>
    <input type='password' id='passwordConfirm' placeholder='Confirm Password'>*<br>
    <input type='text' id='name' placeholder='Your Name'>*<br>
    <select id='gender'>
        <option value='male'>Male</option>
        <option value='female'>Female</option>
        <option value='other'>Other</option>
    </select>*<br>
    <input type='text' id='email' placeholder='E-Mail Address'>*<br>
    <input type='text' id='phonenum' placeholder='Your Phone Number'><br>
    <input type='text' id='website' placeholder='Your Website'><br>
    <input type='text' id='bio' placeholder='A little bit about you'><br>
	<div id='error'></div>
    <input type='submit' id='create' value='Create Account'>
</form>



</body>

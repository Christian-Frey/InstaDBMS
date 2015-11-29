<!--
Name: editProfile.php
Author: Hayly
Purpose: Allows a logged in user to change their account details. It
         autofills the users details so they know what values are currently
         stored. See createUser.php for a similar file.
-->
<head>
<meta charset="utf-8">
<title>InstaDBMS - New Account</title>
<!-- including all of the required files. -->
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheet.css" />
<link rel="stylesheet" type="text/css" media="screen"
      href="css/stylesheetHeader.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/editUserValidation.js'></script>
<script type='text/javascript' src='js/header.js'></script>
</head>

<?php
require_once('header.php');
buildHeader();
 ?>

<body>
<h2>Edit Profile</h2>
<form id="editUser" onsubmit="return false;">
	<p>* Required</p>
	<table>
    	<tr>
		<?php
            // Connecting to the server...*dial up noises*
			require_once("conn.php");

            // Getting the user details of the logged in user.
			$stmtProfile = $mysqli->prepare("SELECT user_name, password, name,
                email, phone, bio, website, gender FROM user WHERE
                user.user_id = ?");
			$stmtProfile->bind_param('i', $_COOKIE['instaDBMS']);

			$stmtProfile->execute();
			$stmtProfile->store_result();
			$stmtProfile->bind_result($user_name, $password, $name, $email, $phone, $bio, $website, $gender);
			$stmtProfile->fetch();
		?>
            <!-- Here we display all of the users data in table form.
                 the current values are autofilled by php, and can be
                 changed by typing over them. -->
			<td>Username</td>
			<td><input type='text' id='username' placeholder='Username' value="<?php echo $user_name;?>"/>*</td>
		</tr>
		<tr>
			<td>
				Password
			</td>
			<td><input type='password' id='password' placeholder='Password' value="<?php echo $password;?>"/></td>
		</tr>
		<tr>
			<td>
				Confirm Password
			</td>
			<td><input type='password' id='passwordConfirm' placeholder='Confirm Password' value="<?php echo $password;?>"/></td>
		</tr>
		<tr>
			<td>
				Name
			</td>
			<td><input type='text' id='name' placeholder='Your Name' value="<?php echo $name;?>"/>*</td>
		</tr>
		<tr>
			<td>
				Gender
			</td>
			<td><select id='gender'>
            <!-- A dropdown for the user to select their gender -->
        	<option value='Other' <?php if ($gender == "O") echo 'selected'; ?>>Unspecified</option>
        	<option value='Male' <?php if ($gender == "M") echo 'selected'; ?>>Male</option>
        	<option value='Female' <?php if ($gender == "F") echo 'selected'; ?>>Female</option>
    	</select/></td>
		</tr>
		<tr>
			<td>
				E-Mail Address
			</td>
			<td><input type='text' id='email' placeholder='E-Mail Address' value="<?php echo $email;?>"/>*</td>
		</tr>
		<tr>
			<td>
				Phone Number
			</td>
			<td><input type='text' id='phonenum' placeholder='Your Phone Number' value="<?php echo $phone;?>"></td>
		</tr>
		<tr>
			<td>
				Website
			</td>
			<td><input type='text' id='website' placeholder='Your Website' value="<?php echo $website;?>"/></td>
		</tr>
		<tr>
			<td>
				Bio
			</td>
			<td><input type='text' id='bio' placeholder='A little bit about you' value="<?php echo $bio;?>"/></td>
		</tr>
	</table>
	<div id='error'></div>
    <!-- The submit button which is handled by js/editUserValidation.js -->
	<input type='submit' id='update' value='Update Account'>
</form>



</body>

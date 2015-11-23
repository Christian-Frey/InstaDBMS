<head>
<meta charset="utf-8">
<title>InstaDBMS - New Account</title>
<link rel="stylesheet" type="text/css" media="screen" href="stylesheet.css" />
<script type='text/javascript' src="jquery.min.js"></script>
<script type='text/javascript' src='js/newUserValidation.js'></script>
</head>

<body>
<h2>Create New Account</h2>
<form id="newUser" onsubmit="return false;">
	<p>* Required</p>
	<table>
    	<tr>
			<td>Username</td>
			<td><input type='text' id='username' placeholder='Username'>*</td>
		</tr>
		<tr>
			<td>
				Password
			</td>
			<td><input type='password' id='password' placeholder='Password'/>*</td>
		</tr>
		<tr>
			<td>
				Confirm Password
			</td>
			<td><input type='password' id='passwordConfirm' placeholder='Confirm Password'/>*</td>
		</tr>
		<tr>
			<td>
				Name
			</td>
			<td><input type='text' id='name' placeholder='Your Name'/>*</td>
		</tr>
		<tr>
			<td>
				Gender
			</td>
			<td><select id='gender'>
					<option value="none"></option>
        	<option value='Male'>Male</option>
        	<option value='Female'>Female</option>
        	<option value='other'>Other</option>
    	</select/></td>
		</tr>
		<tr>
			<td>
				E-Mail Address
			</td>
			<td><input type='text' id='email' placeholder='E-Mail Address'/>*</td>
		</tr>
		<tr>
			<td>
				Phone Number
			</td>
			<td><input type='text' id='phonenum' placeholder='Your Phone Number'></td>
		</tr>
		<tr>
			<td>
				Website
			</td>
			<td><input type='text' id='website' placeholder='Your Website'/></td>
		</tr>
		<tr>
			<td>
				Bio
			</td>
			<td><input type='text' id='bio' placeholder='A little bit about you'/></td>
		</tr>
	</table>
	<div id='error'></div>
	<input type='submit' id='create' value='Create Account'>
</form>



</body>

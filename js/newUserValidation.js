$(document).ready(addListeners);

function addListeners()
{
    $("#create").click(createAccount);
}

function createAccount() 
{
	//This is to reset the errors if they hit the submit button again.
	$("#error").replaceWith("<div id='error'></div>");
	var username = $("#username").val();
	var password = $("#password").val();
	var passwordConfirm = $("#passwordConfirm").val();
	var name = $("#name").val();
	var gender = $("#gender").val();
	var email = $("#email").val();
	var phonenum = $("#phonenum").val();
	var website = $("#website").val();
	var bio = $("#bio").val();

	//There are so many ways to validate e-mail addresses,
	//and none of them work 100% of the time. So lets just 
	//make sure they have an @ somewhere in there and call
	//it a day.
	var eMailRegEx = new RegExp(/@/);
	
	//We want to run checks on the data before we send it 
	//to the DB, to make sure the user is not surprised when 
	//they realized their super long data is cut off.
	
	//First check will be for uniqueness in the username and email.
	//Since we are using ajax, we can do this first and let the other
	//conditions run while we wait for the servers response. 
	$.ajax({
		type: "POST",
		url: "query.php",
		data: {
			"query": "uniqueUserOrPw",
			"username": username,
			"email": email
		},
		dataType: "text",
		//The success callback is on a successful query to the server,
		//and not always a successful result
		success: function (data) {
			if (data == "failure")
			{
				$("<p>That username or E-mail is already taken.<br>" + 
				"Please try a different username or E-mail.</p>")
					.appendTo("#error");
			}
		}
	});
	
	if (username.length > 30) 
	{ 
		$("<p>Username is too long</p>").appendTo("#error");
		return false;
	}
	if (password.length > 32) 
	{
		$("<p>Password is too long</p>").appendTo("#error");
		return false;
	}
	if (password != passwordConfirm) 
	{
		$("<p>Passwords do not match</p>").appendTo("#error");
		return false;
	}
	if (name.length > 140) 
	{
		$("<p>Name is too long</p>").appendTo("#error");
		return false;
	}
	if (email.length > 100) 
	{
		$("<p>E-mail address is too long</p>").appendTo("#error");
		return false;
	}
	if (!eMailRegEx.test(email))
	{
		$("<p>E-mail address is missing an @</p>").appendTo("#error");
		return false;
	}
	if (phonenum.length > 20) 
	{
		$("<p>Your phone number is too long</p>").appendTo("#error");
		return false;
	}
	if (bio.length > 150)
	{
		$("<p>The bio is too long</p>").appendTo("#error");
		return false;
	}
	if (website.length > 100) {
		$("<p>Your website URL is too long</p>").appendTo("#error");
		return false;
	}
	
	//Finally, convert the gender field to one the DB can use:
	switch(gender)
	{
		case('male'):
			gender = 'm';
			break;
		case('female'):
			gender = 'f';
			break;
		case('other'):
			gender = 'o';
			break;
	}
	
	//They made it through alive! Lets get them an account.
	//The query argument is for php to know what block it 
	//should execute to process this request.
	var dataToSend = { 
	"query": "newUser",
	"username": username,
	"password": password,
	"name": name,
	"email": email,
	"phone": phonenum,
	"bio": bio,
	"website": website,
	"gender": gender
	};
	
	$.ajax({
		type: "POST",
		url: "query.php",
		data: dataToSend,
		dataType: "text",
		success: function(data) 
		{
			if (data == "success") 
			{
				window.location("home/home.php");
			}
			if (data == "failure")
			{
				$("error").replaceWith(
				"<p id='error'>Error adding your account. Please try again later.");
			}
		}
	});
	
}

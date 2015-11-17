$(document).ready(addListeners);

function addListeners() 
{
    $("#login_button").click(loginUser);
    $("#new_account").click(createAccount);
}

function loginUser() 
{
    //Passwords are in plaintext, for extra security.
    var username = $("#loginUN").val();
    var password = $("#loginPW").val();

    if (password == "" || username == "") 
    {
        $("#failure").replaceWith("<p id='failure'>Missing Username or Password</p>");
        return;
    }
    
    var dataToSend= { "username": username, "password": password };
    console.log(dataToSend);    

    $.ajax({
        type: "POST",
        url: "checkCredentials.php", 
        data: dataToSend,
        dataType: "text",
        success: function(data) {
            if (data == "success") {
                window.location = 'home/home.php';
            }
            if (data == "failure") {
            $("#failure").replaceWith("<p id='failure'>Invalid username or password</p>");
            }
        }
    });
}
    
function createAccount() 
{
    //Do we want to replace the login form with the account creation fields, 
    //or redirect them to a new page? Should require an equal amount of work.
    window.location="createUser.php";
}

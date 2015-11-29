/*global $ */

// Making sure the DOM is fully loaded before we start adding listeners
$(document).ready(addListeners)

// Adds in all of the listeners for the page, and directs them to the
// appropriate functions.
function addListeners () {
  $('#login_button').click(loginUser)
  $('#new_account').click(createAccount)
}

// Parses the login details the user entered
function loginUser () {
  // Passwords are in plaintext, for extra security
  var username = $('#loginUN').val()
  var password = $('#loginPW').val()

  // Making sure that neither of the fields are blank
  if (password === '' || username === '') {
    $('#failure').replaceWith(
        '<p id="failure">Missing Username or Password</p>')
    return
  }
  // prepackaging the data we want to send to the server in a JSON format
  var dataToSend = {
    'query': 'checkLogin',
    'username': username,
    'password': password
  }

  // Now we can send the data to ther server to be verified
  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: dataToSend,
    dataType: 'text',
    success: function (data) {
      if (data === 'success') {
        // The login details were right, redirect them up to the homepage.
        window.location = 'home.php'
      }
      // The login details were wrong, let them know so they can fix it.
      if (data === 'failure') {
        $('#failure').replaceWith(
          '<p id="failure">Invalid username or password</p>')
      }
    }
  })
}

// If the user wants to create an account, bring them to that page.
function createAccount () {
  window.location = 'createUser.php'
}

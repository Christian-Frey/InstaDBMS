/*global $ */
$(document).ready(addListeners)

function addListeners () {
  $('#update').click(editAccount)
}

function editAccount () {
  // This is to reset the errors if they hit the submit button again.
  $('#error').replaceWith('<div id="error"></div>')
  var username = $('#username').val()
  var password = $('#password').val()
  var passwordConfirm = $('#passwordConfirm').val()
  var name = $('#name').val()
  var gender = $('#gender').val()
  var email = $('#email').val()
  var phonenum = $('#phonenum').val()
  var website = $('#website').val()
  var bio = $('#bio').val()

  // There are so many ways to validate e-mail addresses,
  // and none of them work 100% of the time. So lets just
  // make sure they have an @ somewhere in there and call
  // it a day.
  var eMailRegEx = new RegExp(/@/)

  // We want to run checks on the data before we send it
  // to the DB, to make sure the user is not surprised when
  // they realized their super long data is cut off.

  // Doing check to make sure the values fit within our constraints.

  if (username.length === 0 ||
      password.length === 0 ||
      passwordConfirm.length === 0 ||
      name.length === 0 ||
      email.length === 0) {
    $('<One or more required fields is empty.</p>').appendTo('#error')
    return false
  }
  if (username.length > 30) {
    $('<p>Username is too long</p>').appendTo('#error')
    return false
  }
  if (password.length > 32) {
    $('<p>Password is too long</p>').appendTo('#error')
    return false
  }
  if (password !== passwordConfirm) {
    $('<p>Passwords do not match</p>').appendTo('#error')
    return false
  }
  if (name.length > 140) {
    $('<p>Name is too long</p>').appendTo('#error')
    return false
  }
  if (email.length > 100) {
    $('<p>E-mail address is too long</p>').appendTo('#error')
    return false
  }

  if (!eMailRegEx.test(email)) {
    $('<p>E-mail address is missing an @</p>').appendTo('#error')
    return false
  }
  if (phonenum.length > 20) {
    $('<p>Your phone number is too long</p>').appendTo('#error')
    return false
  }
  if (bio.length > 150) {
    $('<p>The bio is too long</p>').appendTo('#error')
    return false
  }
  if (website.length > 100) {
    $('<p>Your website URL is too long</p>').appendTo('#error')
    return false
  }

  // Finally, convert the gender field to one the DB can use:
  switch (gender) {
    case ('male'):
      gender = 'm'
      break
    case ('female'):
      gender = 'f'
      break
    case ('other'):
      gender = 'o'
      break
  }
  
  var dataToSend = {
    'query': 'updateUser',
    'username': username,
    'password': password,
    'name': name,
    'email': email,
    'phone': phonenum,
    'bio': bio,
    'website': website,
    'gender': gender
  }

  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: dataToSend,
    dataType: 'text',
    success: function (data) {
      if (data === 'success') {
        window.location = 'profile.php'
      } else if (data === 'failure') {
        $('error').replaceWith(
            '<p id="error">Error updating your account.' +
            ' Please try again later.')
	  } else if (data === 'userExists') {
			 $('<p>That username or email is already taken.<br>' +
            'Please try a different username or email.</p>')
          .appendTo('#error')
      } else {
        // Must be some sort of debugging thing.
        console.log(data)
      }
    }
  })
}

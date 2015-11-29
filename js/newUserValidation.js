/*global $ */

/*
 * Name: EditUserValidation.js
 * Author: Christian
 * Purpose: Attempts to provide some checks on what the user enters when
 *          they create their account.
 */

 // Making sure the DOM is fully loaded before we start adding listeners
$(document).ready(addListeners)

// Adds in all of the listeners for the page, and directs them to the
// appropriate functions.
function addListeners () {
  $('#create').click(editAccount)
}

function editAccount () {
  // Resets the error message whenever the user resubmits the form.
  $('#error').replaceWith('<div id="error"></div>')

  // Getting the values of the data out of the forms for processing.
  var username = $('#username').val()
  var password = $('#password').val()
  var passwordConfirm = $('#passwordConfirm').val()
  var name = $('#name').val()
  var gender = $('#gender').val()
  var email = $('#email').val()
  var phonenum = $('#phonenum').val()
  var website = $('#website').val()
  var bio = $('#bio').val()

  // There is no surefire way to validate any possible email address
  // with one simple regular expression or logical expression. Therefore,
  // we will only check for an @ sign, and let the user handle the rest.
  var eMailRegEx = new RegExp(/@/)

  // Checking if any of the required fields have not yet been filed out.
  if (username.length === 0 ||
      password.length === 0 ||
      passwordConfirm.length === 0 ||
      name.length === 0 ||
      email.length === 0) {
    $('<p>One or more required fields is empty.</p>').appendTo('#error')
    return false
  }

  // The username can be a maximum of 30 characters.
  if (username.length > 30) {
    $('<p>Username is too long</p>').appendTo('#error')
    return false
  }

  // The password can be a maximum of 32 characters.
  if (password.length > 32) {
    $('<p>Password is too long</p>').appendTo('#error')
    return false
  }

  // Lets make sure the passwords they entered match
  if (password !== passwordConfirm) {
    $('<p>Passwords do not match</p>').appendTo('#error')
    return false
  }

  // Their full name is limited to 140 characters
  if (name.length > 140) {
    $('<p>Name is too long</p>').appendTo('#error')
    return false
  }

  // Their email is limited to 100 characters
  if (email.length > 100) {
    $('<p>E-mail address is too long</p>').appendTo('#error')
    return false
  }

  // Matching on the RegEx mentioned above
  if (!eMailRegEx.test(email)) {
    $('<p>E-mail address is missing an @</p>').appendTo('#error')
    return false
  }

  // Max of a 20 digit phone - arbitrary number to cover most of the world.
  if (phonenum.length > 20) {
    $('<p>Your phone number is too long</p>').appendTo('#error')
    return false
  }

  // Their biography can be at most 150 characters
  if (bio.length > 150) {
    $('<p>The bio is too long</p>').appendTo('#error')
    return false
  }

  // Their Website URL must be less than 100 chars.
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

  // Encoding the data the user entered into a JSON format
  var dataToSend = {
    'query': 'newUser',
    'username': username,
    'password': password,
    'name': name,
    'email': email,
    'phone': phonenum,
    'bio': bio,
    'website': website,
    'gender': gender
  }

  // Using AJAX to asyncronously send the details to the server to be
  // added. The user is then redirected to the login page to login.
  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: dataToSend,
    dataType: 'text',
    success: function (data) {
      if (data === 'success') {
        window.location = 'profile.php'
      } else if (data === 'failure') {
        // The username or email they tried to use is already in the system.
        $('#error').replaceWith('<p>That username or email is already' +
                'taken.<br>Please try a different username or email.</p>')
      }
    }
  })
}

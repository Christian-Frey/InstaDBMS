/*global $ */

/*
 * Name: profileListener.js
 * Author: Hayly
 * Purpose: Listens to the profile page and processes user input.
 */

// Making sure the DOM is fully loaded before we start adding listeners
$(document).ready(listener)

// Adds in all of the listeners for the page, and directs them to the
// appropriate functions.
function listener () {
  $(document).on('click', '.follow', followUser)
  $(document).on('click', '.log_out', endSession)
  $(document).on('click', '.moderatorPromote', moderatorPromote)
}

// They want to log out, so we can remove the login cookie by setting it
// to a long time in the past.
function endSession () {
  document.cookie = 'instaDBMS=;expires=Thu, 01 Jan 1970 00:00:01 GMT;'
  window.location = 'index.php'
}

// The user wants to follow a user.
function followUser () {
  console.log('Clicked follow button')
  // Sending the data to the server to be processed.
  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: {
      'query': 'followUser',
      'friend_id': $('#friend_id').text()
    },
    dataType: 'text',
    success: function (data) {
      console.log(data)
      // The user has now followed the second user, update the page.
      if (data === 'followed') {
        $('.follow').replaceWith(
            '<a href="javascript:;" class="follow">FOLLOWING</a>')
      }
      // The user has now unfollowed the second user, update the page.
      if (data === 'unfollowed') {
        $('.follow').replaceWith(
            '<a href="javascript:;" class="follow">FOLLOW</a>')
      }
    }
  })
}

function moderatorPromote () {
  var parent = $(this).closest('div')
  var user_id = parent[0].getAttribute('user')
  console.log('Clicked promote button - ' + user_id)
  if ($('.moderatorPromote').text() === 'MODERATOR') {
    return
  }
  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: {
      'query': 'promoteUser',
      'user_id': user_id
    },
    dataType: 'text',
    success: function (data) {
      console.log(data)
      if (data === 'promoted') {
        $('.moderatorPromote').replaceWith(
            '<a href="javascript:;" class="moderatorPromote">MODERATOR</a>')
      } else {
        $('.moderatorPromote').replaceWith(
            '<a href="javascript:;" class="moderatorPromote">PROMOTE TO MODERATOR</a>')
      }
    }
  })
}

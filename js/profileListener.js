/*global $ */
$(document).ready(listener)

function listener () {
  $(document).on('click', '.follow', followUser)
  $(document).on('click', '.log_out', endSession)
  $(document).on('click', '.moderatorPromote', moderatorPromote)
}

function endSession () {
	document.cookie = 'instaDBMS=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	window.location = 'index.php';
}

function followUser () {
  console.log('Clicked follow button')
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
      if (data === 'followed') {
        $('.follow').replaceWith(
            '<a href="javascript:;" class="follow">FOLLOWING</a>')
      }
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
  if ($('.moderatorPromote').text() == "MODERATOR")
	return;
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
      }
      else {
        $('.moderatorPromote').replaceWith(
            '<a href="javascript:;" class="moderatorPromote">PROMOTE TO MODERATOR</a>')
      }
    }
  })
}
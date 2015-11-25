/*global $ */
$(document).ready(listener)

function listener () {
  $(document).on('click', '.follow', followUser)
  $(document).on('click', '.log_out', endSession)
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
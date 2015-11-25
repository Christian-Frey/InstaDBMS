/*global $ */
$(document).ready(listener)

function listener () {
  $(document).on('click', '.follow', followUser)
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

function search (e) {
  var key = e.which
  if (key === 13) { // They hit enter.
    $.ajax({
      type: 'POST',
      url: '../query.php',
      data: {
        'query': 'search',
      },
      dataType: 'text',
      success: function (data) {

      }
    })
  }
}

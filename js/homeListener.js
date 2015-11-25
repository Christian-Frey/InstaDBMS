/*global $ */
$(document).ready(listener)

function listener () {
  $('#searchSite').keyup(search)
  $('[class^=insertComment]').keyup(addComment)
  $(document).on('click', '.heart', likePhoto)
  $('.report').click(reportPhoto)
  $(document).on('click', '#reportButton', submitReport)
}

function addComment (e) {
  var key = e.which
  if (key === 13) {  // They hit enter.
    var photo_id = e.target.id.replace('insertComment', '')
    // We need to parse the comment and look for hashtags, and then
    // add those to the hashtag table.
    var commentString = $('#insertComment' + photo_id).val()
    var token = commentString.split(' ')
    for (var i = 0; i < token.length; i++) {
      if (token[i][0] === '#') {
        // hashtag, lets add it.
        $.ajax({
          type: 'POST',
          url: '../query.php',
          data: {
            'query': 'addHashtag',
            'photo_id': photo_id,
            'hashtag': token[i]
          },
          dataType: 'text',
          success: function (data) {
          }
        })
      }
    }

    $.ajax({
      type: 'POST',
      url: '../query.php',
      data: {
        'query': 'addComment',
        'comment': commentString,
        'photo_id': photo_id,
        'user_name': $('#user_name').text()
      },
      dataType: 'text',
      success: function (data) {
        if (data === 'success') {
          console.log('Insert Successful')
          window.location.href = 'home.php'
        }
      }
    })
  }
}

function likePhoto (e) {
  // var photo_id = e.target.id.replace('heart', '')
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'likePhoto',
      'user_name': $('#user_name').text(),
      'photo_id': $('#photo_id').text()
    },
    dataType: 'text',
    success: function (data) {
      if (data === 'like') {
        $('.heart').replaceWith(
            '<a href="javascript:;" class="heart">Liked</a>')
      }
      if (data === 'unlike') {
        $('.heart').replaceWith(
            '<a href="javascript:;" class="heart">Not Liked</a>')
      }
    }
  })
}

function reportPhoto () {
  var parent = $(this).closest('div').parent()
  var photo = parent[0].getAttribute('class')
  photo = '.' + photo + ' #reportedPlaceholder'
  $(photo).replaceWith(
        '<div id="reportedPlaceholder">' +
        '<form onsubmit="return false;"><select id="reportWhy">' +
        '<option value="1">I do not like this photo</option>' +
        '<option value="2">Picture is spam or a scam</option>' +
        '<option value="3">This photo puts people at risk.</option>' +
        '<option value="4">This photo should not be on' +
        'InstaDBMS</option></select>' +
        '<input type="submit" id="reportButton" value="Report Photo">' +
        '</form></div>')
}
function submitReport () {
  var parent = $(this).closest('div').parent()
  var photoSel = '.' + parent[0].getAttribute('class') + ' #reportedPlaceholder'
  var photo_id = parent[0].getAttribute('class').replace('photo_view', '')
  console.log(photo_id)
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'reportPhoto',
      'photo_id': photo_id,
      'reason': $('#reportWhy').val()
    },
    dateType: 'text',
    success: function (data) {
      console.log(data)
      if (data === 'success') {
        $(photoSel).replaceWith(
            '<p id="reportedPlaceholder">' +
            'Your report has been logged. Thank You.</p>')
      }
    }
  })
}

function search (e) {
  var key = e.which
  if (key === 13) { // They hit enter.
    var searchTerm = $('#searchSite').val()
    $.ajax({
      type: 'POST',
      url: '../query.php',
      data: {
        'query': 'search',
        'search': searchTerm
      },
      dataType: 'text',
      success: function (data) {
        if (data === 'hashtag') {
          searchTerm = searchTerm.substr(1)
          var url = '/hashtagSearch.php?ht=' + searchTerm
          window.location = (url)
        }
        if (data === 'failure') {
          alert('No user found.')
        } else {
          window.location = ('../profile.php?id=' + data)
        }
      }
    })
  }
}

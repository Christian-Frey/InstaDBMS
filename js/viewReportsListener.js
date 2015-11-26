/*global $ alert */
$(document).ready(addListeners)

function addListeners () {
  $(document).on('click', '#ignore', ignoreReport)
  $(document).on('click', '#remove', removePhoto)
  $(document).on('click', '#disable', disableUser)
}

function ignoreReport (e) {
  // This will remove all reports associated with the photo.
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'ignoreReport',
      'photo_id': photoID,
      'rUser': $('.pUsername').text()
    },
    dataType: 'text',
    success: function (data) {
      window.location = 'viewReports.php'
    }
  })
}

function removePhoto () {
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'removePhoto',
      'photo_id': photoID
    },
    dataType: 'text',
    success: function (data) {
      window.location = 'viewReports.php'
    }
  })
}

function disableUser () {
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  var msg = $('.photo_view' + photoID + ' #msg').val()
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'disableUser',
      'photo_id': photoID,
      'msg': msg
    },
    dataType: 'text',
    success: function (data) {
      window.location = 'viewReports.php'
    }
  })
}

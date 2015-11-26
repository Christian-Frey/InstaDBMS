/*global $ */

/*
 * Name: viewReportsListener.js
 * Author: Christian Frey
 * Purpose: Listens to the view reports page, and sends the required data
 *          to the server in order to be processed
 */

// Making sure the DOM is fully loaded before we start adding listeners
$(document).ready(addListeners)

// Adds in all of the listeners for the page, and directs them to the
// appropriate functions.
function addListeners () {
  $(document).on('click', '#ignore', ignoreReport)
  $(document).on('click', '#remove', removePhoto)
  $(document).on('click', '#disable', disableUser)
}

// This will remove all reports associated with the photo
function ignoreReport (e) {
  // Getting the id of the photo the button is associated with
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  // Sending an AJAX request to the server to ignore the report
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
      // The server responded successfully, so lets reload the page to
      // reflect this change
      window.location = 'viewReports.php'
    }
  })
}

// The mod wants to remove the photo, so lets do that
function removePhoto () {
  // Getting the id of the photo the button is associated with
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  // Sending an AJAX request to the server to remove the photo (also ignores
  // (removes) the report serverside)
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'removePhoto',
      'photo_id': photoID
    },
    dataType: 'text',
    success: function (data) { // Reload the page
      window.location = 'viewReports.php'
    }
  })
}

// We want to disable the user.
function disableUser () {
  // Getting the id of the photo the button is associated with
  var parent = $(this).closest('div')
  var photo = parent[0].getAttribute('class')
  var photoID = photo.replace('photo_view', '')
  // Getting the message the mod wrote about why the user is disabled.
  var msg = $('.photo_view' + photoID + ' #msg').val()
  // Sending an AJAX request to the server to remove the photo (also ignores
  // (removes) the report serverside, and removes the photo)
  $.ajax({
    type: 'POST',
    url: '../query.php',
    data: {
      'query': 'disableUser',
      'photo_id': photoID,
      'msg': msg
    },
    dataType: 'text',
    success: function (data) { // reload the page.
      window.location = 'viewReports.php'
    }
  })
}

/*global $ alert */

/*
 * Name: homeListener.js
 * Author: Christian
 * Purpose: Listens for all the events that may occur on the
 *          homepage, and sends the required data to the correct
 *          location to be processed.
 */

// Making sure the DOM is fully loaded before we start adding listeners
$(document).ready(listener)

// Adds in all of the listeners for the page, and directs them to the
// appropriate functions.
function listener () {
  $('#searchSite').keyup(search)
  // This is a substring match at the start of the tag id. It searchs for all
  // IDs that begin with 'insertComment'. This allows us to embed the
  // photo ID into the id, so we know which comment field the keystroke is
  // coming from.
  $('[id^="insertComment"]').keyup(addComment)
  $(document).on('click', '.heart', likePhoto)
  $('.report').click(reportPhoto)
  $(document).on('click', '#reportButton', submitReport)
}

// This function parses the correct comment field and sends the data off
// in order to be added to the DB by query.php
function addComment (e) {
  // This function is called everytime the user types a letter in any of
  // the comment fields.
  var key = e.which
  if (key === 13) {  // They hit enter.
    var photo_id = e.target.id.replace('insertComment', '')
    // We need to parse the comment and look for hashtags, and then
    // add those to the hashtag table.
    var commentString = $('#insertComment' + photo_id).val()
    var token = commentString.split(' ')
    for (var i = 0; i < token.length; i++) {
      // Checking if the first letter of each word is a #
      if (token[i][0] === '#') {
        // hashtag, lets add it to the hashtag table.
        $.ajax({
          type: 'POST', // POST request
          url: '../query.php', // Where to send the data to
          data: { // The data to send, in a JSON Key: value arrangement.
            'query': 'addHashtag',
            'photo_id': photo_id,
            'hashtag': token[i]
          },
          dataType: 'text', // The format we expect the response to be in.
          success: function (data) { // And what to do when we get a result.
          }
        })
      }
    }

    // Now we can add the entire comment, hashtags and all, into the
    // comment table. Same structure as above.
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
          // The insertion was a success, so lets refresh the page so
          // the user can see their shiny new comment.
          console.log('Insert Successful')
          window.location.reload(true)
        }
      }
    })
  }
}
// This function allows the user to like and unlike a photo.
function likePhoto (e) {
  // Standard AJAX request. See addComment for annotations.
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
      // They liked the photo, so we can update the page to reflect that.
      if (data === 'like') {
        $('.heart').replaceWith(
            '<a href="javascript:;" class="heart">Liked</a>')
      }
      // They unliked the photo, so we can update the page to reflect that.
      if (data === 'unlike') {
        $('.heart').replaceWith(
            '<a href="javascript:;" class="heart">Not Liked</a>')
      }
    }
  })
}

// The user has clicked on the report photo button. We will add
// in the options required to submit their report.
function reportPhoto () {
  // Determining which picture the user chose to report
  var parent = $(this).closest('div').parent()
  var photo = parent[0].getAttribute('class')
  photo = '.' + photo + ' #reportedPlaceholder'
  // The reportedPlaceholder was a div with nothing in it. Now, we add
  // in a drop down menu to let the user choose why they are reporting
  // the image. If we want to add more reasons later (up to 99), we con
  // do it from here, and update query.php.
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

// The user has filed in their report, and clicked the report Photo
// button. This function sends that data to the server.
function submitReport () {
  // Determining which picture the user chose to report
  var parent = $(this).closest('div').parent()
  var photoSel = '.' + parent[0].getAttribute('class') + ' #reportedPlaceholder'
  var photo_id = parent[0].getAttribute('class').replace('photo_view', '')
  // Now we can send the data to the server using AJAX
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
      // The report was completed successfully, let the user know.
      if (data === 'success') {
        $(photoSel).replaceWith(
            '<p id="reportedPlaceholder">' +
            'Your report has been logged. Thank You.</p>')
      }
    }
  })
}

// This function powers the client side of the search function.
function search (e) {
  // Checking to see if they pressed the enter key.
  var key = e.which
  if (key === 13) { // They hit enter.
    var searchTerm = $('#searchSite').val()
    // We now send the whole search term to the server to be processed.
    $.ajax({
      type: 'POST',
      url: '../query.php',
      data: {
        'query': 'search',
        'search': searchTerm
      },
      dataType: 'text',
      success: function (data) {
        // The search they entered was a hashtag, bring them to
        // the page for that hashtag.
        if (data === 'hashtag') {
          searchTerm = searchTerm.substr(1)
          var url = '../searchResults.php?search=' + searchTerm
          window.location = (url)
        // They were searching for a user. Here, we found no user by
        // that name, so we let them know that their search is wrong.
        } else if (data === 'failure') {
          alert('No user found.')
        // They found a user, so we can bring them to the users page.
        } else {
          window.location = ('../profile.php?id=' + data)
        }
      }
    })
  }
}

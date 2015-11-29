/*global $ alert*/

/*
 * Name: header.js
 * Author: Christian
 * Purpose: Places all the header information in one place, because maintaining
 * 10 different files all with a different version of the header is tedious
 * and asking for bugs.
 */

$(document).ready(listeners)

function listeners () {
  $('#searchSite').keyup(search)
  $(document).on('click', '.log_out', endSession)
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
      url: 'query.php',
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
          var url = 'searchResults.php?search=' + searchTerm
          window.location = (url)
        // They were searching for a user. Here, we found no user by
        // that name, so we let them know that their search is wrong.
        } else if (data === 'failure') {
          alert('No user found.')
        // They found a user, so we can bring them to the users page.
        } else {
          window.location = ('profile.php?id=' + data)
        }
      }
    })
  }
}

function endSession () {
  document.cookie = 'instaDBMS=;expires=Thu, 01 Jan 1970 00:00:01 GMT;'
  window.location = 'index.php'
}

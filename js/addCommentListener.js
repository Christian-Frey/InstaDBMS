/*global $ */
$(document).ready(addComment)

function addComment() {
  $(".insertComment").keyup(function (e) {
	var key = e.which;
	if (key == 13) { // They hit enter.
	  $.ajax({
		  type: 'POST',
		  url: '../query.php',
		  data: {
			'query': 'addComment',
			'comment': $('.insertComment').val(),
            'photo_id': $('#photo_id').text(),
			'user_name': $('#user_name').text()
		  },
		  dataType: 'text',
		  success: function (data) {
            if (data === 'success') {
              console.log("Insert Successful");
              window.location.href="home.php"
          }
		}
	  })
	}
  });
}

/*global $ */
$(document).ready(listener)

function listener() {
  $(".insertComment").keyup(addComment)
  $(document).on('click', '.heart', likePhoto)
  $(".report").click(reportPhoto)
  $(document).on('click', '#reportButton', submitReport)
}

function addComment(e) {
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
              console.log("Insert Successful")
              window.location.href="home.php"
          }
		}
	  })
    }
}

function likePhoto() {
    console.log("Clicked like button");
    $.ajax({
        type: 'POST',
        url: '../query.php',
        data: {
            'query': 'likePhoto',
            'user_name': $('#user_name').text(),
            'photo_id': $("#photo_id").text()
        },
        dataType: 'text',
        success: function (data) {
            console.log(data);
            if (data === 'like') {
                $('.heart').replaceWith(
                    '<a href="javascript:;" class="heart">LIKED</a>')
            }
            if (data === 'unlike') {
                $('.heart').replaceWith(
                    '<a href="javascript:;" class="heart">NOT LIKED</a>')
            }
        }
    });
}

function reportPhoto() {
    $('#reportedPlaceholder').replaceWith('<div id="reportedPlaceholder">' + 
        '<form onsubmit="return false;"><select id="reportWhy">' +
        '<option value="1">I do not like this photo</option>' +
        '<option value="2">Picture is spam or a scam</option>' +
        '<option value="3">This photo puts people at risk.</option>' +
        '<option value="4">This photo should not be on' +
        'InstaDBMS</option></select>' +
        '<input type="submit" id="reportButton" value="Report Photo">' +
        '</form></div>')
}
function submitReport() {
    $.ajax({
        type: 'POST',
        url: '../query.php',
        data: {
            'query': 'reportPhoto',
            'photo_id': $('#photo_id').text(),
            'reason': $('#reportWhy').val()
        },
        dateType: 'text',
        success: function (data) {
            console.log(data);
            if (data === 'success') {
                $("#reportedPlaceholder").replaceWith(
                    '<p id="reportedPlaceholder">' +
                    'Your report has been logged. Thank You.</p>')
            }
        }
    })
}

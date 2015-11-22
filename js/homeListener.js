/*global $ */
$(document).ready(listener)

function listener() {
  $(".insertComment").keyup(addComment)
  $(document).on('click', '.heart', likePhoto)
  $(".report").click(reportPhoto)
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
    console.log("here");
}

function reportPhoto() {

}

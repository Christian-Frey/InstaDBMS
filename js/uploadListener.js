/*global $,FileReader,alert */

/*
 * Name: uploadListener.js
 * Author: Christian
 * Purpose: To handle any clicks or input on the uploadPhoto.php page
*/

// Waiting until the DOM is loaded to add the listener
$(document).ready(addListeners)

// Adding the upload listener
function addListeners () {
  $('#upload').on('click', uploadPhoto)
  $('input').change(displayPhoto)
}

var imageString

// This function will display the preview of the image
// the user wishes to upload.
// Thanks to https://developer.mozilla.org/en-US/docs/
// Web/API/FileReader/readAsDataURL for the example
function displayPhoto () {
  var preview = document.querySelector('img')
  var filename = $('#image').val()
  if (!filename.endsWith('.jpg') || filename.endsWith('.jpeg')) {
    alert('File must be .jpg or .jpeg')
    return
  }
  var image = document.querySelector('input[type=file]').files[0]
  var imgReader = new FileReader()
  imgReader.onloadend = function () {
    preview.src = imgReader.result
    imageString = imgReader.result
  }
    // Making sure an image is uploaded
  if (image) {
    imgReader.readAsDataURL(image)
  } else {
    preview.src = ''
  }
}

function uploadPhoto (e) {
  // Sending the image data to the server
  $.ajax({
    type: 'POST',
    url: 'query.php',
    data: {
      'query': 'uploadPhoto',
      'image': imageString
    },
    dataType: 'text',
    beforeSend: function () {
      var result = window.confirm('Are you sure you want to upload?')
      return result
    },
    success: function (data) {
      window.location = 'home.php'
      console.log(data)
    }
  })
}

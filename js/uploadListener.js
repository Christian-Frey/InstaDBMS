/*global $,FileReader */

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
}

function uploadPhoto (e) {
  // Thanks to https://developer.mozilla.org/en-US/docs/
  // Web/API/FileReader/readAsDataURL for the example
  var preview = document.querySelector('img')
  var image = $('#image')[0].files[0]
  var imgReader = new FileReader()
  imgReader.onloadend = function () {
    preview.src = imgReader.result
    var imageString = imgReader.result
    // Sending the image data to the server
    $.ajax({
      type: 'POST',
      url: '../query.php',
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
        console.log(data)
      }
    })
  }
  // Making sure an image is uploaded
  if (image) {
    imgReader.readAsDataURL(image)
  } else {
    preview.src = ''
  }
}

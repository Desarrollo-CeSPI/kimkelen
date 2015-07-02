/**
 * UniquenessChecker JS plugin.
 *
 * @author José Nahuel Cuesta Luengo <nahuelcuestaluengo@gmail.com>
 *
 * @param target The target DOM element (either selector or jQuery object) to bind to.
 */
var UniquenessChecker = function(target) {
  var
    $target = $(target),
    url     = $target.data('url');

  if (typeof url === 'undefined' || null === url) {
    // Nothing to do here without an URL
    return;
  }

  // Create the messages
  var $successMessage = $('<div />', {
      'text': $target.data('successmessage') || 'Available',
      'class': 'uniqueness-alert alert-message success'
    }).hide();

  var $errorMessage = $('<div />', {
      'text': $target.data('errormessage') || 'Unavailable',
      'class': 'uniqueness-alert alert-message warning'
    }).hide();

  // Add them after the target element
  $target.after($successMessage, $errorMessage);

  // Checking function
  function check() {
    var value = jQuery.trim($target.val());

    if ('' === value) {
      return false;
    }

    $.post(
      url,
      { 'query': value, 'id': $target.data('id') },
      function(response) {
        if (response) {
          $errorMessage.hide();
          $successMessage.show();
        } else {
          $errorMessage.show();
          $successMessage.hide();
        }
      },
      'json'
    );
  }

  $target.on($target.data('event') || 'keyup', check);
};

// Automatically initialize any element which has a
// data-plugin attribute that contains "uniqueness-checker".
// Using a contains selector allows to have more than one plugin
// in the same DOM element.
jQuery(function($) {
  $('[data-plugin*="uniqueness-checker"]').each(function() {
    new UniquenessChecker(this);
  });
});
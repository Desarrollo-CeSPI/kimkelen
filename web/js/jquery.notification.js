jQuery.prototype.notification = function(options)
{
  opts = jQuery.extend({}, {'duration': 5, 'show-method': 'slideDown', 'hide-method': 'slideUp'}, options);

  // hide the element
  jQuery(this.selector).css('display', 'none');

  // slide down the element
  setTimeout("jQuery('"+this.selector+"')."+opts['show-method']+"();", 0.5 * 1000);

  // slide up the notification when it's clicked
  eval("jQuery('"+this.selector+"').click(function(){jQuery('"+this.selector+"')."+opts['hide-method']+"()})");

  // slide up the notification
  setTimeout("jQuery('"+this.selector+"')."+opts['hide-method']+"();", opts['duration'] * 1000);
}

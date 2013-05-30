jQuery.fn.centerHorizontally = function (elementWidth) {
  if (typeof elementWidth == "undefined" || elementWidth == null) elementWidth = this.width();
  this.css("position", "absolute");
  this.css("left", parseInt((jQuery(window).width() - elementWidth)/2 + jQuery(window).scrollLeft()));
  return this;
}

jQuery.fn.ensureVisibleHeight = function() {
  var windowHeight = jQuery(window).height();
  if (jQuery(this).offset() != null)
  {
    var topOffset    = jQuery(this).offset().top + 40;
    
    if (windowHeight < this.height() + topOffset)
    {
      this.css("height", (windowHeight - topOffset) + "px");
      this.css("overflow-y", "auto");
    }
    else if (windowHeight > this.getUnscrolledHeight() + topOffset)
    {
      this.css("height", this.getUnscrolledHeight() + "px");
      this.css("overflow-y", "hidden");
    }
  }
}

jQuery.fn.getUnscrolledHeight = function() {
  var scrollProperty = this.css("overflow-y");
  var heightProperty = this.height();

  this.css("overflow-y", "hidden");
  this.css("height", "auto");

  var height = this.height();

  this.css("overflow-y", scrollProperty);
  this.css("height", heightProperty);
  
  return height;
}

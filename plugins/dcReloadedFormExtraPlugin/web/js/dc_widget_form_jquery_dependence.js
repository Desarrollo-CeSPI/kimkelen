/* 
 * This is the Javascript source to add dependency behaviour to widgets
 */

var dcWidgetFormJqueryDependence={
  observed_widgets: [],
  /**
   * Adds a new dependency widget
   */
  addDependency: function ( widget)
  {
    var observed_array=[];

    if ( typeof widget.options.observed_id === "string" )
    { 
      observed_array.push(widget.options.observed_id)
    }
    else
    {
      observed_array=widget.options.observed_id;
    }
    widget.observed_array=observed_array;
    this.updateObservedWidgets(widget);
    jQuery.each(observed_array, function()
     {
        jQuery("#"+this).live(
          widget.options.event,
          widget,
          dcWidgetFormJqueryDependence.dependencyUpdated);
     });
  },
  updateDependenciesFor: function (widget)
  {
      jQuery("#"+widget.id).trigger(widget.options.event, ["automated_ajax_dependence"]);
  },
  /**
   * This is the callback that will be called when some dependency changes
   */
  dependencyUpdated: function (event)
  {
    var observed_values = {};
    var widget = event.data;
    jQuery.each(widget.observed_array, function ()
    {
      if (jQuery("#"+this).attr("type") == "checkbox")
      {
        observed_values[this]= jQuery("#"+this).is(":checked");
      }
      else
      {
        observed_values[this]= jQuery("#"+this).val();
      }
    });
    jQuery.ajax(
    {
      async: true,
      type: 'POST',
      url: widget.options.callback,
      beforeSend: function ()
      {
        jQuery("#"+widget.update_id).html(widget.loading_image);
      },
      data: { 
        widget: widget,
        observed_values: observed_values
      },
      error: function(request, textStatus, errorThrown)
      {
        jQuery("#"+widget.update_id).html('<span class="error">'+textStatus+'</span>');
      },
      success: function(data, textStatus, request)
      {
        jQuery("#"+widget.update_id).html(data);
      }
    });
  },
  updateObservedWidgets: function (widget)
  {
    if (this.observed_widgets.length == 0)
    {
      jQuery(window).load(function(){
        jQuery.each(dcWidgetFormJqueryDependence.observed_widgets, function(){
          if (this.root)
          { 
            jQuery("#"+this.observed_id).trigger(this.event, ["automated_ajax_dependence"]);
          }
        });
      });
    }
    jQuery.each(widget.observed_array,function (){
        var object={
          observed_id: this,
          id:          widget.id,
          event:       widget.options.event,
          root:        true
        };
        jQuery.each(dcWidgetFormJqueryDependence.observed_widgets,function(){
          if ( this.observed_id == object.id )
          {
            this.root=false;
          }
          if ( this.id == object.observed_id )
          {
            object.root=false;
          }
        });
        dcWidgetFormJqueryDependence.observed_widgets.push(object);
    });
  }
}


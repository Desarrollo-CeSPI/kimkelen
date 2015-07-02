function pmWidgetFormJQuerySearch()
{
  this.url = "";
  this.search_widget_id = "";
  this.update_div_id = "";
  this.hidden_widget_id = "";
  this.preview_div_id = "";
  this.select_image = "";
  this.deselect_image = "";
  this.serialized_options = "";
  this.js_var_name = "";

  this.search = function(page)
  {
    page = typeof(page) != 'undefined' ? page : 0;

    value = jQuery(this.search_widget_id).val();

    var instance = this;

    jQuery.ajax({
      url: this.url,
      type: "POST",
      data:
      {
        page: page,
        search: value,
        serialized_options: this.serialized_options,
        js_var_name: this.js_var_name
      },
      success: function(data)
      {
        jQuery(eval(instance.js_var_name+".update_div_id")).html(data);
        jQuery(eval(instance.js_var_name+".update_div_id")).show();
      }
    });
  };

  this.select = function(value, text)
  {
    jQuery(this.hidden_widget_id).val(value);
    jQuery(this.hidden_widget_id).change();
    jQuery(this.preview_div_id).html(text);
    jQuery(this.update_div_id).hide();

    this.getDeselectLink();
  };

  this.deselect = function()
  {
    jQuery(eval(this.js_var_name+".hidden_widget_id")).val("");
    jQuery(eval(this.js_var_name+".hidden_widget_id")).change();
    jQuery(eval(this.js_var_name+".preview_div_id")).html("");
  };

  this.getSelectLink = function(value, text)
  {
    // commented out because we may want to select something with an string as the id
    //value = parseInt(value);
    instance = this;
    jQuery("<a><img src='"+eval(this.js_var_name+".select_image")+"'/></a>")
    	.click(function ()
    	{
    	  eval(instance.js_var_name+".select('"+value+"', '"+text+"');")
    	})
    	.prependTo(jQuery("#result_"+value));
  }

  this.getDeselectLink = function()
  {
    var instance = this;

    jQuery("<a><img src='"+this.deselect_image+"'/></a>")
  	  .click(function()
  	  {
  	    eval(instance.js_var_name+".deselect();");
  	  })
  		.appendTo(jQuery(this.preview_div_id));
  }

  this.displayNoResultsFoundLabel = function()
  {
    jQuery("<div>"+eval(this.js_var_name+".no_results_found_label")+"</div>").appendTo(jQuery(eval(this.js_var_name+".update_div_id")));
  }
}

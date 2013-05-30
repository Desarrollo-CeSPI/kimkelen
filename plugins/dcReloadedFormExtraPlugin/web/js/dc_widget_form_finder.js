var dcWidgetFormFinder = {
  register: function(selector, conf)
  {
    var $context = jQuery(selector);
    var defaults = {
      root: $context,
      loader: false,
      submit: false,
      results: false,
      field: false,
      selection: false,
      form: false,
      collapser: false,
      error: false,
      multiple: false,
      default_text: 'No value selected',
      delete_text : 'Are you sure?',
      delete_image: 'images/delete.png'
    };

    var $conf = jQuery.extend(defaults, conf);

    $context.data('dc_widget_form_finder', $conf);

    if (!$context.hasClass('dc_widget_form_finder'))
    {
      $context.addClass('dc_widget_form_finder');
    }

    // Register this as a listener for the submit button's 'click' events
    jQuery($conf.submit).click(this.search_callback);

    jQuery('.dc_widget_form_finder .dc_widget_form_finder_selection_delete').live('click', this.delete_item_callback);

    // Register this as a listener for ajax error events
    jQuery($conf.submit).ajaxError(this.error_callback);

    // Catch any 'ENTER' event on form elements
    jQuery(':input', $context).keypress(this.enter_callback);
  },

  error_callback: function() {
    var conf = dcWidgetFormFinder.get_conf(this);

    if (false != conf)
    {
      jQuery(conf.error).fadeIn(500);
      jQuery(conf.loader).fadeOut(500);
      jQuery(conf.submit).removeAttr('disabled');
    }
  },

  search_callback: function() {
    var
      context = dcWidgetFormFinder.get_context(this),
      conf = dcWidgetFormFinder.get_conf(this);

    if (false != conf)
    {

      jQuery(conf.submit).attr('disabled', true);
      jQuery(conf.loader).fadeIn(500);
      jQuery(conf.error).fadeOut(500);
      jQuery(conf.results).slideUp(250);

      params = jQuery(':input', context).serialize();

/*
      conf.request = jQuery.getJSON(conf.url, params, function(data) {
        dcWidgetFormFinder.update(conf, data);

        jQuery(conf.loader).fadeOut(500);
        jQuery(conf.submit).removeAttr('disabled');
      });
*/
      conf.request = jQuery.ajax({
        url: conf.url, 
        dataType: 'json',
        data: params,
        type: 'POST',
        success: function(data) {
          dcWidgetFormFinder.update(conf, data);

          jQuery(conf.loader).fadeOut(500);
          jQuery(conf.submit).removeAttr('disabled');
        }
      });

      dcWidgetFormFinder.set_conf(this, conf);
    }
  },

  enter_callback: function(event) {
    // Catch any enter key press
    if (event.which == '13')
    {
      // Prevent the whole form from submitting itself
      event.preventDefault();

      // Instead, submit only the finder form
      var conf = dcWidgetFormFinder.get_conf(this);

      jQuery(conf.submit).click();
    }
  },

  select_callback: function() {
    var
      conf = dcWidgetFormFinder.get_conf(this),
      field = jQuery(conf.field),
      $this = jQuery(this);

    if (conf.multiple)
    {
      if (field.find("option[value='" + $this.attr('id') +"']").length == 0)
      {
        if (field.find('option').length == 0)
        {
          jQuery(conf.selection).find('span:first').hide();
        }
        var option = jQuery('<option selected="selected"/>').val($this.attr('id')).html($this.html());
        field.append(option);
        var span = jQuery('<span class="dc_widget_form_finder_selection_item"/>').html($this.html());
        var del = jQuery('<a class="dc_widget_form_finder_selection_delete"><img src="'+ conf.delete_image +'"/></a>');
        del.data('related_id', $this.attr('id'));
        
        jQuery(conf.selection).append(span);
        jQuery(conf.selection).append(del);
        jQuery(conf.selection).append('<br/>');

        $this.closest('.document_finder_result_item_container').fadeOut(500);
      }
    }
    else
    { 
      jQuery(conf.selection).html('<span class="dc_widget_form_finder_selection_item">'+$this.html()+'</span>');
      
      jQuery(conf.selection).next('.dc_widget_form_finder_selection_single_delete').detach();
      
      var del = jQuery('<a class="dc_widget_form_finder_selection_single_delete"><img src="'+ conf.delete_image +'"/></a>');
      del.click(function()
      {
        jQuery(conf.selection).html('<span class="dc_widget_form_finder_selection_item unselected">'+conf.default_text+'</span>');
        field.val('').change();
        $(this).detach();
      });
      jQuery(conf.selection).parent('div.dc_widget_form_finder_selection_container').append(del);
      
      field.val($this.attr('id')).change();
      jQuery(conf.form).slideUp(500, function() {
        jQuery(conf.results).hide();
        jQuery(conf.collapser).fadeIn(500);
      });
    }

    return false;
  },

  delete_item_callback: function() {
    var
      conf = dcWidgetFormFinder.get_conf(this),
      field = jQuery(conf.field),
      $this = jQuery(this);

    if (confirm(conf.delete_text))
    {
      field.find("option[value='" + $this.data('related_id') +"']").detach();

      jQuery(this).prev().detach();
      jQuery(this).next().detach();
      jQuery(this).detach();

      if (field.find('option').length == 0)
      {
        jQuery(conf.selection).find('span:first').show();
      }
    }

    return false;
  },

  get_conf: function(object) {
    // Get the configuration object for an object inside a finder form
    return this.get_context(object).data('dc_widget_form_finder') || false;
  },

  set_conf: function(object, conf) {
    // Set the configuration object for an object inside a finder form
    return this.get_context(object).data('dc_widget_form_finder', conf);
  },

  get_context: function(object) {
    // Get the context jQuery object for an object inside a finder form
    return jQuery(object).closest('.dc_widget_form_finder');
  },

  update: function(conf, data) {
    var target = jQuery(conf.results);
    
    target.children('.dc_widget_form_finder_result').detach();

    jQuery.each(data, function(index, element) {
      jQuery('<a class="dc_widget_form_finder_result" href="#" />')
        .attr('id', index)
        .html(element)
        .click(dcWidgetFormFinder.select_callback)
        .appendTo(target);
    });

    target.slideDown(500);

    return false;
  },

  cancel: function(object) {
    var conf = this.get_conf(object);

    if (conf.request)
    {
      conf.request.abort();
      jQuery(conf.loader).fadeOut(500);
      jQuery(conf.submit).removeAttr('disabled');
      conf.request = false;

      this.set_conf(object, conf);
    }
  }
}

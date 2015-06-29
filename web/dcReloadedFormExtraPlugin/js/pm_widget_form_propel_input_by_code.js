;(function($)
{
	$.fn.extend(
	{
	  get_value_for_code: function(url, serialized_widget_options)
	  {
	    id = $(this).attr('id');
	    code = $(this).val();
	    
	    $.ajax({
	      url: url,
	      data: 'code='+code+'&serialized_widget_options='+serialized_widget_options,
	      success: function(data)
	      {
	        $('#'+id+'_result').html(data);
	      }
	    });
	  },
	  
	  check_for_code: function(url, serialized_widget_options)
	  {
	    element = this;
	    var id = $(this).attr('id');
	    
	    initial_code = $('#'+id).val();
	    
	    if (initial_code != '')
	    {
	      $(this).get_value_for_code(url, serialized_widget_options);
	    }
	    
	    $(this).keyup(function()
	    {
	      var code = $('#'+id).val();
	      
	      if (code.length >= 3)
	      {
	        $('#'+id).get_value_for_code(url, serialized_widget_options);
	      }
	      else
	      {
	        $('#'+id+'_result').html('');
	      }
	    });
    }
  });
})(jQuery);
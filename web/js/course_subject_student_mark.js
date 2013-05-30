function free_mark(name_free_element, name_element)
{      
  var is_free_element = jQuery('#' + name_free_element);                                
  var element = jQuery('#' + name_element);                                
  
  if (is_free_element.attr('checked'))
  {
    element.attr('disabled',true); 
    element.val(0); 
    element.hide();
  }
  else
  {    
    element.attr('disabled',false); 
    element.val(''); 
    element.show();
  }
}
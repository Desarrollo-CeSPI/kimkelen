var WidgetChangeForCredentials =
{
  toAdvancedMode: function(id)
  {
    jQuery('#div_without_'+id).hide();
    jQuery('#div_with_'+id).show();
    jQuery('#div_button_without_'+id).hide();
    jQuery('#div_button_with_'+id).show();
  },
  toNormalMode: function(id)
  {
    jQuery('#div_without_'+id).show();
    jQuery('#div_with_'+id).hide();
    jQuery('#div_button_without_'+id).show();
    jQuery('#div_button_with_'+id).hide();
  },
  addObservers: function(hidden_id, without_id, with_id)
  {
    if (jQuery(without_id))
    {
      jQuery(without_id).change(function()
      {
        jQuery(hidden_id).val(jQuery(without_id).val());
        if ($(with_id))
        {
          jQuery(with_id).val(jQuery(without_id).val());
        }
      });
    }
    if (jQuery(with_id))
    {
      jQuery(with_id).change(function()
      {
        jQuery(hidden_id).val(jQuery(with_id).val());
        if (jQuery(without_id))
        {
          jQuery(without_id).val(jQuery(with_id).val());
        }
      });
    }
  }
}
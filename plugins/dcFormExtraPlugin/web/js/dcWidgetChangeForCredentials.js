var WidgetChangeForCredentials=
{
  toAdvancedMode: function(id)
  {
    $('div_without_'+id).hide();
    $('div_with_'+id).show();
    $('div_button_without_'+id).hide();
    $('div_button_with_'+id).show();
  },
  toNormalMode: function(id)
  {
    $('div_without_'+id).show();
    $('div_with_'+id).hide();
    $('div_button_without_'+id).show();
    $('div_button_with_'+id).hide();
  },
  addObservers: function (hidden_id,without_id,with_id)
  {
    if ($(without_id)) {
      Event.observe(without_id, 'change',function(){
        $(hidden_id).value=$(without_id).value;
        if ($(with_id)) $(with_id).value=$(without_id).value;
      });
    }
    if ($(with_id)){
      Event.observe(with_id, 'change',function(){
        $(hidden_id).value=$(with_id).value;
        if ($(without_id)) $(without_id).value=$(with_id).value;
      });
    }
  }
}

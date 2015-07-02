/*
 * Singleton class
 */
var csWidgetFormStudentMany={

/**
 * Add selected options from_source_id select to to_associated_id section
 * Then call an ajax url action to finally commit changes
 */
  addSelected: function (from_source_id, associated_name, to_associated_section_id, post_url_action, fixed_values)
  { var toadd=$A();
    for (var i = 0; i < $(from_source_id).options.length; i++)
    {
      if ( $(from_source_id).options[i].selected )
      {
        toadd.push($(from_source_id).options[i].value);
        $(from_source_id).options[i] = null;
        --i;
      }
    }
    $(this).commitAddSelected(toadd, associated_name, to_associated_section_id,post_url_action,fixed_values);
  },

  getAssociatedIds: function (selected_container)
  {
    ret = $A();
    $(selected_container).descendants().each( function (e){
      if (e.type == "hidden")
      {
        ret.push(e.value);
      }
    });
    return ret;
  },
  /**
   * Finish addSelected action
   */
  commitAddSelected: function (toadd, associated_name, associated_section_id, post_url_action, fixed_values)
  {
     var associated=$(this).getAssociatedIds(associated_section_id).concat(toadd).toJSON();
     
     new Ajax.Updater($(associated_section_id), post_url_action,{
        asynchronous:false,
        evalScripts:true,
        parameters: 'name='+associated_name+'&associated='+associated+'&fixed_values='+fixed_values
      });
  },
  /**
   * Updates filter criteria container with an ajax action
   */
  updateContainer: function(container_id, url, params, associated_section_id )
  {
      new Ajax.Updater($(container_id), url,{
        asynchronous:false,
        evalScripts:true,
        parameters: params+'&associated=' +$(this).getAssociatedIds(associated_section_id).toJSON()
      });
  },

  deleteSelected: function (associated_section_id,id,container_id,url)
  { var removed=false;
    
    $(associated_section_id).descendants().each( function (e){
      if ((e.type == "checkbox")&&e.checked)
      {
        e.up().up().remove();
        removed=true;
      }
    });
    if ($(id))
    {
      params=$(id).value;
      if (removed && (params.length > 0))
      {
        new Ajax.Updater($(container_id), url,{
          asynchronous:false,
          evalScripts:true,
          parameters: params
        });
      }
    }
  },
  changeCurrentFilter: function(id,container_id,url,params)
  {
      $(id).value=params;
      new Ajax.Updater($(container_id), url,{
        asynchronous:false,
        evalScripts:true,
        parameters: params
      });
  }


}

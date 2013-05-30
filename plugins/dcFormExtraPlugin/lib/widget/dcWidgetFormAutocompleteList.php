<?php

/**
 * dcWidgetFormAutocomplete represents an autocomplete input with a list below.
 * Allows you to add two ajaxs calls: 
 *    save_method:   add a new item to the list, in this method you should do all the actions that you need when the user
 *                   adds a new item from the autocomplete input
 *
 *    delete_method: to revert the action applied in save_method, and the widget remove the item from the list after call this method
 *
 * @package    symfony
 * @subpackage widget
 * @author     Tomas E. Cordoba <cordoba.tomas@gmail.com>
 * @author     Tomas E. Cordoba <cordoba.tomas@gmail.com>
 */

class dcWidgetFormAutocompleteList extends sfWidgetFormJQueryAutocompleter
{

  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addRequiredOption('save_method');
    $this->addRequiredOption('delete_method');
    $this->addRequiredOption('peer_method');
    $this->addRequiredOption('peer_model');
    $this->addRequiredOption('button_label');
    $this->addOption('indicator', image_tag('/dcFormExtraPlugin/images/ajax-loader.gif',array('class'=>'ajax-loader-image', 'alt_title'=>'loading')));
    $this->addOption('undo_image',image_tag('/dcFormExtraPlugin/images/undo.png', array('style'=>'border-style:none;')));
    $this->addOption('list_title');
    $this->addOption('choices');
  }

  public function getValues($value, $name)
  {
    $str = '';
    if($value)
    foreach($value as $id)
    {
        $str .= "<li id='item-".$id."'>". call_user_func(array($this->getOption('peer_model'), $this->getOption('peer_method')), $id);
        $str .= "<a href='#' onClick='deleteItem(".$id.")'>".$this->getOption('undo_image')."</a>";
        $str .= "<input id='".$this->generateId($name)."_".$id."' name='".$name."[]' type='hidden' value='".$id."'/> </li>";
    }
    return $str;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  { 
    $str_values = $this->getValues($value, $name);
    return parent::render($name, null, $attributes, $errors)
.sprintf(<<<EOF
<input id="add-button" type="button" value="%s" onClick="addItem()" style="margin-left:5px;"/>

<div id="ajax-loader" style="display:none; padding:5px;">
  %s
</div>

<div id="items-list">
  <h3 style="margin:0; padding:0;">%s</h3>
  <ul id="autocomplete-list">
    {$str_values}
  </ul>
</div>

<script type="text/javascript">

  function addItem()
  {
    var id = jQuery('#{$this->generateId($name)}').val();
    if((jQuery('#item-'+id).text()=='')&&(id!=''))
      jQuery.ajax({
        url:          '%s',
        data:         'id=' + id,
        cache:        false,
        dataType:     'json',
        beforeSend:   function() { jQuery("#ajax-loader").show(); jQuery("#add-button").hide(); },
        complete:     function() { jQuery("#ajax-loader").hide(); jQuery("#add-button").show(); },
        error:        function(xhr, status, error) { alert(xhr.status); },
        success:      function(data)
                      {
                        var item  = "<li id='item-" + id + "'>" + data[id];
                        item += "<a href='#' onClick='deleteItem("+ id +")'> %s </a>";
                        item += "<input id='{$this->generateId($name)}_" + id + "' name='{$name}[]' type='hidden' value='" + id + "'/> </li>";
                        jQuery('#autocomplete-list').append(item);
                      }
      });
  }

  function deleteItem(object_id)
  {
    jQuery.ajax({
        url:          '%s',
        data:         'id=' + object_id,
        beforeSend:   function() { jQuery('#ajax-loader').show(); },
        complete:     function() { jQuery('#ajax-loader').hide(); },
        success:      function(data) {
                        jQuery('#item-'+object_id).remove();
                      }
    });
  }
</script>
EOF
,   $this->getOption('button_label'),
    $this->getOption('indicator'),
    $this->getOption('list_title'),
    $this->getOption('save_method'),
    preg_replace('/\"/',"'",$this->getOption('undo_image')),
    $this->getOption('delete_method')
  );
}

}

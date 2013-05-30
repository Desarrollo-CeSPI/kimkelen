<?php 

/**
 * This widget renders two button next to any widget.
 *
 * The buttons are 'Add' and 'Refresh'. It's very useful when dealing with select tags.
 * It allows thew addition and refreshing of options.
 *
 * Options
 *  'widget': A widget to add buttons. REQUIRED
 *  'add_button_label': The label of the add button. Defaults to 'Add'.
 *  'refresh_button_label': The label of the refresh button. Defaults to 'Refresh'.
 *
 *  'window_internal_url': The 'module/action' to execute when the add button is pressed. REQUIRED
 *  'window_options': Options accepted by 'window.open()'. Defaults to "channelmode=1,width=700,height=500,location=0".
 *  'window_title': The title of the popup window. Defaults to 'Create'.
 *
 *  'peer_method': The peer method to fetch options when the refresh button is clicked. REQUIRED
 *  'peer_class': The peer class to fetch options when the refresh button is clicked. REQUIRED
 *  'peer_value_method': Defaults to "__toString"
 *  'peer_key_method': Defaults to "getPrimaryKey"
 *  'add_empty': defaults to false
 *  'peer_params': an array of parametes to pass to the peer_method. Call_user_func_array will be used so the signature of the peer_method must be func(array[0], array[1], ..., array[n]); Defaults to array().
 *
 *  'ajax_loader_css': The CSS of the Ajax loader.
 *  'ajax_loader_id': The id of the ajax loader. Defaults to "mtWidgetFormAdder_loader_WIDGET_ID"
 *  'ajax_loader_url': Url of the image of the ajax loader. Defaults to /dcFormExtraPlugin/images/ajax-loader.gif
 **/
class mtWidgetFormAdder extends sfWidgetForm
{
  public function configure($options = array(), $attributes = array())
  {
    //widget options
    $this->addRequiredOption('widget');
    $this->addOption('add_button_label',     'Add');
    $this->addOption('refresh_button_label', 'Refresh');
    //popup window options
    $this->addRequiredOption('window_internal_url');
    $this->addOption('window_options', "channelmode=1,width=700,height=500,location=0");
    $this->addOption('window_title', 'Create');
    //remote function options
    $this->addRequiredOption('peer_method');
    $this->addRequiredOption('peer_class');
    $this->addOption('peer_value_method', '__toString');
    $this->addOption('peer_key_method', 'getPrimaryKey');
    $this->addOption('add_empty', false);
    $this->addOption('peer_params', array());
    //ajax loader
    $this->addOption('ajax_loader_css', 'display: none; width: 20px; margin-left: 5px; margin-right: 5px; float: right;');
    $this->addOption('ajax_loader_id');
    $this->addOption('ajax_loader_url',  '/dcFormExtraPlugin/images/ajax-loader.gif');
    parent::configure($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'Tag', 'Asset'));

    $attrs  = array_merge($this->attributes, $attributes);
    $loader_html = image_tag($this->getOption('ajax_loader_url'), 
                             array('alt' => "ajax_loader",
                                   'style' => $this->getOption('ajax_loader_css'),
                                   'id'  => $this->generateAjaxLoaderId($name, $value)));

    return str_replace(array("%widget%", "%add_button%", "%refresh_button%", "%ajax_loader%"),
      array(
        $this->getOption('widget')->render($name, $value, $attrs, $errors),
        $this->renderAddButton($name, $value),
        $this->renderRefreshButton($name, $value),
        $loader_html
      ),
      $this->getDecoratorFormat());
  }

  protected function renderAddButton($name, $value)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'Tag', 'Asset'));

    $addButtonId    = $this->generateAddButtonId($name, $value);
    $url     = url_for($this->getOption('window_internal_url'));
    $options = $this->getOption('window_options');
    return link_to_function(
      $this->getOption('add_button_label'),
      "newWindow = window.open('$url', '_blank', '$options');
      Event.observe(newWindow, 'unload', function() {".$this->getAjaxRemoteFunction($name, $value).";});
      return true;",
      array('class' => 'mtWidgetFormAdder_button_add', 'id' => $this->generateAddButtonId($name, $value))
    );
  }

  protected function renderRefreshButton($name, $value)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Javascript', 'Url', 'Tag', 'Asset'));

    return link_to_function(
      $this->getOption('refresh_button_label'),
      $this->getAjaxRemoteFunction($name, $value),
      array('class' => 'mtWidgetFormAdder_button_refresh', 'id' => $this->generateRefreshButtonId($name, $value))
    );
  }

  protected function getAjaxRemoteFunction($name, $value)
  {
    $refreshButtonId = $this->generateRefreshButtonId($name, $value);
    return remote_function(array(
                     'update' => $this->generateId($name, $value),
                     'script' => true,
                     'url'    => 'dcFormExtraPlugin/mtWidgetFormAdderRefresh',
                     'complete' => "$('".$this->generateAjaxLoaderId($name, $value)."').hide(); $('".$this->generateId($name, $value)."').enable(); var but = $('$refreshButtonId'); if (but.hasClassName('mtWidgetFormAdder_button_disabled')) but.removeClassName('mtWidgetFormAdder_button_disabled');",
                     'loading'  => "$('".$this->generateAjaxLoaderId($name, $value)."').show(); $('".$this->generateId($name, $value)."').disable(); var but = $('$refreshButtonId'); if (!but.hasClassName('mtWidgetFormAdder_button_disabled')) but.addClassName('mtWidgetFormAdder_button_disabled');",
                     'with'   => "'peer_class=".$this->getOption('peer_class').
                                 "&peer_method=".$this->getOption('peer_method').
                                 "&value_method=".$this->getOption('peer_value_method').
                                 "&key_method=".$this->getOption('peer_key_method').
                                 "&".dcFormExtraArrayToolkit::arrayToArrayedString('peer_params', $this->getOption('peer_params')).
                                 "&add_empty=".($this->getOption('add_empty')? 1 : 0).
                                 "&selected=' + $('".$this->generateId($name, $value)."').getValue()",
                                 ));
  }

  protected function getDecoratorFormat()
  {
    return '<div class="mtWidgetFormAdder_wrapper">%widget%<span class="mtWidgetFormAdder_buttons">%add_button%%refresh_button% %ajax_loader%</span></div>';
  }

  protected function getSelectedString($value)
  {
    if (is_array($value))
    {
      return dcFormExtraArrayToolkit::arrayToArrayedString('selected', $value);
    }
    return "selected=$value";
  }

  protected function generateAjaxLoaderId($name, $value)
  {
    return $this->getOption('ajax_loader_id')? $this->getOption('ajax_loader_id') : 'mtWidgetFormAdder_loader_'.$this->generateId($name, $value);
  }

  protected function generateRefreshButtonId($name, $value)
  {
    return 'mtWidgetFormAdder_button_refresh_'.$this->generateId($name, $value);
  }

  protected function generateAddButtonId($name, $value)
  {
    return 'mtWidgetFormAdder_button_add_'.$this->generateId($name, $value);
  }
}

?>

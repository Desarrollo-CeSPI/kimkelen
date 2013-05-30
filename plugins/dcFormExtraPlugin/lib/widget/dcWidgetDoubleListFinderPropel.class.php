<?php

/*
 * This widget render a list of options and a double-list and the idea is to select a option and update de left-side values of the double-list
 */


class dcWidgetFormSelectDoubleListFinderPropel extends sfWidgetFormSelectDoubleList
{
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }


  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:       The model class (required)
   *                 If the option is not a Boolean, the value will be used as the text value
   *  * column:      The column wich is serached
   *  * method:      The method to use to display object values (__toString by default)
   *  * key_method:  The method to use to display the object keys (getPrimaryKey by default) 
   *  * order_by:    An array composed of two fields:
   *                   * The column to order by the results (must be in the PhpName format)
   *                   * asc or desc
   *  * criteria:    A criteria to use when retrieving objects
   *  * connection:  The Propel connection to use (null by default)
   *  * peer_method: The peer method to use to fetch objects
   *
   */

  protected function configure ($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'I18N'));
    parent::configure($options, $attributes );
    $this->addRequiredOption('column');
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('custom_handler',false);
    $this->addOption('loader', 'Please wait...');

  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $size = isset($attributes['size']) ? $attributes['size'] : (isset($this->attributes['size']) ? $this->attributes['size'] : 10);
    $letters = array('A', 'B', 'C','D', 'E', 'F', 'G', 'H' , 'I' ,'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q','R','S','T','U','V', 'W','X','Y','Z');
    $text = '<div class="dcFinder">';
    $id = $this->generateId($name);
    $id_left = $id . '_left';
    $unassociated_name = 'unassociated_'.$id;
    $double_list = new sfWidgetFormSelectDoubleList(array('choices' => $this->getOption('choices')));
    $double_list->setOptions($this->getOptions());
    $custom_handler = $this->getOption('custom_handler');
    $widget_serialized = base64_encode(serialize($double_list));
    $text.= '<ul class="finder">';
    foreach ($letters as $letter)
    {
      $text.= '<li>';
      $text.= link_to_function($letter , remote_function(array(
        'url' => '@dcWidgetFormSelectDoubleListFinderPropel', 
        'with' => 
          "'?letter=". $letter .
          "&size=" . $size .
          "&name=" . $unassociated_name .
          "&values=". base64_encode(serialize($value)) .
          ($custom_handler ? "&custom_handler=". base64_encode(serialize($custom_handler)):'') .
          "&widget=". $widget_serialized. "'" ,
        'before' => "$('{$id}_indicator').show(); dcFinder.setCurrent('$id', this);",
        'complete' => "$('{$id}_indicator').hide();",
        'update' => $id_left)));
      $text.= '</li>';
    }
    $text.= '<li class="current">';
    $text.= link_to_function(__('All'), remote_function(array(
      'url' => '@dcWidgetFormSelectDoubleListFinderPropel', 
      'with' => 
        "'?letter=".
        "&size=" . $size .
        "&values=". base64_encode(serialize($value)) .
          ($custom_handler ? "&custom_handler=". base64_encode(serialize($custom_handler)):'') .
        "&name=" . $unassociated_name .
        "&widget=". $widget_serialized. "'" ,
      'update' => $id_left,
      'before' => "$('{$id}_indicator').show(); dcFinder.setCurrent('$id', this);",
      'complete' => "$('{$id}_indicator').hide();"
    )));
    $text.= '</li>';

    $text .= '<li style="display: none;" id="'.$id.'_indicator">'.$this->getOption('loader').'</li>';

    $text.= '</ul>';

    $this->addOption('template', <<<EOF
  <div class="%class%">
    <div style="float: left">
      <div class="double_list_label">%label_unassociated%</div>
      <div id="$id_left">%unassociated%</div>
    </div>
    <div style="float: left; margin-top: 2em">
      %associate%
    <br />
      %unassociate%
  </div>
  <div style="float: left">
    <div class="double_list_label">%label_associated%</div>
      %associated%
    </div>
    <br style="clear: both" />
    <script type="text/javascript">
      sfDoubleList.init(document.getElementById('%id%'), '%class_select%');
    </script>
  </div> 
EOF
    );

    $text.= parent::render ($name, $value, $attributes, $errors);
    $text .= '</div>';
    return $text;
  }

  public function getJavascripts()
  {
    return array_merge(parent::getJavascripts(),array('/dcFormExtraPlugin/js/dcWidgetFormSelectDoubleListFinderPropel.js'));
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(),array('/dcFormExtraPlugin/css/dcWidgetFormSelectDoubleListFinderPropel.css' => 'all'));
  }
  
  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of values
   */
  public function getChoices()
  {
    $choices = array();

    $class = constant($this->getOption('model').'::PEER');

    $criteria = is_null($this->getOption('criteria')) ? new Criteria() : clone $this->getOption('criteria');
    if ($order = $this->getOption('order_by'))
    {
      $method = sprintf('add%sOrderByColumn', 0 === strpos(strtoupper($order[1]), 'ASC') ? 'Ascending' : 'Descending');
      $criteria->$method(call_user_func(array($class, 'translateFieldName'), $order[0], BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
    }
    $objects = call_user_func(array($class, $this->getOption('peer_method')), $criteria, $this->getOption('connection'));

    $methodKey = $this->getOption('key_method');
    if (!method_exists($this->getOption('model'), $methodKey))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodKey, __CLASS__));
    }

    $methodValue = $this->getOption('method');
    if (!method_exists($this->getOption('model'), $methodValue))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodValue, __CLASS__));
    }
    foreach ($objects as $object)
    {
      $choices[$object->$methodKey()] = $object->$methodValue();
    }

    return $choices;
  }


}

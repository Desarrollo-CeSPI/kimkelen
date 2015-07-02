<?php

/**
 * pmWidgetFormPropelJQuerySearch represents an HTML input tag with the search capability.
 *
 * @package    dcWidgetFormJQuerySearch
 * @subpackage widget
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmWidgetFormPropelJQuerySearch extends pmWidgetFormJQuerySearch
{
  /**
   * Configures the widget.
   *
   * Available options:
   *
   *  * model:       The model class (required)
   *  * column:      The column (or columns) for performing the search (required)
   *  * method:      The method to use to display object values (__toString by default)
   *  * key_method:  The method to use to display the object keys (getPrimaryKey by default) 
   *  * order_by:    An array composed of two fields:
   *                   * The column to order by the results (must be in the PhpName format)
   *                   * asc or desc
   *  * criteria:    A criteria to use when retrieving objects
   *  * connection:  The Propel connection to use (null by default)
   *  * peer_method: The peer method to use to fetch objects
   *
   * @see pmWidgetFormJQuerySearch
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('retrieve_object_method', 'retrieveByPk');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
    $this->addOption('peer_method', 'doSelect');
    
    $this->setOption("url", "@pm_widget_form_propel_jquery_search");
  }
  
  public function getValueString($value)
  {
    if(is_null($value)){
      return '';
    }
    $class = constant($this->getOption("model")."::PEER");      
    $object = call_user_func(array($class,  $this->getOption('retrieve_object_method')), $value);
    $method = $this->getOption("method");
    
    return !is_null($object) ? parent::getValueString($object->$method()) : "";
  }
}
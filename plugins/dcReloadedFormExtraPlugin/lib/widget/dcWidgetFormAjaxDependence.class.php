<?php
/**
 * dcWidgetFormAjaxDependence is a widget that depends on other widget
 * value. When this other widget updates its value, this widget will update 
 * it's rendernig.
 *
 * This widget needs some JavaScript to work. So, you need to include the JavaScripts
 * files returned by the getJavaScripts() method.
 *
 * If you use symfony 1.2, it can be done automatically for you.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Christian A. Rodriguez <car@cespi.unlp.edu.ar>
 * @version    SVN: $Id: $
 */


class dcWidgetFormAjaxDependence extends sfWidgetForm
{
  protected $myattributes;
  protected $myerrors;
  protected $myvalue;
  protected $myname;

  /* 
   * If this widget observes a widget that may be overwritten by ajax calls not covered by this widget, then
   * this widget provides a javascript function to be called after the ajax update is done. This js function is:
   *    dcWidgetAjaxDependence.fixMyObservers(id); 
   *
   * Available options:
   *
   *  * dependant_widget:             an instance of sfWidgetForm. An instance of this class 
   *                                  will render as this option
   *
   *  * observe_widget_id:            html id of widget to observe updates
   *
   *  * observe_widget_is_boolean:    if observed widget is checkbox for example, when not checked, null value will be returned
   *
   *  * observe_event:                Javascript event without the on prefix: (on)create, (on)change
   *
   *  * observe_callback:             wich ajax action to call when observe_event occurs on observe_widget_id
   *
   *  * indicator:                    what to show when ajax callback is being executed
   *
   *  * get_observed_value_callback:  php callback to call when observed_widget_id updates its value. This callback
   *                                  must receive  2 arguments: an instance of this widget, the observed value
   *
   *  * message_with_no_value:        what to show when this widget can not be rendered because observed_widget_id
   *                                  has no value yet
   *
   *  * after_ajax_js:                javascript code to be executed after renderization
   *
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','JavascriptBase','Url'));
    
    parent::configure($options, $attributes );
    $this->addRequiredOption('dependant_widget');
    $this->addRequiredOption('observe_widget_id');
    $this->addOption('observe_widget_is_boolean',false);
    $this->addOption('observe_callback',url_for('@dc_widget_form_ajax_dependence_changed'));
    $this->addOption('observe_event','change');
      $this->addOption('indicator',image_tag('/dcReloadedFormExtraPlugin/images/ajax-loader.gif',array('class'=>'ajax-loader-image', 'alt_title'=>'loading')));
    $this->addOption('get_observed_value_callback',null);
    $this->addoption('observed_value_callback_extra_param', null);
    $this->addOption('message_with_no_value','Please select a dependant value to update');
    $this->addOption('after_ajax_js', '');
  }

  /*
   * Required Javascripts for this widget 
   */
  public function getJavaScripts()
  {
    /* prototype is required... :(
     * TODO: reimplement form jQuery
     */
    return array_merge(parent::getJavaScripts(),array('/dcReloadedFormExtraPlugin/js/prototype.js', '/dcReloadedFormExtraPlugin/js/ajax_dependence.js'), $this->getDependantWidgetJavaScripts());
  }

  /**
   * Required Javascripts for dependant widget.
   *
   * @return array
   */
  public function getDependantWidgetJavaScripts()
  {
    $dependant_widget = $this->getOption('dependant_widget');
    if (is_callable(array($dependant_widget, 'getJavaScripts')))
    {
      return $dependant_widget->getJavaScripts();
    }
    
    return array();
  }

  /**
   * Required Stylesheets for dependant widget.
   *
   * @return array
   */
  public function getDependantWidgetStylesheets()
  {
    $dependant_widget = $this->getOption('dependant_widget');
    if (is_callable(array($dependant_widget, 'getStylesheets')))
    {
      return $dependant_widget->getStylesheets();
    }

    return array();
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), $this->getDependantWidgetStylesheets());
  }

  /*
   * Javascript function that registers an observer 
   *
   * @param String $update_element   
   *
   * @return String   javascript tag
   */
  public function getJavascript($update_element)
  {
    $my_id=$this->getId();
    $observe_id=$this->getOption('observe_widget_id');
    $event_type=$this->getOption('observe_event');
    $ajax_url=$this->getOption('observe_callback');
    $loading_image=$this->getOption('indicator');
    $widget=base64_encode(serialize(clone $this));
    return javascript_tag("dcWidgetFormAjaxDependence.add(
        '$my_id',
        '$observe_id',
        '$event_type',
        '$ajax_url',
        '$loading_image',
        '$widget',
        '$update_element');");
  }

  /*
   * Javascript function that tries to update observer handlers because it has been reloaded
   *
   * @param array $id        this widget id
   *
   * @return String   javascript tag
   */
  protected function updateDependencies()
  {
    $id=$this->getId();
    return javascript_tag("dcWidgetFormAjaxDependence.updateDependencies('$id');");
  }

  /**
   * Javascript that will be rendered after the widget is rendered
   */
  protected function getAfterAjaxJs()
  {
    return javascript_tag($this->getOption('after_ajax_js', ''));
  }

  /*
   * Representation of this widget after an ajax callback
   *
   * @return String   widget renderization
   */
  public function ajaxRender($observed_value=null)
  {
    
    if (empty($observed_value)&&!$this->getOption('observe_widget_is_boolean'))
    {
      return $this->getOption('message_with_no_value');
    }
    if (!is_null($this->getOption('get_observed_value_callback')))
    {
      call_user_func(
        $this->getOption('get_observed_value_callback'),
        $this->getOption('dependant_widget'),
        $this->getOption('observe_widget_is_boolean')?!empty($observed_value):$observed_value,
        $this->getOption('observed_value_callback_extra_param')
      );
    }
    return $this->getOption('dependant_widget')->render($this->myname,$this->myvalue, $this->myattributes, $this->myerrors).
    $this->updateDependencies().
    $this->getAfterAjaxJs();
    
  }

  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $this->myattributes=array_merge($this->getAttributes(), $attributes);
    $this->myerrors=$errors;
    $this->myvalue=$value;
    $this->myname=$name;
    $update_element="update_".$this->getId();
    return "<span id='$update_element'>".$this->getOption('message_with_no_value')."</span>".$this->getJavascript($update_element).$this->getAfterAjaxJs();
  }

  public function getId()
  {
    return $this->generateId($this->myname);
  }
}

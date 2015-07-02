<?php
/* 
 * dcWidgetFormJqueryDependence is a widget that depends on a set of widgets
 * values. When on of the other widget updates its value, this widget will update
 * it's rendernig.
 *
 * This widget needs some JavaScript to work. So, you need to include the JavaScripts
 * files returned by the getJavaScripts() method. By the name you will know that
 * jquery is a requirement.
 *
 * Available options:
 *
 *  * widget:           an instance of sfWidgetForm that will be rendered when
 *                      observed object changes
 *
 *  * observed_id:      html id or array of html ids of observed widgets
 *
 *  * on_change:        PHP callback to be executed at the end of the Ajax event
 *                      triggered (@see callback option)
 *
 *  * callback:         Ajax action to be called when JS event is trigggered.
 *                      Defaults to @dc_widget_form_jquery_dependence_changed
 *
 *  * event:            JS event that triggers callback Ajax action
 *
 *  * observed_boolean_ids: array of html ids of checkbox elements
 *
 *  * loading_image:    Image to show when ajax callback is being processed
 * 
 *  * no_value_text:    Text to be shown when this widget can not be rendered
 *                      because observed_widget_id has no value yet
 *
 *  * observed_can_be_empty_ids: Array of html ids of inputs that, when empty, the 'no_value_text' won't be shown.
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class dcWidgetFormJQueryDependence extends sfWidgetForm
{
  var 
          $myattributes,
          $myerrors,
          $myvalue,
          $myname;

  protected function configure($options = array(), $attributes = array()) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array(
        'Asset',
        'Tag',
        'JavascriptBase',
        'Url'));

    parent::configure($options, $attributes);

    $this->addRequiredOption('widget');
    $this->addRequiredOption('observed_id');
    $this->addRequiredOption('on_change');
    $this->addOption('callback',url_for('@dc_widget_form_jquery_dependence_changed'));
    $this->addOption('event','change');
    $this->addOption('observed_boolean_ids',array());
    $this->addOption('observed_can_be_empty_ids', array());
    $this->addOption('loading_image',image_tag('/dcReloadedFormExtraPlugin/images/ajax-loader.gif',array('class'=>'ajax-loader-image', 'alt_title'=>'loading')));
    $this->addOption('no_value_text','Please select a dependant value to update');
    $this->addOption('or_null', false);
    $this->addOption('check_all_values_before_render_widget', false);
  }

  /**
   * Renders a HTML content tag.
   *
   * @param string $name
   * @param <type> $value
   * @param array $attributes
   * @param array $errors
   */
  public function render($name, $value = null, $attributes = array(), $errors = array()) 
  {
    $this->myattributes=array_merge($this->getAttributes(), $attributes);
    $this->myerrors=$errors;
    $this->myvalue=$value;
    $this->myname=$name;
    return sprintf('<span id="%s">%s</span>%s',
            "dc_widget_form_jquery_dependence_update_".$this->generateId($name),
            $this->getOption('no_value_text'),
            $this->getJQueryAddDependency()
            );
  }

  /**
   * Renders a HTML content tag after Ajax callback is executed.
   *
   * @param array $dependant_values
   * @return string
   */
  public function renderAfterUpdate($dependant_values)
  {
    $render_widget=false;
    $first = true;
    foreach ($dependant_values as $key=>$value)
    {
      if (!in_array($key, $this->getOption('observed_boolean_ids'))
          && (!empty($value) || in_array($key, $this->getOption('observed_can_be_empty_ids'))))
      {
        $render_widget=($render_widget || $first) && true;
        $first = false;
        if (!$this->getOption('check_all_values_before_render_widget'))
        {
          break;
        }
      }
      else
      {
        $first = $render_widget = false;
      }
    }

    if ($render_widget || $this->getOption('or_null'))
    {
      call_user_func($this->getOption('on_change'),$this,$dependant_values);
    }

    return (
      ($render_widget || $this->getOption('or_null'))?
        $this->getOption('widget')->render($this->myname,$this->myvalue, $this->myattributes, $this->myerrors):
        $this->getOption('no_value_text')).
      $this->getJQueryUpdateDependencies();
  }

  /**
   * Returns a Json encode string for the received widget
   *
   * @param dcWidgetFormJqueryDependence $widget
   * @return string
   */
  static public function encodeWidget(dcWidgetFormJqueryDependence $widget)
  {
    $options = $widget->getOptions();
    return json_encode(
            array(
                'id'            =>  $widget->generateId($widget->myname),
                'myvalue'       =>  serialize($widget->myvalue),
                'myattributes'  =>  serialize($widget->myattributes),
                'myerrors'      =>  serialize($widget->myerrors),
                'myname'        =>  serialize($widget->myname),
                'options'       =>  $options,
                'serialized_options' => base64_encode(serialize($options)),
                'widget_class'  =>  get_class($widget),
                'update_id'     =>  "dc_widget_form_jquery_dependence_update_".$widget->generateId($widget->myname),
                )
    );
  }

   /**
   * Returns an instance of this class
   *
   * @param array $widget_array
   * @return dcWidgetFormJQueryDependence 
   */
  static public function decodeWidget($widget_array)
  {
    $options=unserialize(base64_decode($widget_array['serialized_options']));
    $attributes= unserialize($widget_array['myattributes']);
    $class= $widget_array['widget_class'];
    $instance = new $class($options, $attributes);
    $instance->myattributes=$attributes;
    $instance->myerrors=unserialize($widget_array['myerrors']);
    $instance->myvalue=unserialize($widget_array['myvalue']);
    $instance->myname=unserialize($widget_array['myname']);
    return $instance;
  }

  /**
   * Returns javascript code to add a new dependency
   *
   * @return string
   */
  protected function getJQueryAddDependency()
  {
    $widget=self::encodeWidget($this);
    return javascript_tag("dcWidgetFormJqueryDependence.addDependency($widget);");
  }

  /**
   * Returns javascript code to update dependencies after an Ajax callback is
   * executed
   *
   * @return string
   */
  public function getJQueryUpdateDependencies()
  {
    $widget=self::encodeWidget($this);
    return javascript_tag("dcWidgetFormJqueryDependence.updateDependenciesFor($widget);");
  }

  /**
   * Merge javascripts for this widget with those scripts needed by widget option
   *
   * @return array
   */
  public function getJavaScripts() {
    return array_merge(
            parent::getJavaScripts(),
            array('/dcReloadedFormExtraPlugin/js/dc_widget_form_jquery_dependence.js'),
            $this->getOption('widget')->getJavascripts());
  }

  /**
   * Merge stylesheets for this widget with those css needed by widget option
   *
   * @return array
   */
  public function getStylesheets() {
    return array_merge(
            parent::getStylesheets(),
            $this->getOption('widget')->getStylesheets());
  }
  
}

?>

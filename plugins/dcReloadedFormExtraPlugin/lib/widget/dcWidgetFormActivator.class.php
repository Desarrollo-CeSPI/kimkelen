<?php
/* 
 * dcWidgetFormActivator is a widget that depends on a set of widgets
 * values. When on of the other widget updates its value, this widget will update
 * it's rendernig.
 *
 * This widget needs some JavaScript to work. So, you need to include the JavaScripts
 * files returned by the getJavaScripts() method. By the name you will know that
 * jquery is a requirement.
 *
 * Available options:
 *
 *  * observed_id:      html id or array of html ids of observed widgets
 *
 *  * callback:         Ajax action to be called when JS event is trigggered.
 *                      Defaults to @dc_widget_form_activator
 *
 *  * event:            JS event that triggers callback Ajax action
 *
 *  * observed_boolean_ids: array of html ids of checkbox elements
 *
 *  * loading_image:    Image to show when ajax callback is being processed
 *
 *  * evaluate_method:  The method that returns true if the items should be enable or false otherwise.
 *                      By default a boolean evaluation is made with the given value.
 *
 *  * evaluate_method_extra_params: Extra params for the evaluate_method.
 *
 *  * render_after_method: By default this method renders the javascript that enables / disables the widget.
 *
 * @author Matías Torres <torresmat at gmail dot com> based on the dcWidgetFormJQueryDependence of Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class dcWidgetFormActivator extends sfWidgetForm
{
  var
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
    $this->addOption('evaluate_method', array('dcWidgetFormActivator', 'doDefaultEvaluation'));
    $this->addOption('evaluate_method_extra_params', array());
    $this->addOption('callback', url_for('@dc_widget_form_activator'));
    $this->addOption('event','change');
    $this->addOption('observed_boolean_ids', array());
    $this->addOption('render_after_method', array('dcWidgetFormActivator', 'renderAfterUpdate'));
    $this->addOption('loading_image',image_tag('/dcReloadedFormExtraPlugin/images/ajax-loader.gif',array('class'=>'ajax-loader-image', 'alt_title'=>'loading')));
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
    $this->myname = $name;

    return
        $this->getOption('widget')->render($name,$value, $attributes, $errors).
        sprintf('<span id="%s"></span>%s',
                 "dc_widget_form_activator_update_".$this->generateId($name),
                 $this->getJQueryAddDependency()
               );
  }

  /**
   * Renders a HTML content tag after Ajax callback is executed.
   *
   * @param array $dependant_values
   * @return string
   */
  static public function renderAfterUpdate($dependant_values, $options)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag', 'JavascriptBase'));
    if (call_user_func($options['evaluate_method'], self::filterValues($dependant_values, $options['observed_boolean_ids']), isset($options['evaluate_method_extra_params'])? $options['evaluate_method_extra_params'] : null))
    {
      return javascript_tag("jQuery('#".$options['id']."').removeAttr('disabled').change();");
    }
    else
    {
      return javascript_tag("jQuery('#".$options['id']."').children('option:selected').removeAttr('selected').removeAttr('value').change(); jQuery('#".$options['id']."').attr('disabled', 'disabled')");
    }
  }

  static public function filterValues($values, $boolean_ids)
  {
    $filteredValues = array();
    $boolean_ids    = empty($boolean_ids)? array() : $boolean_ids;
    $values         = is_null($values)? array() : $values;

    foreach ($values as $key => $value)
    {
      $filteredValues[$key] = in_array($key, $boolean_ids)? $value == "true" : $value;
    }

    return $filteredValues;
  }

  static public function doDefaultInversedEvaluation($values, $extraParams = array())
  {
    return !self::doDefaultEvaluation($values, $extraParams);
  }

  static public function doDefaultEvaluation($values, $extraParams = array())
  {
    return (bool) array_pop($values);
  }

  /**
   * Returns a Json encode string for the received widget
   *
   * @param dcWidgetFormActivator $widget
   * @return string
   */
  static public function encodeWidget(dcWidgetFormActivator $widget)
  {
    return json_encode(
            array(
                'id'              =>  $widget->generateId($widget->myname),
                'observed_id'     =>  $widget->getOption('observed_id'),
                'observed_boolean_ids' =>  $widget->getOption('observed_boolean_ids'),
                'update_id'       =>  "dc_widget_form_activator_update_".$widget->generateId($widget->myname),
                'evaluate_method' =>  $widget->getOption('evaluate_method'),
                'evaluate_method_extra_params' => $widget->getOption('evaluate_method_extra_params'),
                'event'           => $widget->getOption('event'),
                'callback'        => $widget->getOption('callback'),
                'render_after_method' => $widget->getOption('render_after_method'),
                'loading_image'       => '<img src="'.image_path('/dcReloadedFormExtraPlugin/images/ajax-loader.gif').'" />',
            )
    );
  }

   /**
   * Returns an instance of this class
   *
   * @param array $widget_array
   * @return dcWidgetFormActivator 
   */
  static public function decodeWidget($widget_array)
  {
    $widget_array['observed_boolean_ids'] = isset($widget_array['observed_boolean_ids'])? $widget_array['observed_boolean_ids'] : array();

    return $widget_array;
  }

  /**
   * Returns javascript code to add a new dependency
   *
   * @return string
   */
  protected function getJQueryAddDependency()
  {
    $widget=self::encodeWidget($this);
    return javascript_tag("dcWidgetFormActivator.addDependency($widget);");
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
    return javascript_tag("dcWidgetFormActivator.updateDependenciesFor($widget);");
  }

  /**
   * Merge javascripts for this widget with those scripts needed by widget option
   *
   * @return array
   */
  public function getJavaScripts()
  {
    return array_merge(
            parent::getJavaScripts(),
            array('/dcReloadedFormExtraPlugin/js/dc_widget_form_activator.js'),
            $this->getOption('widget')->getJavascripts()
    );
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

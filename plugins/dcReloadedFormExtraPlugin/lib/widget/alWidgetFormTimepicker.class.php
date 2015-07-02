
<?php
/**
 * Input time widget based on JQuery UI Timepicker plugin.
 * Must be validated with alValidatorTimepicker.
 * jquery,jquery-ui and jquery-ui-timepicker are REQUIRED.
 *
 * @author Alvaro F. Lara <alvarofernandolara@gmail.com>
 *
 * The configuration option accepts an array containing this options:

    timeSeparator: ':',           // The character to use to separate hours and minutes. (default: ':')
    showLeadingZero: true,        // Define whether or not to show a leading zero for hours < 10. (default: true)
    showMinutesLeadingZero: true, // Define whether or not to show a leading zero for minutes < 10. (default: true)
    showPeriod: false,            // Define whether or not to show AM/PM with selected time. (default: false)
    showPeriodLabels: true,       // Define if the AM/PM labels on the left are displayed. (default: true)
    periodSeparator: ' ',         // The character to use to separate the time from the time period.
    altField: '#alternate_input', // Define an alternate input to parse selected time to
    defaultTime: '12:34',         // Used as default time when input field is empty or for inline timePicker
                                  // (set to 'now' for the current time, '' for no highlighted time, default value: now)
    zIndex: null,                 // Overwrite the default zIndex used by the time picker
    showOn: 'focus',              // Define when the timepicker is shown.
                                  // 'focus': when the input gets focus, 'button' when the button trigger element is clicked,
                                  // 'both': when the input gets focus and when the button is clicked.
    button: null,                 // jQuery selector that acts as button trigger. ex: '#trigger_button'
    // Localization
    hourText: 'Hour',             // Define the locale text for "Hours"
    minuteText: 'Minute',         // Define the locale text for "Minute"
    amPmText: ['AM', 'PM'],       // Define the locale text for periods
    // Position
    myPosition: 'left top',       // Corner of the dialog to position, used with the jQuery UI Position utility if present.
    atPosition: 'left bottom',    // Corner of the input to position
    // Events
    beforeShow: beforeShowCallback, // Callback function executed before the timepicker is rendered and displayed.
    onSelect: onSelectCallback,   // Define a callback function when an hour / minutes is selected.
    onClose: onCloseCallback,     // Define a callback function when the timepicker is closed.
    onHourShow: onHourShow,       // Define a callback to enable / disable certain hours. ex: function onHourShow(hour)
    onMinuteShow: onMinuteShow,   // Define a callback to enable / disable certain minutes. ex: function onMinuteShow(hour, minute)
    // custom hours and minutes
    hours: {
        starts: 0,                // First displayed hour
        ends: 23                  // Last displayed hour
    },
    minutes: {
        starts: 0,                // First displayed minute
        ends: 55,                 // Last displayed minute
        interval: 5               // Interval of displayed minutes
    },
    rows: 4,                      // Number of rows for the input tables, minimum 2, makes more sense if you use multiple of 2
    showHours: true,              // Define if the hours section is displayed or not. Set to false to get a minute only dialog
    showMinutes: true             // Define if the minutes section is displayed or not. Set to false to get an hour only dialog
 *
 * I also added this options for the widget:
 * enable_timerange Boolean It determins if the timepicker should behave like a range or a single value. Single value by default
 *
 * The return value is a string like MM:HH or on case its range, HH:MM-HH:MM
 *
 * For more detailed information, please check the author site: http://fgelinas.com/code/timepicker/
 */

class alWidgetFormTimepicker extends sfWidgetForm
{

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);

    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->addOption('config', array());
    $this->addOption('enable_timerange', false);
    $this->addOption('from_label', __('From:'));
    $this->addOption('to_label', __('To:'));

  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    //Widget configuration
    if(!$this->getOption('enable_timerange')){
      return $this->renderSingleWidget($name, $value, $attributes, $errors);
    } else {
      return $this->renderRangeWidget($name, $value, $attributes, $errors);
    }
  }

  protected function renderSingleWidget($name, $value = null, $attributes = array(), $errors = array())
  {    
    return $this->renderContentTag(
      'input',
      null,
      array_merge(array('name' => $name, 'value' => $value), $attributes
    )) . $this->renderJavascript($name,null,$this->getOption('config'));
  }

  protected function renderRangeWidget($name, $value = null, $attributes = array(), $errors = array())
  {
    //The expected time range is a string like 13:30-23:45 or array('13:30','23:45')
    if(empty($value))
      $value = "-";
    $value = explode('-',$value);

    $ui_widget_conf = $this->getOption('config');    

    $widget_configuration = array();
    $widget_configuration['name'] = 'fake_'.$name.'[]';
    $widget_configuration['config'] = $ui_widget_conf;
    $widget_configuration['config']['onHourShow']   = 'tpStartOnHourShowCallback';
    $widget_configuration['config']['onMinuteShow'] = 'tpStartOnMinuteShowCallback';
    $widget_configuration['config']['onClose'] = 'refreshValue';

    $widget_configuration['range_id'] = '1';

    $widget_to_configuration = array();
    $widget_to_configuration['name'] = 'fake_'.$name.'[]';
    $widget_to_configuration['config'] = $ui_widget_conf;
    $widget_to_configuration['config']['onHourShow']   = 'tpEndOnHourShowCallback';
    $widget_to_configuration['config']['onMinuteShow'] = 'tpEndOnMinuteShowCallback';
    $widget_to_configuration['config']['onClose'] = 'refreshValue';
    $widget_to_configuration['range_id'] ='2';


    $widget = $this->getOption('from_label').$this->renderContentTag('input',
                                       null,
                                       array_merge(array('name' => 'fake_'.$name.'[]', 'value' => $value[0]), 
                                       array_merge($attributes,array('id' => $this->generateId($widget_configuration['name'].$widget_configuration['range_id']))
    )));

    $widget .= $this->getOption('to_label').$this->renderContentTag('input',
                                       null,
                                       array_merge(array('name'=> 'fake_'.$name.'[]', 'value' => $value[1]), 
                                       array_merge($attributes,array('id' => $this->generateId($widget_to_configuration['name'].$widget_to_configuration['range_id']))
    )));

    $widget .= $this->renderContentTag('input',
                                       null,
                                       array_merge(array('name'=> $name, 'value' => implode('-',$value) , 'type' => 'hidden'), 
                                       array_merge($attributes,array('id' => $this->generateId($name))
    )));

    $widget .= $this->renderJavascript('fake_'.$name,$widget_configuration['range_id'],$widget_configuration['config']);
    $widget .= $this->renderJavascript('fake_'.$name,$widget_to_configuration['range_id'],$widget_to_configuration['config']);
    
    //This is a fix for the json_encode function. If you dont use this
    //functions are passed in configuration as string and cant be use
    //as callbacks.
    $widget = str_replace(array('"tpStartOnHourShowCallback"','"tpStartOnMinuteShowCallback"','"tpEndOnHourShowCallback"','"tpEndOnMinuteShowCallback"','"refreshValue"'),
                array('tpStartOnHourShowCallback','tpStartOnMinuteShowCallback','tpEndOnHourShowCallback','tpEndOnMinuteShowCallback','refreshValue'),
                $widget);

    $widget = $this->renderJavascriptRangeFuncitons($this->generateId($widget_configuration['name'].$widget_configuration['range_id']),$this->generateId($widget_to_configuration['name'].$widget_to_configuration['range_id']),$this->generateId($name)) . $widget;
    return $widget;
  }

  protected function renderJavascript($name,$range_id = null,$config = array()){
    return javascript_tag('jQuery("#'.$this->generateId($name.$range_id).'").timepicker('.json_encode($config).');');
  }

  protected function renderJavascriptRangeFuncitons($from_widget_id,$to_widget_id,$real_widget_id)
  {
    return javascript_tag("
    function refreshValue(){
          from = jQuery('#".$from_widget_id."').val();
          to = jQuery('#".$to_widget_id."').val();
          jQuery('#".$real_widget_id."').val(from + '-'+to);
    }

    function tpStartOnHourShowCallback(hour) {
      hour = 0;
       var tpEndHour = $('#".$to_widget_id."').timepicker('getHour');
    // Check if proposed hour is prior or equal to selected end time hour
    if (hour <= tpEndHour) { return true; }
    // if hour did not match, it can not be selected
    return false;
    }

    function tpStartOnMinuteShowCallback(hour, minute) {
      hour = 0;
      minute = 0;
      var tpEndHour = $('#".$to_widget_id."').timepicker('getHour');
      var tpEndMinute = $('#".$to_widget_id."').timepicker('getMinute');
      // Check if proposed hour is prior to selected end time hour
      if (hour < tpEndHour) { return true; }
      // Check if proposed hour is equal to selected end time hour and minutes is prior
      if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
      // if minute did not match, it can not be selected
      return false;
    }

    function tpEndOnHourShowCallback(hour) {
      var tpStartHour = $('#".$from_widget_id."').timepicker('getHour');
      // Check if proposed hour is after or equal to selected start time hour
      if (hour >= tpStartHour) { return true; }
      // if hour did not match, it can not be selected
      return false;
    }

    function tpEndOnMinuteShowCallback(hour, minute) {
      var tpStartHour = $('#".$from_widget_id."').timepicker('getHour');
      var tpStartMinute = $('#".$from_widget_id."').timepicker('getMinute');
      // Check if proposed hour is after selected start time hour
      if (hour > tpStartHour) { return true; }
      // Check if proposed hour is equal to selected start time hour and minutes is after
      if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
      // if minute did not match, it can not be selected
      return false;
    }");
  }

  /*
   * Required Javascripts for this widget
   * jQuery
   * jquery.ui.core
   * jquery.ui.position
   * jquery.ui.widget
   * jquery.ui.tabs
   * jquery.ui.timepicker.js"
   */
  public function getJavaScripts(){
    return array_merge(parent::getJavaScripts(),array("/dcReloadedFormExtraPlugin/js/alTimepicker/jquery.ui.timepicker.js"  ));
  }

  /*
   * Required Stylesheets for this widget
   * jquery.ui => "screen"
   */
  public function getStylesheets(){
    return array_merge(parent::getStylesheets(),array("/dcReloadedFormExtraPlugin/css/alTimepicker/jquery.ui.timepicker.css?v=0.2.5" => "screen","/dcReloadedFormExtraPlugin/css/alTimepicker/reset-tables.css" => "screen"));
  }


}

<?php
/**
 * Description of dcWidgetFormTimepicker
 *
 * @authors ivan y emilia
 */
class dcWidgetFormDateTimePicker extends sfWidgetFormDateTime
{

  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('time',array());
    $this->addOption('date',array());

    parent::__construct($options, $attributes);
  }


  /**
   * Returns the date widget.
   *
   * @param  array $attributes  An array of attributes
   *
   * @return sfWidgetForm A Widget representing the date
   */
  protected function getDateWidget($attributes = array())
  {
    return new mtWidgetFormInputDate(array_merge(array('use_own_help' => false),$this->getOptionsFor('date'), $this->getAttributesFor('date', $attributes)));

  }

  protected function getTimeWidget($attributes = array())
  {
    return new dcWidgetFormTimepicker(array_merge(array('config'=>$this->getOption('time')), $this->getOptionsFor('time')),$this->getAttributesFor('time', $attributes));
  }

  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array("/dcReloadedFormExtraPlugin/js/alTimepicker/jquery.ui.timepicker.js"));

  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array("/dcReloadedFormExtraPlugin/css/alTimepicker/jquery.ui.timepicker.css?v=0.2.5" => "screen", "/dcReloadedFormExtraPlugin/css/alTimepicker/reset-tables.css" => "screen"));

  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $date_value = $time_value = $value;
    
    if(is_array($value))
    {
      if(isset($value['date']))
      {
        $date_value = $value['date'];
      }
      if(isset($value['time']))
      {
        $time_value = $value['time'];
      }
    }
    $date = $this->getDateWidget($attributes)->render($name.'[date]', $date_value);

    if (!$this->getOption('with_time'))
    {
      return $date;
    }

    return strtr($this->getOption('format'), array(
      '%date%' => $date,
      '%time%' => $this->getTimeWidget($attributes)->render($name.'[time]', $time_value),
    ));
  }

}
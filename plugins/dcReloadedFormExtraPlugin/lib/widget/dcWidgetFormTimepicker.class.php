<?php

/**
 * Description of dcWidgetFormTimepicker
 *
 * @author ivan
 */
class dcWidgetFormTimepicker extends alWidgetFormTimepicker
{

  protected function renderSingleWidget($name, $value = null, $attributes = array(), $errors = array())
  {
    $array = explode(' ', $value);
    return parent::renderSingleWidget($name, end($array), $attributes, $errors);

  }

}

?>

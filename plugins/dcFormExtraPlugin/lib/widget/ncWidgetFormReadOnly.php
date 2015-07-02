<?php

/**
 * ncWidgetFormReadOnly represents a read_only field with a hidden input holding
 * its actual value.
 *
 * Options:
 *   * empty_value: A string to show when the value of the field is null.
 *   * value_callback: A valid Callback that should be used in order to obtain a string value for the field.
 *   * plain: A boolean indicating whether this widget should be plain text or include a hidden input.
 *
 * @author ncuesta
 */
class ncWidgetFormReadOnly extends sfWidgetForm
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('empty_value', '&nbsp;');
    $this->addOption('value_callback', null);
    $this->addOption('plain', true);

    parent::__construct($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $html = '';

    if (!$this->getOption('plain'))
    {
      $input_hidden = new sfWidgetFormInputHidden();
      $html .= $input_hidden->render($name, $value);
    }

    if (!is_null($this->getOption('value_callback')))
    {
      $html .= call_user_func($this->getOption('value_callback'), $value);
    }
    else
    {
      $html .= $value;
    }

    return $html;
  }
}

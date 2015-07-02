<?php

/**
 * mtWidgetFormPlainText represents text. It fetches this text using the specified object and method.
 *
 * Options:
 *   * empty_value: A string to show when the value of the field is null.
 *   * value_callback: A valid Callback that should be used in order to obtain a string value for the field.
 *   * add_hidden_input: if this is to true, a hidden input will be added.
 *   * use_retrieved_value: if this is set to false, the hidden input's value will be set to the render's $value parameter.
 *
 * @author mtorres & ideas stolen from ncuesta
 */
class mtWidgetFormPlain extends sfWidgetForm
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('object');
    $this->addOption('method', '__toString');
    $this->addOption('method_args', null);
    $this->addOption('use_retrieved_value', true);
    $this->addOption('empty_value', '&nbsp;');
    $this->addOption('add_hidden_input', false);
    $this->addOption('value_callback', null);

    parent::__construct($options, $attributes);
  }

  protected function retrieveValue($value)
  {
    if (null !== $this->getOption('object'))
    {
      $callback = array($this->getOption('object'), $this->getOption('method'));
      $args     = $this->getOption('method_args');

      if (null === $args)
      {
        $value = call_user_func($callback);
      }
      else
      {
        $value = call_user_func_array($callback, is_array($args) ? $args : array($args));
      }
    }

    return $value;
  }

  protected function retrieveStringValue($value)
  {
    if (null !== $this->getOption('value_callback'))
    {
      try
      {
        $string_value = call_user_func($this->getOption('value_callback'), $value);
      }
      catch (Exception $e)
      {
        $string_value = $value;
      }
    }
    else
    {
      $string_value = $value;
    }

    return $string_value;
  }

  protected function renderHiddenField($name, $value, $attributes = array())
  {
    $input_hidden = new sfWidgetFormInputHidden(array(), $attributes);

    return $input_hidden->render($name, $value);
  }

  protected function renderDescription($name, $value, $content)
  {
    return $this->renderContentTag('span', $content, array('id' => $this->generateId($name, $value).'_description'));
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $original_value = $value;
    $value          = $this->retrieveValue($value);
    $string_value   = $this->retrieveStringValue($value);

    $html = '';

    if ($this->getOption('add_hidden_input'))
    {
      $hidden_value = $this->getOption('use_retrieved_value') ? $value : $original_value;

      $html .= $this->renderHiddenField($name, $hidden_value, $attributes);
    }

    $description = null === $value || strlen($value) == 0 ? __($this->getOption('empty_value')) : $string_value;

    $html .= $this->renderDescription($name, $value, $description);

    return $html;
  }

}
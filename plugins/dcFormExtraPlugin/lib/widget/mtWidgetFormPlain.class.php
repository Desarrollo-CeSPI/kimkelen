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

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    $original_value = $value;
    if (!is_null($this->getOption('object')))
    {
      $args=$this->getOption('method_args');
      if (is_null($args))
      {
        $value  = call_user_func(array($this->getOption('object'), $this->getOption('method')));
      }
      else
      { $args=is_array($args)?$args:array($args);
        $value  = call_user_func_array(array($this->getOption('object'), $this->getOption('method')),$args);
      }
    }

    if (!is_null($this->getOption('value_callback')))
    {
      $string_value = call_user_func($this->getOption('value_callback'), $value);
    }
    else
    {
      $string_value = $value;
    }

    $html = '';
    if ($this->getOption('add_hidden_input'))
    {
      $val = $this->getOption('use_retrieved_value')? $value : $original_value;
      $input_hidden = new sfWidgetFormInputHidden(array(), $attributes);
      $html .= $input_hidden->render($name, $val);
    }

    $text = is_null($value) ? __($this->getOption('empty_value')) : $string_value;
    #$html .= '<span id="'.$this->generateId($name.'_description', $value).'">'.$text.'</span>';
    $html .= $this->renderContentTag('span', $text, array_merge($attributes,array('id'=>$this->generateId($name.'_description', $value))));

    return $html;
  }
}

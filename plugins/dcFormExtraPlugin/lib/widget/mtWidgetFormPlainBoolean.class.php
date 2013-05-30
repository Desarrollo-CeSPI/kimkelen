<?php

/**
 * @author mtorres & ideas stolen from ncuesta
 */
class mtWidgetFormPlainBoolean extends sfWidgetForm
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('object', null);
    $this->addOption('method', null);
    $this->addOption('method_args', null);
    $this->addOption('add_hidden_input', false);
    $this->addOption('true_string', 'Yes');
    $this->addOption('false_string', 'No');

    parent::__construct($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    
    $object = $this->getOption('object');
    $method = $this->getOption('method');
    $method_args = $this->getOption('method_args');

    if (!is_null($object) && !is_null($method))
    {
      $value = $object->$method($method_args);
    }

    $html = $value? __($this->getOption('true_string')) : __($this->getOption('false_string'));

    if ($this->getOption('add_hidden_input'))
    {
      $input_hidden = new sfWidgetFormInputHidden(array(), $attributes);
      $html .= $input_hidden->render($name, $value);
    }

    return $html;
  }
}

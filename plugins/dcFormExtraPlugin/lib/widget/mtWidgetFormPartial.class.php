<?php


/**
 * This widget echoes a partial
 *
 * Options:
 *  - module: the name of the module.
 *  - partial: the name of the partial.
 *  - form: the form.
 *  - parameters: array of parameters that will be passed to the partial.
 */
class mtWidgetFormPartial extends sfWidgetForm
{
  public function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('module');
    $this->addRequiredOption('partial');
    $this->addRequiredOption('form');
    $this->addOption('parameters', array());
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $default_parameters = array('form' => $this->getOption('form'), 'value' => $value, 'name' => $name);
    $parameters = array_merge($this->getOption('parameters'), $default_parameters);
    return get_partial($this->getOption('module').'/'.$this->getOption('partial'), $parameters);
  }
}

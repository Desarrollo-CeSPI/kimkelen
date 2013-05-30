<?php

class gmExporterFieldDecoratorDate extends gmExporterFieldDecorator
{
  public function configure($options)
  {
    $options = parent::configure($options);
    $options['dateFormat'] = isset($options['dateFormat'])? $options['dateFormat'] : null;
    return $options;
  }

  public function render($value)
  {
    $value = parent::render($value);
    if (!is_null($this->options['dateFormat']))
    {
      $value = date($this->options['dateFormat'], strtotime($value));
    }
    return $value;
  }
}

<?php

class gmExporterFieldDecoratorBoolean extends gmExporterFieldDecorator
{
  public function configure($options)
  {
    $options = parent::configure($options);

    $options['trueRepresentation']  = isset($options['trueRepresentation'])? $options['trueRepresentation'] : 'Yes';
    $options['falseRepresentation'] = isset($options['falseRepresentation'])? $options['falseRepresentation'] : 'No';

    return $options;
  }

  public function render($value)
  {
    $value = parent::render($value);

    return $value? $this->translate($this->options['trueRepresentation']) : $this->translate($this->options['falseRepresentation']);
  }
}

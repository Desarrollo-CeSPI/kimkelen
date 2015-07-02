<?php

class gmExporterFieldDecoratorText extends gmExporterFieldDecorator
{
  public function configure($options)
  {
    $options = parent::configure($options);
    $options['truncateTo'] = isset($options['truncateTo'])? $options['truncateTo'] : -1;
    $options['lowercase'] = isset($options['lowercase'])? $options['lowercase'] : false;
    $options['uppercase'] = isset($options['uppercase'])? $options['uppercase'] : false;
    $options['capitalize'] = isset($options['capitalize'])? $options['capitalize'] : false;
    return $options;
  }

  public function render($value)
  {
    $value = strval(parent::render($value));
    if ($this->options['truncateTo'] != -1 && strlen($value) > $this->options['truncateTo'])
    {
      $value = substr($value, 0, $this->options['truncateTo']-3);
      $value .= '...';
    }
    
    if ($this->options['capitalize'])
    {
      $value = ucfirst(strtolower($value));
    }

    if ($this->options['uppercase'])
    {
      $value = strtoupper($value);
    }

    if ($this->options['lowercase'])
    {
      $value = strtolower($value);
    }

    return $value;
  }
}

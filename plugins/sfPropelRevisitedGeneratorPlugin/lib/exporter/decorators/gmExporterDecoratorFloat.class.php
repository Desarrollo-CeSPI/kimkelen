<?php

class gmExporterFieldDecoratorFloat extends gmExporterFieldDecorator
{
  public function configure($options)
  {
    $options = parent::configure($options);
    $options['decimals'] = isset($options['decimals'])? $options['decimals'] : gmGeneratorConfiguration::getExportationNumberOfDecimals();
    return $options;
  }


  public function render($value)
  {
    $value = parent::render($value);

    if ($this->options['decimals'])
    {
      $value = gmGeneratorRounder::round($value, $this->options['decimals']);
    }
    return (float) parent::render($value);
  }
}

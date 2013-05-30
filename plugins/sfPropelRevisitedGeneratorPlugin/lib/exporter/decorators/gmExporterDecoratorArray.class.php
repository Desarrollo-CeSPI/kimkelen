<?php

class gmExporterFieldDecoratorArray extends gmExporterFieldDecorator
{
  public function configure($options)
  {
    $options = parent::configure($options);
    $options['enclosure'] = isset($options['enclosure'])? $options['enclosure'] : '';
    $options['separator'] = isset($options['separator'])? $options['separator'] : ', ';
    $options['itemDecorator'] = isset($options['itemDecorator'])? $options['itemDecorator'] : 'text';
    $options['itemDecoratorOptions'] = isset($options['itemDecoratorOptions'])? $options['itemDecoratorOptions'] : array();
    return $options;
  }

  public function render($value)
  {
    $value = parent::render($value);

    $string = empty($this->options['enclosure'])? '' : $this->options['enclosure'];

    $string .= implode($this->options['separator'], $this->decorateWhole($value));

    $string .= empty($this->options['enclosure'])? '' : $this->options['enclosure'];

    return $string;
  }

  protected function getItemDecorator()
  {
    $options = array_merge($this->options['itemDecoratorOptions'], array('decorator' => $this->options['itemDecorator']));

    $decorator = gmExporterFieldDecorator::getInstance($options);

    return $decorator;
  }

  protected function decorateWhole($value)
  {
    $dec = $this->getItemDecorator();
    $ret = array();
    foreach ($value as $v)
    {
      $ret[] = $dec->render($v);
    }
    return $ret;
  }
}

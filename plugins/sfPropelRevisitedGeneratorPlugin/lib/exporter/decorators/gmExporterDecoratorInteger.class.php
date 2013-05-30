<?php

class gmExporterFieldDecoratorInteger extends gmExporterFieldDecorator
{
  public function render($value)
  {
    $value = parent::render($value);
    return (int) parent::render($value);
  }
}

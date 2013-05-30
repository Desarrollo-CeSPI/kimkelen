<?php

class gmExporterFieldDecorator
{
  protected 
    $context = null,
    $options = array();

  static public function getInstance($options)
  {
    $klass = null;
    self::parseOptions($options);

    switch ($options['decorator'])
    {
      case 'text':
        $klass = 'gmExporterFieldDecoratorText';
        break;
      case 'integer':
        $klass = 'gmExporterFieldDecoratorInteger';
        break;
      case 'float':
        $klass = 'gmExporterFieldDecoratorFloat';
        break;
      case 'date':
        $klass = 'gmExporterFieldDecoratorDate';
        break;
      case 'array':
        $klass = 'gmExporterFieldDecoratorArray';
        break;
      case 'pass':
        $klass = 'gmExporterFieldDecoratorPass';
      case 'boolean':
        $klass = 'gmExporterFieldDecoratorBoolean';
        break;
      default:
        $klass = $options['decorator'];
    }
    return new $klass($options);
  }

  public function __construct($options, $context = null)
  {
    $this->options = $this->configure($options);
    $this->context = is_null($context)? sfContext::getInstance() : $context;
  }

  static public function parseOptions(&$options)
  {
    $options['decorator'] = isset($options['decorator'])? $options['decorator'] : 'text';
  }

  public function configure($options)
  {
    $options['method_parameter']  = isset($options['method_parameter'])? $options['method_parameter'] : null;
    $options['translate']         = isset($options['translate'])? $options['translate'] : true;
    $options['label']             = isset($options['label'])? $options['label'] : '';

    return $options;
  }

  public function getContext()
  {
    return $this->context;
  }

  public function getId()
  {
    return strtolower(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'),
                                  array('a', 'e', 'i', 'o', 'u', 'n'),
                                  sfInflector::underscore($this->options['label'])));
  }

  public function getLabel()
  {
    return $this->translate($this->options['label']);
  }

  public function render($value)
  {
    if (is_object($value) && isset($this->options['method']))
    {
      $method = $this->options['method'];
      if (is_null($this->options['method_parameter']))
      {
        return $value->$method();
      }
      return $value->$method($this->options['method_parameter']);
    }
    return $value;
  }

  protected function translate($text)
  {
    if ($this->options['translate'])
    {
      $this->getContext()->getConfiguration()->loadHelpers('I18N');
      return __($text);
    }
    return $text;
  }
}

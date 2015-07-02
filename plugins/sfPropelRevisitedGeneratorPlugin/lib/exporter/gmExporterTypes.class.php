<?php

class gmExporterTypes
{
  const EXPORT_TYPE_XLS = 'xls';
  const EXPORT_TYPE_CSV = 'csv';
  const EXPORTER_SUBCLASS_PREFFIX = 'gm';

  static protected
    $types = array(
      self::EXPORT_TYPE_XLS => 'Excel',
      self::EXPORT_TYPE_CSV => 'CSV',
    );

  static protected
    $classes = array(
      self::EXPORT_TYPE_XLS => 'ExporterXls',
      self::EXPORT_TYPE_CSV => 'ExporterCsv',
    );

  static public function getFileExtension($type)
  {
    $class = self::getClassForType($type);

    return call_user_func(array($class, 'getFileExtension'));
  }

  static public function getMimeType($type)
  {
    $class = self::getClassForType($type);

    return call_user_func(array($class, 'getMimeType'));
  }

  static public function getClassPreffix($preffix = null)
  {
    return is_null($preffix)? self::EXPORTER_SUBCLASS_PREFFIX : $preffix;
  }

  static public function getClassForType($type, $preffix = null)
  {
    if (isset(self::$classes[$type]))
    {
      return self::getClassPreffix($preffix).self::getClassSuffixForType($type);
    }
    return $type;
  }

  static public function getClassSuffixForType($type)
  {
    return isset(self::$classes[$type])? self::$classes[$type] : $type;
  }
  
  static public function getTypes()
  {
    return array_keys(self::$types);
  }

  static public function getChoices($addEmpty = false, $context = null)
  {
    $choices = $addEmpty? array(null => null) : array();

    foreach (self::$types as $id => $lbl)
    {
      $choices[$id] = self::translate($lbl, $context);
    }

    return $choices;
  }

  static public function translate($val, $context = null)
  {
    if (is_null($context))
    {
      return $val;
    }

    self::loadHelpers($context);

    return __($val);
  }

  static public function loadHelpers($context)
  {
    $context->getConfiguration()->loadHelpers('I18N');
  }
}

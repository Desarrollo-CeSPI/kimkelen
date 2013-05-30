<?php

class gmGeneratorConfiguration
{
  static public function getEmbeddedFormFormatterName()
  {
    return sfConfig::get('app_gm_generator_plugin_embedded_form_formatter_name', 'revisitedEmbedded');
  }

  static public function getFormFormatterName()
  {
    return sfConfig::get('app_gm_generator_plugin_form_formatter_name', 'revisited');
  }

  static public function getExportationCommonTitle()
  {
    $opts = self::getExportationConfiguration();
    return isset($opts['common_title'])? $opts['common_title'] : false; 
  }

  static public function getExportationAppendCommonDate()
  {
    $opts = self::getExportationConfiguration();
    return isset($opts['append_common_date'])? $opts['append_common_date'] : false;
  }

  static public function getExportationDateFormat()
  {
    $opts = self::getExportationConfiguration();
    return isset($opts['date_format'])? $opts['date_format'] : false;
  }

  static public function getExportationConfiguration()
  {
    return sfConfig::get('app_gm_generator_plugin_exportation', array());
  }

  static public function getNumberOfDecimals()
  {
    return sfConfig::get('app_gm_generator_plugin_decimals', false);
  }

  static public function getExportationNumberOfDecimals()
  {
    $opts = self::getExportationConfiguration();
    return isset($opts['decimals'])? $opts['decimals'] : false;
  }

}

?>

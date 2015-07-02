<?php

class ncChangeLogConfigHandler
{
  static public function getForeignValues()
  {
    return sfConfig::get("app_nc_change_log_behavior_get_foreign_values", false);
  }

  static public function shouldEscapeValues()
  {
    return sfConfig::get("app_nc_change_log_behavior_escape_values", false);
  }

  static public function getDateTimeFormat()
  {
    return sfConfig::get("app_nc_change_log_behavior_date_time_format", 'Y/m/d H:i:s');
  }

  static public function getDateFormat()
  {
    return sfConfig::get("app_nc_change_log_behavior_date_format", 'Y/m/d');
  }

  static public function getTimeFormat()
  {
    return sfConfig::get("app_nc_change_log_behavior_date_format", 'H:i:s');
  }

  static public function isI18NActive()
  {
    return sfConfig::get("app_nc_change_log_behavior_translation_use_i18n", false);
  }

  static public function getFormatterClass()
  {
    return sfConfig::get('app_nc_change_log_behavior_formatter_class', 'ncChangeLogEntryFormatter');
  }

  static public function getFormatter()
  {
    $formatterClass = self::getFormatterClass();
    return new $formatterClass();
  }

  static public function getUsernameMethod()
  {
    return sfConfig::get('app_nc_change_log_behavior_username_method', 'getUsername');
  }

  static public function getUsernameCli()
  {
    return sfConfig::get('app_nc_change_log_behavior_username_cli', 'cli');
  }

  static public function getIgnoreFields()
  {
    return sfConfig::get('app_nc_change_log_behavior_ignore_fields');
  }

  static public function getTranslationObjectMethod()
  {
    return sfConfig::get('app_nc_change_log_behavior_translation_object_method', 'getHumanName') ;
  }

  static public function getTranslationFieldMethod()
  {
    return sfConfig::get('app_nc_change_log_behavior_translation_field_method', 'translateField');
  }
}

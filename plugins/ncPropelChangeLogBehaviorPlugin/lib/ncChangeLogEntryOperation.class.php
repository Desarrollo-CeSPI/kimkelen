<?php

class ncChangeLogEntryOperation
{
  const
    NC_CHANGE_LOG_ENTRY_OPERATION_INSERTION = 1,
    NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE    = 2,
    NC_CHANGE_LOG_ENTRY_OPERATION_DELETION  = 3;

  protected static
    $_types = array(
      self::NC_CHANGE_LOG_ENTRY_OPERATION_INSERTION => 'Insertion',
      self::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE    => 'Update',
      self::NC_CHANGE_LOG_ENTRY_OPERATION_DELETION  => 'Deletion'
    );

  public static function getTypes()
  {
    return self::$_types;
  }

  public static function getStringFor($type_index)
  {
    if (array_key_exists($type_index, self::$_types))
    {
      return self::$_types[$type_index];
    }

    return null;
  }

  public static function getI18NStringFor($type_index)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    if ($string = self::getStringFor($type_index))
    {
      return __($string, null, 'nc_change_log_behavior');
    }

    return null;
  }
}

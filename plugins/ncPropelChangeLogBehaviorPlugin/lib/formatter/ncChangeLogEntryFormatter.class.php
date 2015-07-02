<?php
/**
 * class ncChangeLogEntryFormatter.
 *
 * @author ncuesta
 */
class ncChangeLogEntryFormatter
{
  protected
    /* Update formats: available placeholders: * %field_name% * %old_value% * %new_value% */
    $valueUpdateFormat   = "Value of field '%field_name%' changed from '%old_value%' to '%new_value%'.",
    $valueAdditionFormat = "Value of field '%field_name%' was set to '%new_value%'. It had no value set before.",
    $valueRemovalFormat  = "Value of field '%field_name%' was unset. It's previous value was '%old_value%'.",
    /* Insertion format: available placeholders: * %object_name% * %pk% */
    $insertionFormat     = "A new %object_name% has been created and it has been given the primary key '%pk%' at %date% by %username%.",
    /* Deletion format: available placeholders: * %object_name% * %pk% */
    $deletionFormat      = "The %object_name% with primary key '%pk%' has been deleted at %date% by %username%.";


  /**
   * Format string that should be call before the
   * renderization of an ncChangeLogEntry
   *
   * @returns String formatting text
   */
  public function formatStart()
  {
    return '';
  }

  /**
   * Format string that should be call after the
   * renderization of an ncChangeLogEntry
   *
   * @returns String formatting text
   */
  public function formatEnd()
  {
    return '';
  }

  /**
   * Format an insertion operation.
   *
   * @param ncChangeLogAdapter
   * @return String HTML representation of an Insertion
   */ 
  public function formatInsertion(ncChangeLogAdapter $adapter)
  {
    return str_replace(array('%object_name%', '%pk%', '%date%', '%username%'),
                       array($adapter->renderClassName(), $adapter->getPrimaryKey(), $adapter->renderCreatedAt(), $adapter->renderUsername()),
                       ncChangeLogConfigHandler::isI18NActive()
                          ? __($this->insertionFormat, null, 'nc_change_log_behavior')
                          : $this->insertionFormat);
  }

  /**
   * Format an update operation.
   *
   * @param ncChangeLogAdapter
   * @return String HTML representation of an Update
   */
  public function formatUpdate(ncChangeLogAdapter $adapter)
  {
    $html = '';
    foreach ($adapter as $change)
    {
      $html .= $change->render()."\r\n";
    }
    return $html;
  }

  /**
   * Format a deletion operation.
   *
   * @param ncChangeLogAdapter
   * @return String HTML representation of a deletion
   */
  public function formatDeletion(ncChangeLogAdapter $adapter)
  {
    return trim(str_replace(array('%object_name%', '%pk%', '%date%', '%username%'),
      array($adapter->renderClassName(), $adapter->getPrimaryKey(), $adapter->renderCreatedAt(), $adapter->renderUsername()),
      ncChangeLogConfigHandler::isI18NActive()? __($this->deletionFormat, null, 'nc_change_log_behavior') : $this->deletionFormat));
  }

  /**
   * Formats a 'change' in an update operation
   * Return the string format representation.
   *
   * @param Array $params
   * @return String
   */
  public function formatUpdateChange(ncChangeLogUpdateChange $change)
  {
    if (is_null($change->getOldValue()) || (strlen($change->getOldValue()) == 0))
    {
      $format = $this->valueAdditionFormat;
    }
    elseif (is_null($change->getNewValue()) || (strlen($change->getNewValue()) == 0))
    {
      $format = $this->valueRemovalFormat;
    }
    else
    {
      $format = $this->valueUpdateFormat;
    }

    return str_replace(array('%field_name%', '%old_value%', '%new_value%'),
                       array($change->renderFieldName(), $change->getOldValue(), $change->getNewValue()),
                       self::translate($format));
  }


  /**
   * Used to output the starting HTML code of a list of changes
   *
   * @returns String
   */
  public function formatListStart()
  {
    return '';
  }

  /**
   * Used to output the ending HTML code of a list of changes
   *
   * @returns String
   */
  public function formatListEnd()
  {
    return '';
  }

  /**
   * Outputs the html representation of a single operation
   *
   * @param ncChangeLogAdapter
   * @return String
   */
  protected function formatList($adapter)
  {
    $format = "%operation% at %date%";
    if (ncChangeLogConfigHandler::isI18NActive())
    {
      $format = self::translate($format);
    }
    return str_replace(
      array('%operation%', '%date%'),
      array($adapter->renderOperationType(), $adapter->renderCreatedAt()),
      $format
    );
  }

  /**
   * Outputs the html representation of a single insertion operation
   * in a listing
   *
   * @param ncChangeLogAdapterInsertion $adapter
   * @param String url of the link to the 'show' action
   */
  public function formatListInsertion($adapter, $url)
  {
    return $this->formatList($adapter);
  }

  /**
   * Outputs the html representation of a single update operation
   * in a listing
   *
   * @param ncChangeLogAdapterInsertion $adapter
   * @param String url of the link to the 'show' action
   */
  public function formatListUpdate($adapter, $url)
  {
    return $this->formatList($adapter);
  }

  /**
   * Outputs the html representation of a single deletion operation
   * in a listing
   *
   * @param ncChangeLogAdapterInsertion $adapter
   * @param String url of the link to the 'show' action
   */
  public function formatListDeletion($adapter, $url)
  {
    return $this->formatList($adapter);
  }

  /**
   * Translates text
   *
   * @param String
   * @param Array
   * @return String
   */
  public static function translate($string, $params= null)
  {
    if (ncChangeLogConfigHandler::isI18NActive())
    {
      if (sfContext::hasInstance())
      {
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        return __($string, $params, 'nc_change_log_behavior');
      }
    }
    return $string;
  }
}

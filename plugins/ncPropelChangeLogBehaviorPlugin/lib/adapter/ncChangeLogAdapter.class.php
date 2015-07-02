<?php

/**
 * This class is the base class for the adapters for each
 * type of operation.
 *
 * These classes allows to access each type operation in a uniform way.
 */
abstract class ncChangeLogAdapter implements ArrayAccess, Iterator, Countable
{
  protected
    $entry,
    $elements,
    $keys,
    $count;

  /**
   * Constructor
   *
   * @params ncChangeLogEntry The entry to adapt.
   */
  public function __construct($entry)
  {
    $this->entry    = $entry;
    $this->elements = $this->getChangeLog();
    $this->keys     = array_keys($this->elements? $this->elements : array());
  }




  /******************************
   * Iterator interface methods
   *****************************/


  public function rewind()
  {
    reset($this->keys);
    $this->count = count($this->keys);
  }

  public function current()
  {
    return $this->elements[current($this->keys)];
  }

  public function key()
  {
    return current($this->keys);
  }

  public function next()
  {
    next($this->keys);
    --$this->count;
  }

  public function valid()
  {
    return $this->count > 0;
  }



  /*******************************
   * Countable interface Methods
   *******************************/



  public function count()
  {
    return count($this->keys);
  }



  /*********************************
   * ArrayAccess interface Methods *
   ********************************/



  public function offsetExists($name)
  {
    return isset($this->elements[$name]);
  }

  public function offsetGet($name)
  {
    if (!$this->offsetExists($name))
    {
        throw new InvalidArgumentException(sprintf('Change "%s" does not exist.', $name));
    }

    return $this->elements[$name];
  }

  public function offsetSet($offset, $value)
  {
    throw new LogicException('Cannot update changes.');
  }

  public function offsetUnset($offset)
  {
    throw new LogicException('Cannot unset changes.');
  }



  /************************
   *      Own methods     *
   ***********************/

  /**
   * Retrieves the changes
   *
   * @return mixed The changes
   */
  public function getChangeLog()
  {
    return array();
  }


  /**
   * Retrieves the affected class name.
   *
   * @return String The affected class name
   */
  public function getClassName()
  {
    return $this->entry->getObjectClassName();
  }

  /**
   * Retrieves the affected table name.
   *
   * @return String The affected table name
   */
  public function getTableName()
  {
    return $this->entry->getObjectTableName();
  }

  /**
   * Retrieves the affected object's primary key.
   *
   * @return String The affected object's primary key.
   */
  public function getPrimaryKey()
  {
    return $this->entry->getObjectPrimaryKey();
  }

  /**
   * Retrieves the changes
   *
   * @return Array The affected changes
   */
  protected function getChanges()
  {
    return array();
  }


  public function getEntry()
  {
    return $this->entry;
  }


  public function getObject()
  {
    $this->entry->getObject();
  }

  /**************************
   *        Format!         *
   **************************/

  protected function getFormatter()
  {
    $formatterName = ncChangeLogConfigHandler::getFormatter();
    return new $formatterName();
  }

  /**
   * Retrieves the HTML representation of the class name.
   * It may transform the class name values (eg. translation)
   *
   * @return String HTML representation of the className.
   */
  abstract public function renderClassName();


  /**
   * Retrieves the formatted date of the ChangeLogEntry
   * It may transform the created at value (eg. translation)
   *
   * @return String HTML representation of the date
   */
  public function renderCreatedAt()
  {
    return $this->entry->getCreatedAt(ncChangeLogConfigHandler::getDateTimeFormat());
  }

  /**
   * Retrieves the formatted username of the ChangeLogEntry
   *
   * @return String HTML representation of the username
   */
  public function renderUsername()
  {
    return $this->entry->getUsername();
  }

  /**
   * Retrieves the formatted operation name of the ChangeLogEntry
   *
   * @return String HTML representation of operation name
   */

  public function renderOperationType()
  {
    return $this->ncChangeLogPluginTranslate(ncChangeLogEntryOperation::getStringFor($this->entry->getOperationType()));
  }

  /**
   * Retrieves the HTML representation of the changes
   *
   * @return String HTML representation of the changes.
   */
  abstract public function render();

  /**
   * Retrieves the HTML representation
   * to be shown in a ncChangeLogEntry listing
   */
  abstract public function renderList($url = null);

  /*************************
   *      Translation      *
   ************************/

  /**
   * Retrieves a translated string using the table's catalogue.
   *
   * @param String $tableName The table's name. (The catalogue name)
   * @param String $string The string to translate
   *
   * @return String
   */
  public function tableTranslate($tableName, $string)
  {
    if (ncChangeLogConfigHandler::isI18NActive())
    {
      if (sfContext::hasInstance())
      {
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        return __($string, null, 'tables/'.$tableName);
      }
    }
    return $string;
  }

  /**
   * Retrieves a translated string usign the table's catalogue but
   * using the className.
   *
   * @param String $className The className used to obtain the table's name. (The catalogue name)
   * @param String $string The string to translate
   *
   * @return String
   */
  public function classTranslate($className, $string)
  {
    $peerClass = constant($className.'::PEER');
    $tableName = constant($className.'::TABLE_NAME');
    return $this->tableTranslate($tableName, $string);
  }

  /**
   * Translates strings using this adapter's catalogue.
   *
   * @param $string String String to translate
   *
   * @return String
   */
  public function translate($string)
  {
    return $this->tableTranslate($this->getTableName(), $string);
  }

  /**
   * Uses the plugin catalogue to translate strings.
   *
   * @param String $string String to translate
   *
   * @return String
   */
  public function ncChangeLogPluginTranslate($string)
  {
    if (ncChangeLogConfigHandler::isI18NActive())
    {
      if (sfContext::hasInstance())
      {
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        return __($string, null, 'nc_change_log_behavior');
      }
    }
    return $string;
  }

}

?>

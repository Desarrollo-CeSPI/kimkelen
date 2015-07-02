<?php

class ncChangeLogUpdateChange
{
  public
    $fieldName,
    $oldValue,
    $adapter,
    $newValue;

  

  public function __construct($fieldName, $oldValue, $newValue, $updateAdapter)
  {
    $this->fieldName  = $fieldName;
    $this->oldValue   = $oldValue;
    $this->newValue   = $newValue;
    $this->adapter    = $updateAdapter;
  }

  /**
   * Returns a propel column type or null if cannot fetch it. 
   * Available column types are:

      const CHAR = "CHAR";
      const VARCHAR = "VARCHAR";
      const LONGVARCHAR = "LONGVARCHAR";
      const CLOB = "CLOB";
      const NUMERIC = "NUMERIC";
      const DECIMAL = "DECIMAL";
      const TINYINT = "TINYINT";
      const SMALLINT = "SMALLINT";
      const INTEGER = "INTEGER";
      const BIGINT = "BIGINT";
      const REAL = "REAL";
      const FLOAT = "FLOAT";
      const DOUBLE = "DOUBLE";
      const BINARY = "BINARY";
      const VARBINARY = "VARBINARY";
      const LONGVARBINARY = "LONGVARBINARY";
      const BLOB = "BLOB";
      const DATE = "DATE";
      const TIME = "TIME";
      const TIMESTAMP = "TIMESTAMP";

      const BU_DATE = "BU_DATE";
      const BU_TIMESTAMP = "BU_TIMESTAMP";

      const BOOLEAN = "BOOLEAN";

   *
   */
  public function getColumnType()
  {
    $peerClassName = $this->adapter->getEntry()->getObjectPeerClassName();

    if (!is_null($peerClassName) && class_exists($peerClassName))
    {
      $tableMap = call_user_func(array($peerClassName , 'getTableMap'));
      if (!is_null($tableMap) && ($tableMap !== false) && ($tableMap->containsColumn($this->getFieldName())))
      {
        $column = $tableMap->getColumn($this->getFieldName());

        return $column->getType();
      }
    }

    return null;
  }

  protected function getDispatcher()
  {
    if (sfContext::hasInstance())
      return sfContext::getInstance()->getEventDispatcher();
    return null;
  }

  protected function createEvent()
  {
    return new sfEvent(
      $this, 
      $this->adapter->getTableName().'.render_'.$this->getFieldName(),
      array('fieldName' => $this->getFieldName(), 'tableName' => $this->adapter->getTableName(), 'fieldType' => $this->getColumnType())
    );
  }

  protected function createGlobalEvent()
  {
    return new sfEvent(
      $this, 
      'ncChangeLog.render',
      array('fieldName' => $this->getFieldName(), 'tableName' => $this->adapter->getTableName(), 'fieldType' => $this->getColumnType())
    );
  }

  public function getForeignValue($value, $method = '__toString')
  {
    if (ncChangeLogConfigHandler::getForeignValues() && $this->isForeignKey())
    {
      $peerClassName = constant($this->adapter->getClassName().'::PEER');
      $tableMap = call_user_func(array($peerClassName , 'getTableMap'));
      $column = $tableMap->getColumn($this->getFieldName());
      $relatedTableName     = $column->getRelatedTableName();
      $relatedPeerClassName = ncClassFinder::getInstance()->findPeerClassName($relatedTableName);
      if (!is_null($relatedPeerClassName) && class_exists($relatedPeerClassName))
      {
        $object = call_user_func(array($relatedPeerClassName, 'retrieveByPk'), $value);
        if (!is_null($object) && method_exists($object, $method))
        {
          return $object->$method();
        }
      }
    }
    return $value;
  }

  public function isForeignKey()
  {
    $peerClassName = constant($this->adapter->getClassName().'::PEER');
    if (!is_null($peerClassName) && class_exists($peerClassName))
    {
      $tableMap = call_user_func(array($peerClassName , 'getTableMap'));
      if (!is_null($tableMap) && ($tableMap !== false) && ($tableMap->containsColumn($this->getFieldName())))
      {
        $column = $tableMap->getColumn($this->getFieldName());
        return is_null($column)? false : $column->isForeignKey();
      }
    }
    return false;
  }

  /**
   * Retrieves the name of the field that have changed
   *
   * @return String
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }

  protected function getValue($value, $emitSignal)
  {
    $res   = $value;
    $event = null;

    if ($emitSignal)
    {
      $globalEvent = $this->createGlobalEvent();
      $this->getDispatcher()->filter($globalEvent, $value);
      $res = $globalEvent->getReturnValue();

      $event = $this->createEvent();
      $this->getDispatcher()->filter($event, $res);
      $res = $event->getReturnValue();
    }

    if (is_null($event) || (!$event->isProcessed() && !empty($value) && $this->isForeignKey()))
    {
      $res = $this->getForeignValue($value);
    }

    return $res;
  }

  /**
   * Retrieves the old value of the field that changed.
   *
   * @return String
   */
  public function getOldValue($emitSignal = false)
  {
    return $this->getValue($this->oldValue, $emitSignal);
  }

  /**
   * Retrieves the new value of the field that changed.
   *
   * @return String
   */
  public function getNewValue($emitSignal = false)
  {
    return $this->getValue($this->newValue, $emitSignal);
  }

  /**
   * Retrieves the translated name of the field that changed.
   *
   * @return String
   */
  public function renderFieldName()
  {
    return $this->adapter->translate($this->getFieldName());
  }

  /**
   * Uses the formatter 'formatUpdateChange' method to render this change.
   *
   * @return String
   */
  public function render()
  {
    return ncChangeLogConfigHandler::getFormatter()->formatUpdateChange($this);
  }
}

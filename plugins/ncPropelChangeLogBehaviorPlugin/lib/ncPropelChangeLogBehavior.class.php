<?php

/**
 * ncPropelChangeLogBehavior class.
 * Behavior that keeps a change log for an object.
 *
 * @author      José Nahuel CUESTA LUENGO <ncuesta@cespi.unlp.edu.ar>
 * @package     ncPropelChangeLogBehaviorPlugin
 * @subpackage  lib
 * @version     $SVN Id: $
 */
class ncPropelChangeLogBehavior
{
  /**
   * Before an $object is saved, determine the changes that have been made to it (if it had already been saved),
   * generate an ncChangeLogEntry an queue it so it can be committed *after* the object has been saved to the database.
   *   * If running from cli (as in propel:data-load or propel:build-all-load tasks), there won't be any available user to
   * get its username, so a configurable default value will be used: 'app_nc_change_log_behavior_username_cli' (defaults to 'cli').
   *   * Otherwise (if not running from cli), use 'app_nc_change_log_behavior_username_attribute' configuration value to obtain
   *       sfUser's username attribute (Defaults to 'username').
   *
   * @param mixed $object
   * @param PropelPDO $con
   * @return Boolean or null
   */
  static protected $enabled = true;

  public static function preSave($object, $con)
  {
    if (!self::$enabled) return false;
    $entry = new ncChangeLogEntry();

    $entry->setClassName(get_class($object));
    $entry->setUsername(self::getUsername());

    if ($object->isNew())
    {
      $entry->setOperationType(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_INSERTION);

      try
      {
        $object->setCreatedAt(time());
        $entry->setCreatedAt($object->getCreatedAt(null));
      }
      catch (Exception $e)
      {
        $entry->setCreatedAt(time());
      }
    }
    else
    {
      $entry->setOperationType(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE);
      $entry->setObjectPk($object->getPrimaryKey());

      if (!self::_update_changes($object, $entry))
      {
        return false;
      }
    }

    ncChangeLogEntryQueue::getInstance()->push($entry);
  }

  /**
   * After an object has been saved, commit the changes to its changelog.
   *
   * @param mixed $object
   * @param PropelPDO $con
   */
  public static function postSave($object, $con)
  {
    if (!self::$enabled) return false;
    $entry = ncChangeLogEntryQueue::getInstance()->selectivePop(get_class($object), ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE, $object->getPrimaryKey());

    if (!$entry)
    {
      $entry = ncChangeLogEntryQueue::getInstance()->selectivePop(get_class($object), ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_INSERTION, null, method_exists($object, 'getCreatedAt')? $object->getCreatedAt(null) : null);
    }

    if ($entry)
    {
      if ($entry->isOperation(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_INSERTION))
      {
        $entry->setObjectPk($object->getPrimaryKey());

        $changes = array(
          'class' => get_class($object),
          'pk'    => $object->getPrimaryKey(),
          'raw'   => array()
        );

        $entry->setChangesDetail(base64_encode(serialize($changes)));
      }

      $entry->save();
    }
  }

  /**
   * After an object has been deleted, state this change in its ChangeLog.
   *   * Use 'app_nc_change_log_behavior_username_attribute' configuration value to obtain the performing action username.
   *       Defaults to 'username'.
   *
   * @param mixed $object
   * @param mixed $con
   */
  public static function postDelete($object, $con)
  {
    if (!self::$enabled) return false;
    $entry = new ncChangeLogEntry();

    $entry->setClassName(get_class($object));
    $entry->setUsername(self::getUsername());
    $entry->setOperationType(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_DELETION);
    $entry->setObjectPk($object->getPrimaryKey());

    $changes = array(
      'class' => get_class($object),
      'pk'    => $object->getPrimaryKey(),
      'raw'   => array()
    );

    $entry->setChangesDetail(base64_encode(serialize($changes)));
    $entry->save();
  }

  /**
   * Get $object's ChangeLog and return it as an array of ncChangeLogAdapters.
   * If no entry is found, answer an empty Array.
   *
   * @param mixed $object
   * @param Criteria $criteria
   * @param PropelPDO $con
   * @return Array of ncChangeLogEntry
   */
  public static function getChangeLog($object, $criteria = null, $con = null, $transformToAdapters = true)
  {
    return self::getChangeLogByPkClassName($object->getPrimaryKey(), get_class($object), $criteria, $con, $transformToAdapters);
  }

  public static function hasChangeLog($object, $con = null)
  {
    $criteria = new Criteria();

    $criteria->add(ncChangeLogEntryPeer::CLASS_NAME, get_class($object));
    $criteria->add(ncChangeLogEntryPeer::OBJECT_PK, $object->getPrimaryKey());

    return (ncChangeLogEntryPeer::doCount($criteria, true, $con) > 0);
  }

  public static function getChangeLogByPkClassName($primaryKey, $className, $criteria = null, $con = null, $transformToAdapters = true)
  {
    if ($criteria instanceof Criteria)
    {
      $criteria = clone $criteria;
    }
    else
    {
      $criteria = new Criteria();
    }
    $criteria->add(ncChangeLogEntryPeer::CLASS_NAME, $className);
    $criteria->add(ncChangeLogEntryPeer::OBJECT_PK, $primaryKey);

    $results = array();
    foreach (ncChangeLogEntryPeer::doSelect($criteria) as $obj)
    {
      $results[] = $transformToAdapters? $obj->getAdapter() : $obj;
    }
    return $results;
  }

  public static function getRelatedAdapters($tables)
  {
    $results  = array();

    foreach ($tables as $t => $objects)
    {
      foreach ($objects as $f => $object)
      {
        $results[$t][$f] = $object->getAdapter();
      }
    }

    return $results;
  }

  /**
   * Get $object's Related ChangeLog and return it as an array of ncChangeLogAdapters.
   * If no entry is found, answer an empty Array.
   *
   * This methods inspects the columns of the object's table and if one of them is a foreign key,
   * it returns the change log of the referenced object.
   *
   * @param mixed $object
   * @param date $from_date
   * @param transformToAdapters
   *
   * @return Array of ncChangeLogEntry
   */
  public static function get1NRelatedChangeLog($object, $from_date = null, $transformToAdapters = true)
  {
    $relatedChangeLog = array();

    if (!is_null($object))
    {
      $class      = get_class($object);
      $peer       = constant($class.'::PEER');
      $tableMap   = call_user_func(array($peer , 'getTableMap'));

      foreach ($tableMap->getColumns() as $c)
      {
        if ($c->isForeignKey())
        {
          $method           = 'get'.$c->getPhpName();
          $relatedTableName = $c->getRelatedTableName();
          $relatedColName   = $c->getRelatedColumnName();
          $relatedPeerClass = ncClassFinder::getInstance()->findPeerClassName($relatedTableName);
          $relatedClass     = ncClassFinder::getInstance()->findClassName($relatedTableName, $relatedPeerClass);

          $criteria = new Criteria();
          $criteria->add(ncChangeLogEntryPeer::CLASS_NAME, $relatedClass);
          $criteria->add(ncChangeLogEntryPeer::OBJECT_PK,  $object->$method());

          if (!is_null($from_date))
            $criteria->add(ncChangeLogEntryPeer::CREATED_AT, $from_date, Criteria::GREATER_THAN);

          $relatedChangeLog[$c->getName()] = ncChangeLogEntryPeer::doSelect($criteria);
        }
      }
    }

    return $transformToAdapters? self::getRelatedAdapters($relatedChangeLog) : $relatedChangeLog;
  }

 /**
  * This methods inspects the columns of the object's table and if one of them if a foreign key,
  * it returns the change log of the referenced object IF it points to the specified object (parameter).
  *
  * @param mixed $object
  * @param date $from_date
  * @param transformToAdapters
  *
  * @return Array of ncChangeLogEntry
  */
  public function getNNRelatedChangeLog($object, $from_date = null, $transformToAdapters = true)
  {
    $relatedChangeLog = array();
    $relatedObjects   = array();

    if (!is_null($object))
    {
      // Obtain object's information
      $object_class = get_class($object);
      $peer         = constant($object_class.'::PEER');

      // Get all tableMaps and make the queries to retrieve all object instances that reference the object!!!
      ncClassFinder::getInstance()->reloadClasses();

      foreach (ncClassFinder::getInstance()->getPeerClasses() as $class => $path)
      {
        if ($class != get_class($object) && class_exists($class) && method_exists($class, 'getTableMap'))
        {
          $criteria = new Criteria();
          $tableMap = call_user_func(array($class, 'getTableMap'));

          foreach ($tableMap->getColumns() as $c)
          {
            if ($c->isForeignKey())
            {
              $method           = 'get'.$c->getPhpName();
              $relatedTableName = $c->getRelatedTableName();
              $relatedColName   = $c->getRelatedColumnName();
              $relatedPeerClass = ncClassFinder::getInstance()->findPeerClassName($relatedTableName);
              $relatedClass     = ncClassFinder::getInstance()->findClassName($relatedTableName, $relatedPeerClass);

              // Traverse all collumns. If any has as its `relatedClass` the class of $object, make a
              // Criteria object to fetch every related object.
              if ($relatedClass == get_class($object))
              {
                $criterion = $criteria->getNewCriterion(constant($class.'::'.$c->getName()), $object->getPrimaryKey());
                $criteria->addOr($criterion);
              }
            }
          }

          if ($criteria->size() > 0)
          {
            $relatedObjects[$class] = call_user_func(array($class, 'doSelect'), $criteria);
          }
        }
      }

      // Get every object's change log
      foreach ($relatedObjects as $tableName => $objects)
      {
        foreach ($objects as $o)
        {
          $criteria = new Criteria();

          if (!is_null($from_date))
          {
            $criteria->add(ncChangeLogEntryPeer::CREATED_AT, $from_date, Criteria::GREATER_THAN);
          }

          if (sfMixer::getCallable('Base'.get_class($o).':getChangeLog') && count($changes = $o->getChangeLog($criteria)) > 0)
          {
            if (method_exists($o, '__toString'))
            {
              $relatedChangeLog[$tableName][strval($o)] = $changes;
            }
            else
            {
              $relatedChangeLog[$tableName][$o->getPrimaryKey()] = $changes;
            }
          }
        }
      }
    }
    return $relatedChangeLog;
  }


  /**
   * Answer the route to $object's change log module.
   *
   * @param mixed $object
   * @return String
   */
  public static function getChangeLogRoute($object)
  {
    return '@nc_change_log?class='.get_class($object).'&pk='.$object->getPrimaryKey();
  }

  /**
   * Inspect the changes made to $object since its last version (the one stored in the database).
   * Update $entry's changes_detail to reflect the changes made.
   *
   * @param mixed $object
   * @param ncChangeLogEntry $entry
   */
  protected static function _update_changes($object, ncChangeLogEntry $entry)
  {
    //hack: remove $object from it's Peer's instance pool before diff is computed
    call_user_func(array(get_class($object->getPeer()), 'removeInstanceFromPool'), $object);

    $new_values = $object->toArray(BasePeer::TYPE_FIELDNAME);

    if (is_array($object->getPrimaryKey()))
    {
      $stored_object = call_user_func_array(array(get_class($object->getPeer()), 'retrieveByPK'), $object->getPrimaryKey());
    }
    else
    {
      $stored_object = call_user_func(array(get_class($object->getPeer()), 'retrieveByPK'), $object->getPrimaryKey());
    }

    if (!$stored_object)
    {
      // Unable to retrieve object from database: do nothing
      return false;
    }

    $stored_values  = $stored_object->toArray(BasePeer::TYPE_FIELDNAME);
    $ignored_fields = self::getIgnoredFields(get_class($object));

    $dbMap = Propel::getDatabaseMap();
    $table = $dbMap->getTable(constant(get_class($object->getPeer()).'::TABLE_NAME'));

    $diff = array('class' => get_class($object), 'pk' => $object->getPrimaryKey(), 'changes' => array());

    foreach ($new_values as $key => $value)
    {
      if (in_array($key, $ignored_fields))
      {
        continue;
      }
      elseif ($value != $stored_values[$key])
      {
        $column = $table->getColumn(BasePeer::translateFieldname(get_class($object), $key, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME));
        list($value_method, $params) = self::extractValueMethod($column);

        $diff['changes'][$key] = array(
          'old'   => $stored_object->$value_method($params),
          'new'   => $object->$value_method($params),
          'field' => $key,
          'raw'    => array(
            'old'   => $stored_values[$key],
            'new'   => $value
          )
        );
      }
    }

    if (isset($diff['changes']) && empty($diff['changes']))
    {
      return false;
    }

    $entry->setChangesDetail(base64_encode(serialize($diff)));

    return true;
  }

  /**
   * Return an array of fields that should be ignored in the changelog.
   *   * Use 'app_nc_change_log_behavior_ignore_fields' configuration value.
   *       Defaults to:
   *          <code>
   *            array(
   *              'created_at',
   *              'created_by',
   *              'updated_at',
   *              'updated_by'
   *            );
   *          </code>
   *
   * @return Array
   */
  public static function getIgnoredFields($class)
  {
    $ignore_fields = ncChangeLogConfigHandler::getIgnoreFields();

    if (!is_null($ignore_fields)) {
      if (isset($ignore_fields[$class]))
        return $ignore_fields[$class];
      elseif (isset($ignore_fields['any_class']))
        return $ignore_fields['any_class'];
    }

    return array('created_at', 'created_by', 'updated_at', 'updated_by');
  }

  /**
   * Extract the value method and the required parameters for it, for given a ColumnMap's type.
   * Return an Array holding the value method as first value and its parameters as the second one.
   *
   * @param ColumnMap $column
   * @return Array
   */
  static public function extractValueMethod(ColumnMap $column)
  {
    $value_method = 'get'.$column->getPhpName();
    $params = null;
    if (in_array($column->getType(), array(PropelColumnTypes::BU_DATE, PropelColumnTypes::DATE)))
    {
      $params = ncChangeLogConfigHandler::getDateFormat();
    }
    elseif (in_array($column->getType(), array(PropelColumnTypes::BU_TIMESTAMP, PropelColumnTypes::TIMESTAMP)))
    {
      $params = ncChangeLogConfigHandler::getDateTimeFormat();
    }
    elseif ($column->getType() == PropelColumnTypes::TIME)
    {
      $params = ncChangeLogConfigHandler::getTimeFormat();
    }

    return array($value_method, $params);
  }

  static protected function getUsername()
  {
    if (sfContext::hasInstance())
    {
      $user   = sfContext::getInstance()->getUser();
      $method = ncChangeLogConfigHandler::getUsernameMethod();

      if (method_exists($user, $method))
      {
        return $user->$method();
      }
    }

    // Use a default username.
    return ncChangeLogConfigHandler::getUsernameCli();
  }


  static public function disable()
  {
    self::$enabled=false;
  }

  static public function enable()
  {

    self::$enabled=true;
  }
}

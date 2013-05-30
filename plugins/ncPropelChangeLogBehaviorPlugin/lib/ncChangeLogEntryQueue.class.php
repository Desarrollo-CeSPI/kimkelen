<?php

/**
 * ncChangeLogEntryQueue class.
 * Queue manager for ncChangeLogEntry objects.
 *
 * @author ncuesta
 */
class ncChangeLogEntryQueue
{
  protected static $_instance;
  protected $_queue = array();
    

  /**
   * Singleton pattern.
   *
   * @return ncChangeLogEntryQueue
   */
  public static function getInstance()
  {
    if (!self::$_instance instanceof self)
    {
      self::$_instance = new self;
    }

    return self::$_instance;
  }

  /**
   * Return this instance's queue.
   *
   * @return Array
   */
  public function getQueue()
  {
    return $this->_queue;
  }

  /**
   * Set this instance's queue to $queue.
   * Answer the new queue.
   *
   * @param Array $queue
   * @return Array
   */
  public function setQueue(Array $queue)
  {
    $this->_queue = $queue;

    return $this->_queue;
  }

  /**
   * Insert an entry at the end of this instance's queue.
   * 
   * @param  ncChangeLogEntry $entry The entry to insert.
   * @return int The number of elements in the queue after the insertion.
   */
  public function push(ncChangeLogEntry $entry)
  {
    if (!$this->alreadyHas($entry))
    {
      return array_push($this->_queue, $entry);
    }

    return count($this->_queue);
  }

  /**
   * Remove the first entry at this instance's queue and return it.
   * 
   * @return ncChangeLogEntry or null
   */
  public function pop()
  {
    if (!$this->isEmpty())
    {
      return array_shift($this->_queue);
    }

    return null;
  }

  /**
   * Search for the first entry in the queue that matches the following criteria:
   *   * class_name equals $class,
   *   * operation_type equals $operation,
   *   * if $pk isn't null: object_pk equals $pk,
   *   * if $created_at isn't null: created_at equals $created_at.
   * Note that the preceding criteria is defined by an AND operator, i.e. every
   * condition must be met for an entry to match.
   * If any match is found, pop it from the queue and return it.
   * If none is found, return null.
   *
   * @param String $class The name of the class.
   * @param integer $operation As defined in ncChangeLogEntryOperation class.
   * @param integer $pk The primary key.
   * @param integer $created_at The timestamp for created_at attribute.
   * @return ncChangeLogEntry or null.
   */
  public function selectivePop($class, $operation, $pk = null, $created_at = null)
  {
    $index = null;
    $i = 0;
    foreach ($this->_queue as $entry)
    {
      if (0 == strcmp($entry->getClassName(), $class) && $entry->getOperationType() == $operation)
      {
        if (!is_null($pk) && $entry->getObjectPK() == $pk)
        {
          if (!is_null($created_at) && $entry->getCreatedAt(null) == $created_at)
          {
            $hit = $entry;
            $index = $i;
            break;
          }
          elseif (is_null($created_at))
          {
            $hit = $entry;
            $index = $i;
            break;
          }
        }
        elseif (is_null($pk))
        {
          if (!is_null($created_at) && $entry->getCreatedAt(null) == $created_at)
          {
            $hit = $entry;
            $index = $i;
            break;
          }
          elseif (is_null($created_at))
          {
            $hit = $entry;
            $index = $i;
            break;
          }
        }
      }
      $i++;
    }

    if (!is_null($index))
    {
      $this->removeItemAt($index);
      return $hit;
    }

    return null;
  }

  /**
   * Remove the element at position $index in this instance's queue and return it.
   * If $index is not a valid index, return null.
   *
   * @param integer $index
   * @return ncChangeLogEntry or null
   */
  public function removeItemAt($index)
  {
    if ($index >= 0 && $index < count($this->_queue))
    {
      $return = $this->_queue[$index];
      array_splice($this->_queue, $index, 1);
      return $return;
    }

    return null;
  }

  /**
   * Answer whether this instance's queue is empty.
   * 
   * @return Boolean
   */
  public function isEmpty()
  {
    return empty($this->_queue);
  }

  /**
   * Answer whether this instance's queue has $entry queued.
   *
   * @param ncChangeLogEntry $entry
   * @return Boolean
   */
  public function alreadyHas(ncChangeLogEntry $entry)
  {
    return (array_search($entry, $this->_queue) !== false);
  }
}
<?php

class ncChangeLogEntryPeer extends BasencChangeLogEntryPeer
{
  protected static function convertToAdapter($entries)
  {
    if (is_array($entries))
    {
      $res = array();
      foreach ($entries as $k => $e)
      {
        $res[$k] = $e->getAdapter();
      }
      return $res;
    }
    else
    {
      return $entries->getAdapter();
    }
  }

  public static function getChangeLogOfObject($class, $pk = null, $from_date = null, $to_date = null)
  {
    $c = new Criteria();


    $c->add(ncChangeLogEntryPeer::CLASS_NAME, $class);
    if (!is_null($pk))
      $c->add(ncChangeLogEntryPeer::OBJECT_PK, $pk);
      
    $criterion1 = $c->getNewCriterion(ncChangeLogEntryPeer::CREATED_AT, $from_date, Criteria::GREATER_EQUAL);
    $criterion2 = $c->getNewCriterion(ncChangeLogEntryPeer::CREATED_AT, $to_date, Criteria::LESS_EQUAL);
    
    $c->addAnd($criterion1);
    $c->addAnd($criterion2);

    return self::convertToAdapter(ncChangeLogEntryPeer::doSelect($c));
  }
}

<?php

class PluginsfGuardLoginFailurePeer extends BasesfGuardLoginFailurePeer
{
  public static function doCountForUsernameInTimeWindow($username, $time_window)
  {
      $criteria = self::buildCriteriaForTimeWindow($time_window);
      $criteria->add(self::USERNAME, $username);
      return self::doCount($criteria);
  }

  public static function doCountForIpInTimeWindow($ip, $time_window)
  {
      $criteria = self::buildCriteriaForTimeWindow($time_window);
      $criteria->add(self::IP_ADDRESS, $ip);
      return self::doCount($criteria);
  }

  protected static function buildCriteriaForTimeWindow($time_window)
  {
      $to = time();
      $from = strtotime("-$time_window seconds");
      $criteria = new Criteria();
      $cri = $criteria->getNewCriterion(self::FAILED_AT,$from,Criteria::GREATER_EQUAL);
      $cri->addAnd($criteria->getNewCriterion(self::FAILED_AT,$to,Criteria::LESS_EQUAL));
      $criteria->add($cri);
      return $criteria;
  }

}

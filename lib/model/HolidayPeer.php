<?php

class HolidayPeer extends BaseHolidayPeer
{

  public static function isHoliday($date)
  {
    $c = new Criteria();
    $c->add(self::DAY, $date);

    return self::doCount($c);
  }
}

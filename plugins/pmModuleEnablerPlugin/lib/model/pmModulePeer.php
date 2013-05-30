<?php

class pmModulePeer extends BasepmModulePeer
{
  public static function retrieveByName($name)
  {
    $c = new Criteria();
    $c->add(self::NAME, $name);
    return self::doSelectOne($c);
  }
}

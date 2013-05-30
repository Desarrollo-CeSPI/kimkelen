<?php

class License extends BaseLicense
{  
  
  public function canBeActivated()
  {
    if ($this->getIsActive())
      return false;

    $c = new Criteria();
    $c->add(LicensePeer::PERSON_ID, $this->getPersonId());
    $c->add(LicensePeer::IS_ACTIVE, true);

    return LicensePeer::doCount($c) == 0;
  }

  public function canBeDeactivated()
  {
    return $this->getIsActive();
  }

  public function getMessageCantBeDeactivated()
  {
    return 'The license is not active.';
  }

  public function getMessageCantBeActivated()
  {
    if ($this->getIsActive())
      return 'The license is already activated.';

    return 'There is other license activated from the same person.';
  }
}

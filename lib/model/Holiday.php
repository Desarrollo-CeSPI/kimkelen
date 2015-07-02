<?php

class Holiday extends BaseHoliday
{
  public function __toString() {
    return $this->getDay('d/m/Y');
  }
}

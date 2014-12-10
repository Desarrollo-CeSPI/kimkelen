<?php

class Department extends BaseDepartment
{
  const LA_PLATA = 5406441;

  public function __toString(){
    return $this->getName();
  }
}
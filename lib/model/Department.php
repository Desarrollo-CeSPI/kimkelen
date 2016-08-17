<?php

class Department extends BaseDepartment
{
  public function __toString(){
    return $this->getName();
  }
}

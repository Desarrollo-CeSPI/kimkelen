<?php

class FamilyRelationship extends BaseFamilyRelationship
{
    public function __toString()
    {
      return $this->getName();
    }
}

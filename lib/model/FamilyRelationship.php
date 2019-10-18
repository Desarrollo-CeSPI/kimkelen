<?php

class FamilyRelationship extends BaseFamilyRelationship
{
    public function __toString()
    {
      return $this->getName();
    }
    
    public function canBeDeleted()
    {
        $c = new Criteria();
        $c->add(AuthorizedPersonPeer::FAMILY_RELATIONSHIP_ID, $this->getId());
        
        $result = AuthorizedPersonPeer::doCount($c);
        return $result == 0;
    }
}

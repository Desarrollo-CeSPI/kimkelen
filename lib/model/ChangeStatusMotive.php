<?php

class ChangeStatusMotive extends BaseChangeStatusMotive
{
	public function canBeDeleted(PropelPDO $con = null)
  {
    $criteria = new Criteria();
    $criteria->add(StudentCareerSchoolYearPeer::CHANGE_STATUS_MOTIVE_ID, $this->getId());
    
    return !(StudentCareerSchoolYearPeer::doCount($criteria));
  }

}

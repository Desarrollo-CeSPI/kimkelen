<?php

class Pathway extends BasePathway
{
    
    function getPathwayStudents($criteria = null, PropelPDO $con = null)
    {
        return parent::getPathwayStudentsJoinStudent(PathwayPeer::getCriteriaForPathwayStudents($criteria), $con, Criteria::INNER_JOIN);
    }
    
    function getPathwayStudentsForYear($year, $criteria = null, PropelPDO $con = null)
    {
        $criteria = PathwayPeer::getCriteriaForPathwayStudents($criteria);
        $criteria->addJoin( StudentPeer::ID, StudentCareerSchoolYearPeer::STUDENT_ID  );
        $criteria->add(StudentCareerSchoolYearPeer::YEAR, $year);
        $criteria->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $this->getSchoolYearId());
        
        return parent::getPathwayStudentsJoinStudent($criteria, $con, Criteria::INNER_JOIN);
    }
    
    function __toString()
    {
        return $this->getName();
    }

	  function canBeDeleted() {
		  return ($this->countPathwayStudents() == 0);
	  }
}

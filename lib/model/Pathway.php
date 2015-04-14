<?php

class Pathway extends BasePathway
{
    function getPathwayStudents($criteria = null, \PropelPDO $con = null)
    {
        if ($criteria === null)
        {
            $criteria = new Criteria();
        }
        $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        //$criteria->addAscendingOrderByColumn(PathwayStudentPeer::YEAR);
        $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        $criteria->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);
        
        return parent::getPathwayStudentsJoinStudent($criteria, $con, Criteria::INNER_JOIN);
    }
}

<?php

class PathwayPeer extends BasePathwayPeer
{

    public static function retrieveCurrent()
    {
        $c = new Criteria();
        $c->add(PathwayPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId(), Criteria::EQUAL);

        return PathwayPeer::doSelectOne($c);
    }

    public static function getCriteriaForPathwayStudents(Criteria $criteria = null)
    {
        if ($criteria === null)
        {
            $criteria = new Criteria();
        }
        $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        //$criteria->addAscendingOrderByColumn(PathwayStudentPeer::YEAR);
        $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        $criteria->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);
        
        return $criteria;
    }

}

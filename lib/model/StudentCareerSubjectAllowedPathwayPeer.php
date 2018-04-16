<?php

class StudentCareerSubjectAllowedPathwayPeer extends BaseStudentCareerSubjectAllowedPathwayPeer
{
    static public function retrieveCriteriaByStudentAndCareerSubject($student, $career_subject)
    {
        $c = new Criteria();
        $c->add(self::STUDENT_ID, $student->getId());
        $c->add(self::CAREER_SUBJECT_ID, $career_subject->getId());

        return $c;
    }
    
    static public function doCountStudentAndCareerSubject($student, $career_subject)
    {
            return self::doCount(self::retrieveCriteriaByStudentAndCareerSubject($student, $career_subject));
    }
}

<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

class StudentRepprovedCourseSubjectPeer extends BaseStudentRepprovedCourseSubjectPeer
{
  public function getAvailableForExaminationRepprovedSubject(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    return StudentRepprovedCourseSubjectPeer::doSelect(self::getAvailableForExaminationRepprovedSubjectCriteria($examination_repproved_subject));
  }

  static public function getAvailableForExaminationRepprovedSubjectCriteria(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    
    $c->add(CareerSubjectPeer::SUBJECT_ID,$examination_repproved_subject->getCareerSubject()->getSubjectId());
    $c->addAnd(CareerSubjectPeer::YEAR,$examination_repproved_subject->getCareerSubject()->getYear());

    return $c;
  }

    static public function getFreeGraduatedStudentsCriteria(ExaminationRepprovedSubject $examination_repproved_subject)
    {
        $c = new Criteria();
        $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
        $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
        $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
        $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, CourseSubjectStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
        $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::FREE, Criteria::EQUAL, Criteria::INNER_JOIN);
        $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
        $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $examination_repproved_subject->getCareerSubjectId());

        return $c;
    }

  static public function getAvailableStudentsForExaminationRepprovedSubject(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    if($examination_repproved_subject->getExaminationRepproved()->getExaminationType() == ExaminationRepprovedType::FREE_GRADUATED) {

        $c = self::getFreeGraduatedStudentsCriteria($examination_repproved_subject);

    }else{
        $c = self::getAvailableForExaminationRepprovedSubjectCriteria($examination_repproved_subject);
    }
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    
     /*Saco retirados*/
    $criteria = new Criteria();
    $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
    $criterion = $criteria->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::EQUAL);
    $criterion->addOr($criteria->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL));
    $criteria->add($criterion);
    $criteria->clearSelectColumns();
    $criteria->addSelectColumn(StudentPeer::ID);
    $stmt = StudentPeer::doSelectStmt($criteria);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $c->add(StudentPeer::ID, $ids, Criteria::NOT_IN);

    return StudentPeer::doSelect($c);
  }

  public static function retrieveByCareerSubjectIdAndStudentId($career_subject_id, $student_id)
  {
    $career_subject = CareerSubjectPeer::retrieveByPK($career_subject_id);
    $c = new Criteria();
    //Join con students
    $c->addJoin(self::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID,  $student_id);

    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    
    $c->add(CareerSubjectPeer::SUBJECT_ID,$career_subject->getSubjectId());
    $c->addAnd(CareerSubjectPeer::YEAR,$career_subject->getYear());
    
  

    return self::doSelectOne($c);
  }

  /**
   * Counts the repproved course subjects for the given student and career.
   *
   * @param Student $student
   * @param Career $career
   * @param PropelPDO $con
   * @return Criteria
   */
  public static function retrieveCriteriaRepprovedForStudentAndCareer(Student $student, Career $career, PropelPDO $con = null)
  {

    $c = new Criteria();
    $c->add(self::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);

    $c->addJoin(self::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());

    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::CAREER_ID, $career->getId());

    $excluded_subjects = SchoolBehaviourFactory::getEvaluatorInstance()->getExcludeRepprovedSubjects();
    $c->add(CareerSchoolYearPeer::ID, $excluded_subjects, Criteria::NOT_IN);

    return $c;
  }
  /**
   * Counts the repproved course subjects for the given student and career.
   *
   * @param Student $student
   * @param Career $career
   * @param PropelPDO $con
   * @return integer
   */
  public static function countRepprovedForStudentAndCareer(Student $student, Career $career, PropelPDO $con = null)
  {
    $criteria = self::retrieveCriteriaRepprovedForStudentAndCareer($student, $career, $con);
    return self::doCount($criteria);
  }

  public static function countRepprovedForStudentAndCareerAndYear(Student $student, Career $career, $year, $con = null)
  {
    $criteria = self::retrieveCriteriaRepprovedForStudentAndCareer($student, $career, $con);
    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $criteria->add(CareerSubjectPeer::YEAR, $year);

    return self::doCount($criteria);
  }

  public static function retrieveCourseSubjectStudent($course_subject_student)
  {
    $c =  new Criteria();
    $c->add(self::COURSE_SUBJECT_STUDENT_ID,$course_subject_student->getId());
    return self::doSelectOne($c);
  }

  public static function retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject, $criteria = null)
  {
    if(is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(self::STUDENT_APPROVED_CAREER_SUBJECT_ID, $studentApprovedCareerSubject->getId());

    return self::doSelectOne($criteria);
  }

  public static function retrieveByCourseSubjectStudent($course_subject_student, $criteria = null)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(self::COURSE_SUBJECT_STUDENT_ID, $course_subject_student->getId());

    return self::doSelectOne($criteria);
  }
  
  public static function retrieveByStudentAndCareer(Student $student, Career $career, PropelPDO $con = null)
  {
    $criteria = self::retrieveCriteriaRepprovedForStudentAndCareer($student, $career, $con);
    return self::doSelect($criteria);
  }

}

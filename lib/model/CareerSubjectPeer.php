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

class CareerSubjectPeer extends BaseCareerSubjectPeer
{
  const FIRST_YEAR = 1;


  /**
   * Returns a collection of objects that can potentially select receiving CareerSubject
   * object $cs as their correlatives.
   * $exclude_related dictates if consider current correlatives or not
   * The implementation is delegated to custom behaviour
   *
   * @param CareerSubject $cs object to analyze which CareerSubjects are subject of select us as correlative
   * @param bool $exclude_related
   * @param Criteria $c pre initialized criteria
   * @param PropelPDO $con
   * @return Criteria
   */
  public static function getAvailableCarrerSubjectsAsCorrelativesFor(CareerSubject $cs, $exclude_related = true, Criteria $c=null, PropelPDO $con=null)
   {
      $criteria = self::getAvailableCarrerSubjectsAsCorrelativesCriteriaFor($cs,$exclude_related,$c,$con);
      return self::doSelect($criteria);
   }

/**
 * Returns a Criteria object with conditions applied so it can be used to retrieve
 * every CareerSubjects objects (for example with CareerSubjectPeer::doSelect) that
 * can potentially select receiving CareerSubject object $cs as their correlatives.
 * $exclude_related dictates if consider current correlatives or not
 * The implementation is delegated to custom behaviour
 *
 * @param CareerSubject $cs object to analyze which CareerSubjects are subject of select us as correlative
 * @param bool $exclude_related
 * @param Criteria $c pre initialized criteria
 * @param PropelPDO $con
 * @return Criteria
 */
   public static function getAvailableCarrerSubjectsAsCorrelativesCriteriaFor(CareerSubject $cs, $exclude_related = true, Criteria $c=null, PropelPDO $con=null)
   {
     return SchoolBehaviourFactory::getInstance()->getAvailableCarrerSubjectsAsCorrelativesCriteriaFor($cs,$exclude_related, $c,$con);
   }

   /**
    * This method returns an array of CareerSubjects, for all the career_subjects that have students to a examination_repproved
    *
    * @param ExaminationRepproved $examination_repproved
    * @param PropelPDO $con
    *
    * @return Criteria $c
    */
   public static function retrieveForExaminationRepprovedCriteria (ExaminationRepproved $examination_repproved , PropelPDO $con = null)
   {
     //arreglo con las career_subject que ya estan en la mesa de previa.
     $c = new Criteria();
     $c->add(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, $examination_repproved->getId());
     $c->addJoin(ExaminationRepprovedSubjectPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
     $c->clearSelectColumns();
     $c->addSelectColumn(CareerSubjectPeer::ID);
     $stmt = self::doSelectStmt($c);
     $already_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

     $c = new Criteria();
     $c->add(PersonPeer::IS_ACTIVE, true);
     $c->addJoin(StudentPeer::PERSON_ID,  PersonPeer::ID);
     $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
     $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
     $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
     $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
     $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID,  CareerSubjectSchoolYearPeer::ID);
     $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, self::ID);
     $c->add(self::ID, $already_ids, Criteria::NOT_IN);
     $c->addGroupByColumn(self::ID);

     return $c;
   }

    public static function sorted(Criteria $c, $career = null)
    {
      $has_school_year = !is_null($career) && !is_null(CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($this->getCareer(), SchoolYearPeer::retrieveCurrent()));

      if ($has_school_year)
      {
        CareerSubjectSchoolYearPeer::sorted($c);  
      } 
      
      $c->addAscendingOrderByColumn(SubjectPeer::NAME);
      $c->addJoin(self::SUBJECT_ID, SubjectPeer::ID);
      
   }

  public static function retrieveForExaminationRepproved(PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, self::ID);
    $c->addGroupByColumn(self::ID);

    return self::doSelect($c, $con);
  }

  public static function retrieveByCareerSubjectSchoolYearId($career_subject_school_year_id)
  {
      return CareerSubjectSchoolYearPeer::retrieveByPK($career_subject_school_year_id)->getCareerSubject();
  }

   public static function OrderByYearAndName(Criteria $c)
   {
     $c->addAscendingOrderByColumn(self::YEAR);
     $c->addAscendingOrderByColumn(SubjectPeer::NAME);
     $c->addJoin(self::SUBJECT_ID, SubjectPeer::ID);
   }
}
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

class CareerSubjectSchoolYearPeer extends BaseCareerSubjectSchoolYearPeer
{

  static public function getAvailableChoicesCriteria($career_subject_school_year, $exclude_related = true, $exclude_related = true)
  {
    return SchoolBehaviourFactory::getInstance()->getAvailableChoicesForCareerSubjectSchoolYearCriteria($career_subject_school_year, $exclude_related, $exclude_related);

  }

  /**
   * Returns CareerSubjectSchoolYears for a given examination
   *
   * @param Examination $examination
   * @param PropelPDO $con
   * @return CareerSubjectSchoolYear[]
   */
  public static function retrieveForExamination(Examination $examination, PropelPDO $con)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $c = new Criteria();

    $c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, self::ID);

    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);

    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $examination->getSchoolYearId());
    $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $examination->getExaminationNumber());

    $c->addGroupByColumn(self::ID);

    return self::doSelect($c, $con);

  }

  /**
   * Retrieves a career subject school year by career subject and school year pass by parameter
   * @param CareerSubject $cs
   * @param SchoolYear $school_year
   *
   * @return CareerSubjectSchoolYear
   */
  public static function retrieveByCareerSubject(CareerSubject $cs)
  {
    $c = new Criteria();
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(self::CAREER_SUBJECT_ID, $cs->getId());
    return self::doSelectOne($c);

  }

  public static function retrieveByCareerSubjectAndSchoolYear(CareerSubject $cs, SchoolYear $school_year = null)
  {
    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::CAREER_ID, $cs->getCareerId());
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CareerSchoolYearPeer::ID, self::CAREER_SCHOOL_YEAR_ID, Criteria::INNER_JOIN);
    $c->add(self::CAREER_SUBJECT_ID, $cs->getId());

    return self::doSelectOne($c);

  }

  public static function sorted(Criteria $criteria)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->addJoin(CareerSubjectPeer::ID, self::CAREER_SUBJECT_ID);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $criteria->addAscendingOrderByColumn(CareerSubjectPeer::YEAR);
    $criteria->addAscendingOrderByColumn(self::INDEX_SORT);
    $criteria->addAscendingOrderByColumn(SubjectPeer::NAME);

  }

  public static function getCurrentCareerSubjectSchoolYearIdsBySubjectId($subjec_id)
  {
    $criteria = new Criteria();

    $criteria->add(SubjectPeer::ID, $subjec_id);
    $criteria->addJoin(SubjectPeer::ID, CareerSubjectPeer::SUBJECT_ID);
    $criteria->addJoin(CareerSubjectPeer::ID, self::CAREER_SUBJECT_ID);
    $criteria->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    $criteria->clearSelectColumns();
    $criteria->addSelectColumn(self::ID);
    $stmt = self::doSelectStmt($criteria);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);


  }

	/**
	 * Returns CareerSubjectSchoolYears for a given examination and year
	 *
	 * @param Examination $examination
	 * @param Integer $year
	 * @param PropelPDO $con
	 * @return CareerSubjectSchoolYear[]
	 */
	public static function retrieveForExaminationAndYear(Examination $examination, $year, PropelPDO $con = null)
	{
		$con = is_null($con) ? Propel::getConnection() : $con;

		$c = new Criteria();

		$c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
		$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
		$c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, self::ID);

		$c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
		$c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $examination->getSchoolYearId());
		$c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $examination->getExaminationNumber());
		$c->addJoin(CareerSubjectPeer::ID, self::CAREER_SUBJECT_ID);
		$c->add(CareerSubjectPeer::YEAR, $year);
                $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, NULL, Criteria::ISNULL);
		$c->setDistinct();
		return self::doSelect($c, $con);
	}
}
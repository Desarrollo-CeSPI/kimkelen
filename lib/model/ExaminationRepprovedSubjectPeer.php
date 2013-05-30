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

class ExaminationRepprovedSubjectPeer extends BaseExaminationRepprovedSubjectPeer
{
  public static function canCreateExaminationRepprovedFor(SchoolYear $school_year, PropelPDO $con = null)
  {
    $ok = true;
    foreach(ExaminationRepprovedType::getInstance('ExaminationRepprovedType')->getKeys() as $type)
    {
      $ok = $ok 
            && (self::countNotClosedExaminationRepprovedSubjectsForSchoolYearAndType($school_year, $type, $con)
            || self::countExaminationRepprovedWithoutSubjects($school_year, $type, $con));
    }
    return !$ok;
  }
  
  public static function countNotClosedExaminationRepprovedSubjectsForSchoolYearAndType(SchoolYear $school_year, $type, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $c = new Criteria();
    $c->add(ExaminationRepprovedPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID);
    $c->add(ExaminationRepprovedSubjectPeer::IS_CLOSED, false);
    $c->add(ExaminationRepprovedPeer::EXAMINATION_TYPE, $type);
    return ExaminationRepprovedSubjectPeer::doCount($c, $con);
  }
  
  public static function countExaminationRepprovedWithoutSubjects(SchoolYear $school_year, $type, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $c = new Criteria();
    $c->add(ExaminationRepprovedPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID, Criteria::RIGHT_JOIN);
    $c->add(ExaminationRepprovedSubjectPeer::ID, null, Criteria::ISNULL);
    return ExaminationRepprovedPeer::doCount($c, $con);
  }

  public static function sortedBySubject(Criteria $c = null)
  {
    if (is_null($c))
    {
      $c = new Criteria();
    }

    $c->addJoin(self::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->addAscendingOrderByColumn(CareerSubjectPeer::YEAR);
    $c->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $c->addAscendingOrderByColumn(SubjectPeer::NAME);

    return $c;
  }
}
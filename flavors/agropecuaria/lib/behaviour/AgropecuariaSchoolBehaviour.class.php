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

class AgropecuariaSchoolBehaviour extends BaseSchoolBehaviour
{


  protected $school_name  ="Escuela de Educación Técnico Profesional de Nivel Medio en Producción Agropecuaria y Agroalimentaria - Facultad de Ciencias Veterinarias de la UBA";
  protected $HOUR_FOR_SCHEMA = array(7 => "07", 8 => "08", 9 => "09", 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18);
  protected $MINUTES_FOR_SCHEMA = array(0 => "00", "05" => "05", 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50, 55 => 55);

  const BLOCKS_PER_COURSE_SUBJECT_DAY = 2;

  public function getDefaultCityId()
  {
    return City::BUENOS_AIRES;

  }

  public function getDefaultStateId()
  {
    return State::CIUDAD_AUTONOMA;

  }

  public function getBlocksPerCourseSubjectDay()
  {
    return self::BLOCKS_PER_COURSE_SUBJECT_DAY;

  }

  public function showReportCardRepproveds()
  {
    return true;

  }

  public function getDivisionCourseType()
  {
    return CourseType::QUATERLY;

  }

  public function canRelatedToDivision($division = null, $is_current_school_year = null)
  {
    return is_null($division) && $is_current_school_year;

  }

   public function getTotalAbsences($career_school_year_id, $period=null, $course_subject_id = null, $exclude_justificated = true, $student_id)
  {
    $absences = $this->getAbsences($career_school_year_id, $student_id, $period, $course_subject_id, $exclude_justificated);
    $rounder = new StudentAttendanceRounder();
    $total = 0;

    foreach ($absences as $absence)
    {
      // sacamos las justificadas, es decir se quiere el total SIN las justificadas
      if ($exclude_justificated && $absence->hasJustification())
      {
        continue;
      }
      else{
        $total += $absence->getValue();
        $rounder->process($absence);
      }

    }

    $diff = $rounder->calculateDiff();

    return $total + $diff;
  }

  /**
   * This method returns the absences depending of arguments:
   *
   * IF period_id is null, then returns all the absences.
   * IF course_subject_id is null then returns the absences per day.
   * IF include_justificated is null, then excludes the absences justificated.
   *
   * @param type $career_school_year_id
   * @param type $student_id
   * @param type $period_id
   * @param type $course_subject_id
   * @param type $include_justificated
   *
   * @return StudentAttendance array
   */
  public function getAbsences($career_school_year_id, $student_id, $period = null, $course_subject_id = null, $exclude_justificated=true)
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::STUDENT_ID, $student_id);
    $c->add(StudentAttendancePeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);

    if ($course_subject_id instanceof CourseSubject)
    {
      $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $course_subject_id->getId());
    }
    else
    {
      $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $course_subject_id);
    }


    $c->add(StudentAttendancePeer::VALUE, 0, Criteria::NOT_EQUAL);

    if (!is_null($period))
    {
      $criterion = $c->getNewCriterion(StudentAttendancePeer::DAY, $period->getStartAt(), Criteria::GREATER_EQUAL);
      $criterion->addAnd($c->getNewCriterion(StudentAttendancePeer::DAY, $period->getEndAt(), Criteria::LESS_EQUAL));
      $c->add($criterion);
    }
    return $student_attendances = StudentAttendancePeer::doSelect($c);

  }

}
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

class SchoolYearStudent extends BaseSchoolYearStudent
{
/**
 *
 * @see @student actions
 * @return boolean
 */
  public function canBeDeleted()
  {
    return true;
  }

  public function createStudentCareerSchoolYear($con)
  {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getStudentId());

    foreach (CareerStudentPeer::doSelect($c) as $career_student)
    {
      /*
       * @CareerStudent $career_student
       */
      $career_school_year = $career_student->getCareer()->getCareerSchoolYear($this->getSchoolYear());
      if ( $career_school_year &&
           StudentCareerSchoolYearPeer::countByCareerAndStudent($career_student->getCareerId(), $career_student->getStudentId(), $this->getSchoolYearId()) == 0)
      {
        $last_student_career_school_year = $career_student->getCurrentStudentCareerSchoolYear();

        $year = (is_null($last_student_career_school_year)) ? $career_student->getStartYear() : $last_student_career_school_year->suggestYear();

        if ($year <= $career_school_year->getCareer()->getQuantityYears())
        {
          $student_career_school_year = new StudentCareerSchoolYear();
          $student_career_school_year->setCareerSchoolYear($career_school_year);
          $student_career_school_year->setStudentId($this->getStudentId());

          //SI REPITIO
          if (!is_null($last_student_career_school_year) && $last_student_career_school_year->getStatus() == StudentCareerSchoolYearStatus::REPPROVED)
          {
            $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED);
          }

          $student_career_school_year->setYear($year);
          $student_career_school_year->save($con);
        }
      }
    }
  }
}

try { sfPropelBehavior::add('SchoolYearStudent', array('studentCareerSchoolYear')); } catch(sfConfigurationException $e) {}

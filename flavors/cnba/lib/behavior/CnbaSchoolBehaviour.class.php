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

/**
 * Copy and rename this class if you want to extend and customize
 */
class CnbaSchoolBehaviour extends BaseSchoolBehaviour
{
  protected $school_name = "Colegio Nacional de Buenos Aires";
  protected $MINUTES_FOR_SCHEMA = array(0 => "00", 5 => "5", 10 => "10", 15 => "15", 20 => "20", 25 => "25", 30 => "30", 35 => "35", 40 => "40", 45 => "45", 50 => "50", 55 => "55");
  protected $HOUR_FOR_SCHEMA = array(7 => "07", 8 => "08", 9 => "09", 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22');

  const DAYS_FOR_MULTIPLE_ATTENDANCE_FORM = 0;
  const BLOCKS_PER_COURSE_SUBJECT_DAY = 2;

  public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState', 'label' => 'Cambiar vigencia', 'credentials' => array(0 => 'edit_school_year',),),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array(0 => 'show_school_year',), 'label' => 'Registered students',),
      'careers' => array('action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' => array(0 => 'show_career',),),
      'examinations' => array('action' => 'examinations', 'label' => 'Examinations', 'credentials' => array(0 => 'show_examination',), 'condition' => 'canExamination',),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved',), 'condition' => 'canExamination',),
      '_delete' => array('credentials' => array(0 => 'edit_school_year',), 'condition' => 'canBeDeleted',),
    );

  }

  public function getDefaultCityId()
  {
    return City::BUENOS_AIRES;

  }

  public function getDefaultStateId()
  {
    return State::CIUDAD_AUTONOMA;

  }

  public function getSubjectToString($subject)
  {
    return $subject->getFantasyName();

  }

  public function getDaysForMultipleAttendanceForm()
  {
    return self::DAYS_FOR_MULTIPLE_ATTENDANCE_FORM;

  }

  /*
   * This method sums the total of absences, in this behavior
   * if the student has been justificated for more than 7 days, then the entire value of the absences is justificated, otherwise
   * it only justificates the half of the value.
   */

  public function getTotalAbsences($career_school_year_id, $period, $course_subject_id = null, $exclude_justificated = true, $student_id)
  {
    $absences = $this->getAbsences($career_school_year_id, $student_id, $period, $course_subject_id);
    $rounder  = new StudentAttendanceRounder();
    $total    = 0;

    foreach ($absences as $absence)
    {
      if ($exclude_justificated) //  si se excluyen las justificadas
      {
        $justificated = $absence->getStudentAttendanceJustification();
        if (!is_null($justificated))
        {
          $cant = $justificated->countStudentAttendances();
          if ($justificated->countStudentAttendances() >= 5 && $justificated->countStudentAttendances() < 7)
          {
            $total += 0.5;
          }
          elseif ($justificated->countStudentAttendances() < 5)
          {
             $total += $absence->getValue();
             $rounder->process($absence);
          }
        }
        else
        {
          $total += $absence->getValue();
          $rounder->process($absence);
        }
      }
      else  //$exclude_justificated= false, o sea  HAY QUE INCLUIR las justificadas
      {
        $total += $absence->getValue();
        $rounder->process($absence);
      }
    }

    $diff = $rounder->calculateDiff();

    return $total + $diff;

  }

  public function getBlocksPerCourseSubjectDay()
  {
    return self::BLOCKS_PER_COURSE_SUBJECT_DAY;

  }

}
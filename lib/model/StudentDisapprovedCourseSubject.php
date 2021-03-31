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

class StudentDisapprovedCourseSubject extends BaseStudentDisapprovedCourseSubject
{

  public function __toString()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getStudentDisapprovedResultString($this);
  }

  public function getClass()
  {
    return sfInflector::underscore(SchoolBehaviourFactory::getEvaluatorInstance()->getStringFor($this->getExaminationNumber()));
  }

  public function getColor()
  {
    if ($this->getExaminationNumber() == 1 )
    {
      return 'mark_yellow';
    }

    return 'mark_red';
  }

  /*
   * This method has to create the examination that corresponds to the examination_number
   */
  public function close(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    SchoolBehaviourFactory::getEvaluatorInstance()->closeCourseSubjectStudent($this, $con);
  }

  public function getStudent()
  {
    return $this->getCourseSubjectStudent()->getStudent();
  }

  public function getResultStr()
  {
    if ($this->getCourseSubjectStudent()->getIsNotAverageable())
    {
      return "";
    }
    return SchoolBehaviourFactory::getEvaluatorInstance()->getStudentDisapprovedResultStringShort($this);
  }

  public function getCourseSubject()
  {
    return $this->getCourseSubjectStudent()->getCourseSubject();
  }

  public function getCareerSchoolYear()
  {
    return $this->getCourseSubject()->getCareerSchoolYear();
  }

  public function isApproved()
  {
    //If has the link to student_approved_career_subject means that the subject has been approved in examination
    return ! is_null($this->getStudentApprovedCareerSubject());
  }

  public function getFinalMark()
  {
    if (! $this->isApproved())
    {
      return null;
    }

    return $this->getStudentApprovedCareerSubject()->getMark();
  }
  
  public function getAvgColorDisapprovedReport()
  {
    if ($this->getExaminationNumber() == 1 )
    {
      return 'mark_yellow';
    }

    return 'mark_yellow_red';
  }
  public function getColorDisapprovedReport($examination_number)
  {
      $course_subject_student_examination = $this->getCourseSubjectStudent()->getCourseSubjectStudentExaminationsForExaminationNumber($examination_number);
      if(is_null($course_subject_student_examination))
      {
          return '';
      }
      else
      {
          if($course_subject_student_examination->getIsAbsent())
          {
              return 'absent';
          }
          else
          {
              if($examination_number == 1 && $course_subject_student_examination->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote())
              {
                  return 'mark_red';
              }
              elseif ($examination_number == 2 && $course_subject_student_examination->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote()) {
                  return 'febrero';
              }
          }
      }
  }
}
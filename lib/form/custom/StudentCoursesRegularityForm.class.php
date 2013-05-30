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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CourseSubjectStudentsRegularityForm
 *
 * @author mbrown
 */
class StudentCoursesRegularityForm extends sfForm {

  private $student;

  public function configure()
  {
    $this->widgetSchema->setNameFormat('student_courses_regularity[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);
  }

  public function setStudent(Student $student)
  {
    $this->student = $student;
    $course_subject_students = $this->student->getCourseSubjectStudents();
    foreach ($course_subject_students as $course_subject_student)
    {
      $course_subject_student_id = $course_subject_student->getId();

      $this->setWidget("student_$course_subject_student_id", new mtWidgetFormPlain(array(
        "object" => $course_subject_student,
        "add_hidden_input" => false,
        "use_retrieved_value" => false,
        'method' => 'getCourseSubject'
      )));

      $this->setValidator("student_$course_subject_student_id", new sfValidatorPass());

      $this->setDefault("student_$course_subject_student_id", $course_subject_student_id);
      $this->widgetSchema->setLabel("student_$course_subject_student_id", "Student");

      $this->setDefault("free_student_$course_subject_student_id", $course_subject_student->getIsFree());
      $this->setWidget("free_student_$course_subject_student_id", new sfWidgetFormInputCheckbox());
      $this->setValidator("free_student_$course_subject_student_id", new sfValidatorBoolean(array('required' => false)));

    }
  }

  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($con))
    {
      $con = Propel::getConnection(CareerStudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    try
    {
      $con->beginTransaction();

      $values = $this->getValues();

      foreach ($this->student->getCourseSubjectStudents() as $course_subject_student)
      {
        $course_subject_student->setIsFree($values['free_student_'.$course_subject_student->getId()]);
        $course_subject_student->save();
      }

      $con->commit();

    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
    
  }
}
?>
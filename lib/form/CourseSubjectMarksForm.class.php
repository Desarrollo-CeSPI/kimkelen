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
 * Description of CourseSubjectMarksForm
 *
 * @author ncuesta
 */
class CourseSubjectMarksForm extends BaseCourseSubjectForm
{
  public function configure()
  {
    $widgets    = array();
    $validators = array();

    $options = array(
      'min'      => $this->getMinimumMark(),
      'max'      => $this->getMaximumMark(),
      'required' => false
    );

    $messages = array(
      'min'     => 'La calificación debe ser al menos %min%.',
      'max'     => 'La calificación debe ser a lo sumo %max%.',
      'invalid' => 'El valor ingresado es inválido.'
    );
    $this->disableCSRFProtection();
    $tmp_sum = 0;
    foreach ($this->object->getCourseSubjectStudents() as $course_subject_student)
    {
      foreach ($course_subject_student->getAvailableCourseSubjectStudentMarks() as $course_subject_student_mark)
      {
        $widget_name = $course_subject_student->getId().'_'.$course_subject_student_mark->getMarkNumber();
        if ($course_subject_student_mark->getIsClosed())
        {
          $widgets[$widget_name] = new mtWidgetFormPlain(array(
              'object' => $course_subject_student_mark, 'method' => 'getMark', 'add_hidden_input' => false), array('class' => 'mark'));
          $widgets[$widget_name]->setAttribute('class', 'mark_note');
        }
        else
        {
          $widgets[$widget_name] = new sfWidgetFormInput(array('default' => $course_subject_student_mark->getMark()), array('class' => 'mark'));

          //IS FREE
          $free_widget_name = $course_subject_student->getId().'_free_'.$course_subject_student_mark->getMarkNumber();
          $name = 'course_student_mark_'. $this->getObject()->getId() . '_' . $widget_name;
          $name_free_element = 'course_student_mark_'. $this->getObject()->getId() . '_' . $free_widget_name;
         $widgets[$free_widget_name] = new sfWidgetFormInputCheckbox(array('default' => $course_subject_student_mark->getIsFree()), array('onChange' => "free_mark('$name_free_element','$name');"));


          if ($course_subject_student_mark->getIsFree())
          {
            $widgets[$widget_name]->setAttribute('style', 'display:none');
          }
          $validators[$free_widget_name] = new sfValidatorBoolean();
        }
        $tmp_sum = $this->evaluationFinalProm($course_subject_student, $course_subject_student_mark, $tmp_sum);
        $validators[$widget_name] = new sfValidatorNumber($options, $messages);

      }
      $tmp_sum = 0;
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'][%s]');
  }

  public function evaluationFinalProm($course_subject_student, $course_subject_student_mark, $tmp_sum){
    return $tmp_sum;
  }

  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(),array('course_subject_student_mark.js'));
  }

  protected function getMinimumMark()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark();
  }

  protected function getMaximumMark()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMaximumMark();
  }

  protected function doSave($con = null)
  {
    $values = $this->getValues();

    $c = new Criteria();
    $c->add(CourseSubjectStudentMarkPeer::IS_CLOSED, false);
    foreach ($this->object->getCourseSubjectStudents() as $course_subject_student)
    {
      foreach ($course_subject_student->getAvailableCourseSubjectStudentMarks($c) as $course_subject_student_mark)
      {
        $is_free = $values[$course_subject_student->getId() . '_free_' . $course_subject_student_mark->getMarkNumber()];
        $value = $values[$course_subject_student->getId() . '_' . $course_subject_student_mark->getMarkNumber()];
        if ((!is_null($is_free)))
        {
          if ($is_free)
          {
            $value = 0;
          }

          $course_subject_student_mark->setMark($value);
          $course_subject_student_mark->setIsFree($is_free);
          $course_subject_student_mark->save($con);
        }
      }
    }
  }
}
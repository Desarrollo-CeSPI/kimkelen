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
class CourseSubjectNotAverageableMarksForm extends BaseCourseSubjectForm
{
  public function configure()
  {
    $widgets    = array();
    $validators = array();

    $this->disableCSRFProtection();
    
    foreach ($this->object->getCourseSubjectStudents() as $course_subject_student)
    {
      
        
        $calification_final_widget_name = $course_subject_student->getId().'_calification_final';
      
        if(!is_null($course_subject_student->getNotAverageableCalification())) {
          $this->setDefault($calification_final_widget_name, $course_subject_student->getNotAverageableCalification());
        }
   
      $widgets[$calification_final_widget_name] = new sfWidgetFormSelect(array(
          'choices'  => BaseCustomOptionsHolder::getInstance('NotAverageableCalificationType')->getOptions(true)
           ));
     $validators[$calification_final_widget_name] = new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('NotAverageableCalificationType')->getKeys(),
        'required'=>false));
      
        
        
        }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'][%s]');
  }



  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(),array('course_subject_student_mark.js'));
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
          else
          {
            if($value != null)
            {
              if (!$course_subject_student->getConfiguration()->isNumericalMark())
              {
                $value = LetterMarkPeer::retrieveByPk($value)->getValue();
              }
            }
          }

          $course_subject_student_mark->setMark($value);
          $course_subject_student_mark->setIsFree($is_free);
          $course_subject_student_mark->save($con);
        }
      }
    }
  }
}

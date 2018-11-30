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
 * @author 
 */
class NacionalCourseSubjectMarksForm extends CourseSubjectMarksForm
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
      'invalid' => 'El valor ingresado es inválido, solo se aceptan numeros enteros.'
    );

    $this->disableCSRFProtection();
    $tmp_sum = 0;
    $configuration = $this->object->getCareerSubjectSchoolYear()->getConfiguration();
    $course_marks = $configuration->getCourseMarks();
    if($this->object->getCourseSubjectConfigurations())
    {
       $period = array();
       foreach($this->object->getCourseSubjectConfigurations() as $csc)
       {
           $period[] = $csc->getCareerSchoolYearPeriod();
       }
       
    }
    else
    {
        $period = CareerSchoolYearPeriodPeer::retrieveCurrents($configuration->getCourseType());
    }
    
    $current_period = $this->object->getCourse()->getCurrentPeriod();
    
    foreach ($this->object->getCourseSubjectStudents() as $course_subject_student)
    {
      
      foreach ($course_subject_student->getAvailableCourseSubjectStudentMarks() as $course_subject_student_mark)
      {
        $widget_name = $course_subject_student->getId().'_'.$course_subject_student_mark->getMarkNumber();
        if ($course_subject_student_mark->getIsClosed())
        {
          $widgets[$widget_name] = new mtWidgetFormPlain(array(
              'object' => $course_subject_student_mark, 'method' => 'getMarkByConfig', 'method_args' => $configuration, 'add_hidden_input' => false), array('class' => 'mark'));
          $widgets[$widget_name]->setAttribute('class', 'mark_note');

        }
        else
        {
          if($configuration->isNumericalMark())
          { $p = $period[$current_period-1];

            $widgets[$widget_name] = new sfWidgetFormInput(array('default' => $course_subject_student_mark->getMark()), array('class' => 'mark'));
            $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($course_subject_student->getStudent(), $this->object->getCareerSubjectSchoolYear()->getCareerSchoolYear());
            
            $is_free = StudentFreePeer::retrieveByStudentCareerSchoolYearCareerSchoolYearPeriodAndCourseSubject($student_career_school_year, $p, $this->object);
            
            //si está libre
            if ($is_free)
            {
                //si (la nota es la última) y (tiene más notas que periodos)
                if($course_subject_student_mark->getMarkNumber() == $course_marks && count($period) < $course_marks )
                {
                    //está libre en todos los periodos
                    $list_cssm = CourseSubjectStudentMarkPeer::retrieveByCourseSubjectStudent($course_subject_student->getId());
                    $is_real_free = TRUE; 
                    foreach($list_cssm as $cssm)
                    {
                        if(! is_null($cssm->getMark()) && $cssm->getMark() != 0)
                        {
                             $is_real_free = FALSE;
                        }
                        
                    }
                    
                    if($is_real_free)
                    {
                        $widgets[$widget_name]->setAttribute('disabled', 'disabled');
                    }
    
                }
                else
                { //no es la ultima
                   $widgets[$widget_name]->setAttribute('disabled', 'disabled');
                }
                
            } 
            $validators[$widget_name] = new sfValidatorInteger($options, $messages);
          }
          else
          {
            $letter_mark = LetterMarkPeer::getLetterMarkByValue((Int)$course_subject_student_mark->getMark());
            
            if(!is_null($letter_mark)) {
              $this->setDefault($widget_name, $letter_mark->getId());
            }
            $widgets[$widget_name] = new sfWidgetFormPropelChoice(array('model'=> 'LetterMark', 'add_empty' => true));
            $validators[$widget_name] = new sfValidatorPropelChoice(array('model' => 'LetterMark', 'required' => false));
          }
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
        

      }
      $tmp_sum = 0;
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'][%s]');
  }
}
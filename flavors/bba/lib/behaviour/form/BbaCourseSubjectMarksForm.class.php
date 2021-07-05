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
 * @author Ivan
 */
class BbaCourseSubjectMarksForm extends CourseSubjectMarksForm
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
          {
            $widgets[$widget_name] = new sfWidgetFormInput(array('default' => $course_subject_student_mark->getMark()), array('class' => 'mark'));
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
       
        // OBSERVATIONS
          $observation_widget_name = $course_subject_student->getId().'_observation_'.$course_subject_student_mark->getMarkNumber();
          $name_observation_element = 'course_student_mark_'. $this->getObject()->getId() . '_' . $observation_widget_name;
          
          if(!is_null($course_subject_student_mark->getObservationMarkId())) {
              $this->setDefault($observation_widget_name, $course_subject_student_mark->getObservationMarkId());
          }
          $widgets[$observation_widget_name] = new sfWidgetFormPropelChoice(array('model'=> 'ObservationMark', 'add_empty' => true));
          $validators[$observation_widget_name] = new sfValidatorPropelChoice(array('model' => 'ObservationMark', 'required' => false)); 

      }
     // OBSERVATIONS final


      $observation_final_widget_name = $course_subject_student->getId().'_observation_final';
      
     if(!is_null($course_subject_student->getObservationFinal())) {
          $this->setDefault($observation_final_widget_name, $course_subject_student->getObservationFinal());
    }
   
      $widgets[$observation_final_widget_name] = new sfWidgetFormSelect(array(
          'choices'  => BaseCustomOptionsHolder::getInstance('ObservationFinalType')->getOptions(true)
           ));
     $validators[$observation_final_widget_name] = new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('ObservationFinalType')->getKeys(),
        'required'=>false));
      
      $tmp_sum = 0;
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'][%s]');
  } 

  public function evaluationFinalProm($course_subject_student, $course_subject_student_mark, $tmp_sum)
  {
    $subject_configuration = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSubjectConfigurationOrCreate();
    if ($subject_configuration->getEvaluationMethod() == EvaluationMethod::FINAL_PROM)
    {
      /**
       * Si es la ultima nota!
       */
      if ($course_subject_student_mark->getMarkNumber() == ($subject_configuration->getCourseMarks() - 1))
      {
        $widgets[$course_subject_student->getId() . '_final_prom'] = new mtWidgetFormPlain(array(
            'add_hidden_input' => false,
            'empty_value' => '' . (!$course_subject_student_mark->getMark() && $course_subject_student_mark->getIsFree()) ? __('free') : (int) ceil(($tmp_sum + $course_subject_student_mark->getMark()) / ($subject_configuration->getCourseMarks() - 1))),
            array('class' => 'mark'));

        $validators[$course_subject_student->getId() . '_final_prom'] = new sfValidatorPass(array('required' => false));
      }
      elseif ($course_subject_student_mark->getMarkNumber() < ($subject_configuration->getCourseMarks() - 1))
      {
        $tmp_sum += $course_subject_student_mark->getMark();
      }
    }
    return  $tmp_sum;
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
       
       $observation_mark  = $values[$course_subject_student->getId() . '_observation_' . $course_subject_student_mark->getMarkNumber()];
       //$observation_mark = ObservationMarkPeer::retrieveByPk((int)$observation_value);
      
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
          if(! is_null($observation_mark))
          { 
             $course_subject_student_mark->setObservationMarkId((int)$observation_mark);
          }
          else
          {
            $course_subject_student_mark->setObservationMarkId(NULL);
          }
          $course_subject_student_mark->save($con);
        }
      }

       $observation_final  = $values[$course_subject_student->getId() . '_observation_final'];
       if(! is_null($observation_final))
        { 
           $course_subject_student->setObservationFinal((int)$observation_final);
        }
        else
       {
           $course_subject_student->setObservationFinal(NULL);
       }
        $course_subject_student->save($con);

    }
  }

}

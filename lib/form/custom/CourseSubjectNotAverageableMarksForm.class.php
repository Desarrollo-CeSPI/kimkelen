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
    
    foreach ($this->object->getCourseSubjectStudentsNotAverageable() as $course_subject_student)
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

    $this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'_calification_final][%s]');
  }



  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(),array('course_subject_student_mark.js'));
  }

 

  protected function doSave($con = null)
  {
    $values = $this->getValues();
    $c = new Criteria();
    foreach ($this->object->getCourseSubjectStudentsNotAverageable() as $course_subject_student)
    {
        $value = $values[$course_subject_student->getId() . '_calification_final'];
      
      
      
      
      $c1 = new Criteria();
      $c1->add(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $course_subject_student->getId());
            
      $sdcs = StudentDisapprovedCourseSubjectPeer::doSelectOne($c1);
      $sacs = StudentApprovedCourseSubjectPeer::retrieveForCourseSujectStudent($course_subject_student);
        
        if($value == 1)
        {//aprobado
            
            ///si estaba desaprobado o no tiene nota aprobado
            if(!is_null($sdcs) || is_null($sacs))
            {
                if(!is_null($sdcs))
                {
                    $sdcs->delete();
                }

                $school_year = $course_subject_student->getCourseSubject($con)->getCourse($con)->getSchoolYear($con);
                $student_approved_course_subject = new StudentApprovedCourseSubject();
                $student_approved_course_subject->setCourseSubject($course_subject_student->getCourseSubject($con));
                $student_approved_course_subject->setStudent($course_subject_student->getStudent($con));
                $student_approved_course_subject->setSchoolYear($school_year);

                $student_approved_course_subject->save();
                $course_subject_student->setStudentApprovedCourseSubject($student_approved_course_subject);

                foreach ($course_subject_student->getCourseSubjectStudentMarks($c) as $course_subject_student_mark)
                {
                    $course_subject_student_mark->setIsClosed(TRUE);
                    $course_subject_student_mark->save($con);

                }
                
                $course_subject_student->setIsNotAverageable(TRUE);
                $course_subject_student->setNotAverageableCalification($value);
                $course_subject_student->save();
            }
   

        }
        elseif($value == 2)
        {
            
            ///si estaba aprobado o no tiene nota desaprobado
            if(!is_null($sacs) || is_null($sdcs))
            {
                if(!is_null($sacs))
                {
                    $sacs->delete();
                }

                $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
                $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);
                $student_disapproved_course_subject->setExaminationNumber(1);
                $student_disapproved_course_subject->save();
              
                foreach ($course_subject_student->getCourseSubjectStudentMarks($c) as $course_subject_student_mark)
                {
                    $course_subject_student_mark->setIsClosed(TRUE);
                    $course_subject_student_mark->save($con);

                }
                
                $course_subject_student->setIsNotAverageable(TRUE);
                $course_subject_student->setNotAverageableCalification($value);
                $course_subject_student->save();
            }

        }
        
         
        
      }
         
    }
  
}

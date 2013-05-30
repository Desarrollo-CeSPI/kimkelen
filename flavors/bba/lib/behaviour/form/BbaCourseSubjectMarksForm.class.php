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

}
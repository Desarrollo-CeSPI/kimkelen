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
class FhaycsalmafuerteEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    return self::DECEMBER; //Todos los alumnos se van a diciembre

  }

  const POSTPONED_NOTE_FHAYCS = 5; //nota del 3er trimestre no puede ser menor de 6

  public function getPosponedNote()
  {
    return self::POSTPONED_NOTE_FHAYCS;
  }

  public function getAverage($course_subject_student, $course_subject_student_examination)
  {
    $examination = $course_subject_student_examination->getExaminationSubject()->getExamination();
    #DICIEMBRE
    if ($examination->getExaminationNumber() == self::DECEMBER)
    {
      return $course_subject_student_examination->getMark();
    }
    elseif ($examination->getExaminationNumber() == self::FEBRUARY)
    {
      return $course_subject_student_examination->getMark();
    }
    else
    {
      return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
    }

  }
  
}
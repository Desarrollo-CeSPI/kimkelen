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

class studentComponents extends sfComponents
{

  public function executeComponent_analytical_table()
  {

    //De aca recupero el nombre del establecimiento
    $this->career_student = $this->getVar('career_student');

    $this->student =  $this->career_student->getStudent();

    $this->student_career_school_years = $this->student->getStudentCareerSchoolYears();

    //Deberia recorrer todos los "scsy" y recuperar por c/año las materias 

    $this->objects = array();
    $avg_mark_for_year = array();

    foreach ($this->student_career_school_years as $scsy ) {

        //Si no repitio el año lo muestro en el analitico - Ver que pasa cuando se cambia de escuela y repite el ultimo año
        //Siempre tomo el año "Aprobado" - Ver si esta bien asi o si deberia quedarme con el ultimo 
        if ($scsy->getStatus() == 1){

            $year_in_career = $scsy->getYear();

            $career_school_year = $scsy->getCareerSchoolYear();

            $school_year = $career_school_year->getSchoolYear();

            $approved = StudentApprovedCareerSubjectPeer::retrieveByStudentAndSchoolYear($this->student, $school_year);

            $csss = SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForAnalytics($this->student, $school_year);

            foreach($csss as $css){
                 if(!isset($this->objects[$year_in_career])){
                     $this->objects[$year_in_career] = array();
                     $this->objects[$year_in_career]['status'] = 'C';
                     $avg_mark_for_year[$year_in_career]['sum'] = 0;
                     $avg_mark_for_year[$year_in_career]['count'] = 0;
                 }
                 $avg_mark_for_year[$year_in_career]['sum'] += $css->getMark();
                 $avg_mark_for_year[$year_in_career]['count'] += ($css->getMark(false)?1:0);
                 if (!$css->getMark(false))
                 {
                     $this->objects[$year_in_career]['status'] = 'I';
                 }
                 $this->objects[$year_in_career]['subjects'][] = $css;
            }
            foreach ($this->objects as $year => $data)
            {
                $this->objects[$year]['average'] = ($avg_mark_for_year[$year]['sum'] / $avg_mark_for_year[$year]['count']);
            }
        }
    }

  }

}


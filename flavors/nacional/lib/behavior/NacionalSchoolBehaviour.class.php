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
class NacionalSchoolBehaviour extends BaseSchoolBehaviour
{
  protected $school_name = "Colegio Nacional Rafael Hernández";
  protected  $MINUTES_FOR_SCHEMA = array(0 => "00", 5 => "5",  10 => "10", 15 => "15", 20 => "20", 25=>"25", 30 => "30", 35 => "35", 40 => "40", 45 => "45", 50 => "50", 55 => "55");

  public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState',  'label' => 'Cambiar vigencia',  'credentials' =>   array( 0 => 'edit_school_year' ,), ),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array( 0 => 'show_school_year', ), 'label' => 'Registered students',  ),
      'careers' => array( 'action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' =>  array( 0 => 'show_career', ),),
      'examinations' => array( 'action' => 'examinations', 'label' => 'Examinations', 'credentials' =>  array( 0 => 'show_examination',),'condition' => 'canExamination',  ),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved', ),'condition' => 'canExamination', ),
      '_delete' => array('credentials' => array( 0 => 'edit_school_year',),'condition' => 'canBeDeleted',),
    );
  }

  public function getFileNumberIsGlobal()
  {
    return true;
  }

  protected function getClassSubjectStudentAnalytic()
  {
    return 'NacionalSubjectStudentAnalytic';
  }

}
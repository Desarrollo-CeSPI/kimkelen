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

class backendConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    $this->dispatcher->connect('admin.build_criteria', array('AdminGeneratorFiltersClass', 'applyRestrictions'));
    
  }
  
  public function initialize()
  {
    parent::initialize();
    
    // Register behavior's hooks
    
    sfPropelBehavior::registerHooks('studentCareerSchoolYear', array(
      ':save:pre'    => array('StudentCareerSchoolYearBehavior', 'createStudentCareerSchoolYear'),
    ));
    
    sfPropelBehavior::registerHooks('examination', array(
      ':save:pre' => array('ExaminationBehavior', 'createExaminationSubjects'),
    ));
    
    //sfPropelBehavior::registerHooks('examination_subject', array(
    //  ':save:pre' => array('ExaminationSubjectBehavior', 'updateCourseSubjectStudentExaminations'),
    // ));
    
    sfPropelBehavior::registerHooks('examination_repproved_subject', array(
      ':save:pre' => array('ExaminationRepprovedSubjectBehavior', 'updateStudentExaminationRepprovedSubjects'),
    ));

    sfPropelBehavior::registerHooks('student_approved_career_subject', array(
      ':save:post' => array('StudentApprovedCareerSubjectBehavior', 'graduateStudent'),
    ));
    
    sfPropelBehavior::registerHooks('student_approved_course_subject', array(
      ':save:post' => array('StudentRepprovedCourseSubjectBehavior', 'checkRepeatition'),
    ));


    sfPropelBehavior::registerHooks('student_reincorporation_save', array(
      ':save:post' => array('StudentReincorporationBehavior', 'updateReincorporation'),
    ));

    sfPropelBehavior::registerHooks('student_reincorporation_delete', array(
      ':delete:pre' => array('StudentReincorporationBehavior', 'deleteReincorporation'),
    ));

    
    sfPropelBehavior::registerHooks('career_subject_school_year_update', array(
      ':save:post' => array('CareerSubjectSchoolYearBehavior', 'updateCareerSubjectSchoolYear'),
    ));

    sfPropelBehavior::registerHooks('person_delete', array(
      ':delete:pre' => array('PersonBehavior', 'deletePerson'),
    ));

    spl_autoload_register(array('ncFlavorAutoload', 'initialize'));
  }
}
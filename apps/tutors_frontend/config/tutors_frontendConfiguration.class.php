<?php

class tutors_frontendConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
  }

  public function initialize()
  {
    parent::initialize();

    // Register behavior's hooks


    sfPropelBehavior::registerHooks('studentCareerSchoolYear', array(
      ':save:pre'    => array('StudentCareerSchoolYearBehavior', 'createStudentCareerSchoolYear'),
    ));
   /* 
    sfPropelBehavior::registerHooks('examination_subject', array(
      ':save:pre' => array('ExaminationSubjectBehavior', 'updateCourseSubjectStudentExaminations'),
     ));
    
    sfPropelBehavior::registerHooks('examination_repproved_subject', array(
      ':save:pre' => array('ExaminationRepprovedSubjectBehavior', 'updateStudentExaminationRepprovedSubjects'),
    ));

    sfPropelBehavior::registerHooks('student_reincorporation_save', array(
      ':save:post' => array('StudentReincorporationBehavior', 'updateReincorporation'),
    ));

    sfPropelBehavior::registerHooks('student_reincorporation_delete', array(
      ':delete:pre' => array('StudentReincorporationBehavior', 'deleteReincorporation'),
    ));

    */
    sfPropelBehavior::registerHooks('student_approved_career_subject', array(
      ':save:post' => array('StudentApprovedCareerSubjectBehavior', 'graduateStudent'),
    ));
    
    sfPropelBehavior::registerHooks('student_approved_course_subject', array(
      ':save:post' => array('StudentRepprovedCourseSubjectBehavior', 'checkRepeatition'),
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

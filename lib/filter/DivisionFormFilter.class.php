<?php

/**
 * Division filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class DivisionFormFilter extends BaseDivisionFormFilter
{

  public function configure()
  {
    $this->getWidget('year')->setOption("with_empty", false);

    $sf_user = sfContext::getInstance()->getUser();

    $this->setWidget('school_year_id', new sfWidgetFormPropelChoice(array('model' => 'SchoolYear', 'add_empty' => false)));
    $this->setValidator('school_year_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'SchoolYear', 'column' => 'id')));


    if ($sf_user->hasCredential('preceptor_filter'))
    {
      $this->setWidget('preceptor_id', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectPreceptor')));
      $this->setValidator('preceptor_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));
    }

    if ($sf_user->hasCredential('teacher_filter'))
    {
      $this->setWidget('teacher_id', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectTeacher')));
      $this->setValidator('teacher_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));
    }




    $this->setWidget('student', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectStudent')));
    $this->setValidator('student', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));

  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
        'preceptor_id' => "ForeignKey",
        'teacher_id' => "ForeignKey",
        'school_year_id' => "ForeignKey",
        'student' => 'ForeignKey'
      ));

  }

  public function addStudentColumnCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->add(PersonPeer::ID, $value);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
    $criteria->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);

  }

  public function addPreceptorIdColumnCriteria($criteria, $field, $value)
  {
    $criteria->add(PersonPeer::ID, $value);
    $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);

    $criteria->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
    $criteria->addJoin(DivisionPreceptorPeer::DIVISION_ID, DivisionPeer::ID);

  }

  public function addTeacherIdColumnCriteria($criteria, $field, $value)
  {

    $criteria->add(PersonPeer::ID, $value);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $criteria->addJoin(CoursePeer::DIVISION_ID, DivisionPeer::ID);

  }

  public function addSchoolYearIdColumnCriteria($criteria, $field, $value)
  {
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $value);
    $criteria->addJoin(CareerSchoolYearPeer::ID, DivisionPeer::CAREER_SCHOOL_YEAR_ID);

  }

}

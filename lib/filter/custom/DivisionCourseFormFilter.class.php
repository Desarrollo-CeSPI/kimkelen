<?php
class DivisionCourseFormFilter extends CourseFormFilter
{
  public function configure() {
    parent::configure();
    unset($this['starts_at'], $this['quota'], $this['school_year_id'], $this['division_id'], $this['is_closed'], $this['current_period'], $this['related_division_id']);

    $this->getWidget('name')->setOption('with_empty', false);

    $sf_user = sfContext::getInstance()->getUser();

    if ($sf_user->hasCredential('teacher_filter'))
    {
      $this->setWidget('teacher_id', new sfWidgetFormPropelChoice(array('model' => 'Teacher', 'peer_method' => 'doSelectActive', 'add_empty' => true)));
      $this->setValidator('teacher_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Teacher', 'column' => 'id')));
    }
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'teacher_id' =>   "ForeignKey",
    ));
  }

  public function addTeacherIdColumnCriteria($criteria, $field, $value)
  {
    $criteria->add(CourseSubjectTeacherPeer::TEACHER_ID, $value);
    $criteria->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
  }
}

<?php

/**
 * CourseSubjectNonNumericalCalificationsForm
 *
 */
class RevertCourseSubjectNonNumericalCalificationsForm extends sfFormPropel
{
  public function getModelName()
  {
    return 'CourseSubject';
  }

  public function configure()
  {
     sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Javascript'));
    $this->widgetSchema->setNameFormat('revert_course_subject_non_numerical_califications[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $c = new Criteria();
    $c->addjoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getObject()->getId());
    $c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, true);

    $this->setWidget('course_subject_id', new sfWidgetFormInputHidden());
    $this->setValidator('course_subject_id', new sfValidatorNumber());
    $this->setDefault('course_subject_id', $this->getObject()->getId());

    $this->setWidget("student_list", new sfWidgetFormPropelChoiceMany(array(
        'model' => 'Student',
        'add_empty' => false,
        'multiple' => true,
        'peer_method' => 'doSelectActive',
        'renderer_class' => 'csWidgetFormSelectDoubleList',
        'criteria' => $c
      )));

    $this->setValidator("student_list", new sfValidatorPropelChoiceMany(array(
        "model" => "Student",
        "required" => true,
      )));
  }

  protected function doSave($con = null)
  {
    $values = $this->getValues();
    $course_subject = CourseSubjectPeer::retrieveByPk($values['course_subject_id']);
    $course = $course_subject->getCourse();

    $con = (is_null($con)) ? $this->getConnection() : $con;

    try
    {
      $con->beginTransaction();

      foreach ($values['student_list'] as $student_id)
      {
        $course_subject_student = CourseSubjectStudentPeer::retrievebyCourseSubjectAndStudent($course_subject->getid(), $student_id);
        $course_subject_student->setIsNotAverageable(true);
        $course_subject_student_marks = CourseSubjectStudentMarkPeer::retrieveByCourseSubjectStudent($course_subject_student->getId());

        foreach ($course_subject_student_marks as $mark)
        {
          if($mark->getMarkNumber() < $course->getCurrentPeriod())
          {
			  $mark->setIsClosed(true);
		  }
		  else{
			if($mark->getMarkNumber() >= $course->getCurrentPeriod() && !$course->getIsClosed())
			{
				  $mark->setIsClosed(false);
			}
		  }
          $mark->save($con);
        }

		//elimino el student_approved_course_subject y el student_approved_career_subject
		
		$student_approved_course_subject = $course_subject_student->getStudentApprovedCourseSubject();
		$student_approved_career_subject = $student_approved_course_subject->getStudentApprovedCareerSubject();
		
        $student_approved_course_subject->delete($con);
        $student_approved_career_subject->delete($con);
        
        $course_subject_student->setStudentApprovedCourseSubject(null);
        $course_subject_student->setIsNotAverageable(false);
        $course_subject_student->save($con);
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      throw $e;
      $con->rollBack();
    }
  }

}

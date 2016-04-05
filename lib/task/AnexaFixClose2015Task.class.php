<?php

class AnexaFixClose2015Task extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'AnexaFixClose2015';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
    The [AnexaFixClose2015|INFO] task does things.
    Call it with:

    [php symfony AnexaFixClose2015|INFO]
EOF;
  }

  protected  function createContextInstance($application = 'frontend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $con = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    $this->createContextInstance('backend');

    $criteria = new Criteria();
    $criteria->add(CoursePeer::IS_CLOSED, false);
    $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);

    $course_subject_students = CourseSubjectStudentPeer::doSelect($criteria);
    
    $this->log('Cant. Course Subject Students a procesar:');
    $this->log(count($course_subject_students));

    foreach ($course_subject_students as $course_subject_student)
    {

      $course_subject_student_marks = CourseSubjectStudentMarkPeer::retrieveByCourseSubjectStudent($course_subject_student->getId());

      $this->log('Id del course subject student Actual:');
      $this->log($course_subject_student->getId());
   
      foreach ($course_subject_student_marks as $mark)
      {
        $mark->setIsClosed(true);
        $mark->save($con);
      }

      $student_approved_course_subject = new StudentApprovedCourseSubject();
      $student_approved_course_subject->setCourseSubject($course_subject_student->getCourseSubject());

      $student_approved_course_subject->setStudentId($course_subject_student->getStudentId());
      $student_approved_course_subject->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getSchoolYear());

      $student_approved_career_subject = new StudentApprovedCareerSubject();
      $student_approved_career_subject->setStudentId($course_subject_student->getStudentId());
      $student_approved_career_subject->setCareerSubject($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject());
      $student_approved_career_subject->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getSchoolYear());
      $student_approved_career_subject->save($con);

      $student_approved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
      $student_approved_course_subject->save($con);

      $course_subject_student->setStudentApprovedCourseSubject($student_approved_course_subject);
      $course_subject_student->save($con);

    }

    $criteria = new Criteria();
    $criteria->add(CoursePeer::IS_CLOSED, false);
    $courses = CoursePeer::doSelect($criteria);

    foreach ($courses as $c) 
    {
      $c->setIsClosed(true);
      $c->save($con);
    }
  }

}

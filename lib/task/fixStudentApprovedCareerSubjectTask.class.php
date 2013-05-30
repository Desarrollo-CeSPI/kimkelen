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

class fixStudentApprovedCareerSubjectTask extends sfBaseTask
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

    $this->namespace = '';
    $this->name = 'fixStudentApprovedCareerSubject';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [fixStudentApprovedCareerSubject|INFO] task does things.
Call it with:

  [php symfony fixStudentApprovedCareerSubject|INFO]
EOF;

  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $this->createContextInstance('backend');


    try
    {

      $connection->beginTransaction();



      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::SCHOOL_YEAR_ID, 1);
      $student_approved_career_subjects = StudentApprovedCareerSubjectPeer::doSelect($c);
      $arreglados = 0;
      foreach ($student_approved_career_subjects as $student_approved_career_subject)
      {


        if (!($student_approved_career_subject->hasStudentApprovedCourseSubject()
          || $student_approved_career_subject->hasStudentDisapprovedCourseSubject()
          || $student_approved_career_subject->hasStudentRepprovedCourseSubject()))
        {

          $evaluetor = new LvmEvaluatorBehaviour();

          $c = new Criteria();

          $course_subject_student = CourseSubjectStudentPeer::retrieveByStudentApprovedCareerSubject($student_approved_career_subject, SchoolYearPeer::retrieveByPK(1));


          if ($course_subject_student)
          {

            $instanceResult = $evaluetor->getCourseSubjectStudentResult($course_subject_student, $connection);

            if ($instanceResult instanceof StudentApprovedCourseSubject)
            {
              $criteria = new Criteria();
              $criteria->add(StudentApprovedCourseSubjectPeer::STUDENT_ID, $instanceResult->getStudentId());
              $criteria->add(StudentApprovedCourseSubjectPeer::SCHOOL_YEAR_ID, $instanceResult->getSchoolYearId());
              $criteria->add(StudentApprovedCourseSubjectPeer::COURSE_SUBJECT_ID, $instanceResult->getCourseSubjectId());

              if (StudentApprovedCourseSubjectPeer::doCount($criteria))
              {
                $instanceResult = StudentApprovedCourseSubjectPeer::doSelectOne($criteria);
              }
              $instanceResult->setStudentApprovedCareerSubject($student_approved_career_subject);
              $instanceResult->setStudentApprovedCareerSubjectId($student_approved_career_subject->getId());
              $instanceResult->save();

              $this->logSection("arreglado", $arreglados);
              $this->logSection("alumno", $instanceResult->getStudent());
              $this->logSection("careerSubject", $instanceResult->getCareerSubject());
              $this->logSection("StudentApprovedCourseSubject", $instanceResult);
              $this->logSection("StudentApprovedCareerSubject ID: ", $student_approved_career_subject->getId());

              //var_dump($instanceResult);die();

              $arreglados++;
            }
          }
          else
          {
            $this->logSection("No se pudieron ubicar", $student_approved_career_subject->getStudent() . '- ' . $student_approved_career_subject->getCareerSubject() . '' . $student_approved_career_subject->getId());
          }
        }
      }

      $this->logSection("arreglados", $arreglados);
      $connection->commit();




      $connection->beginTransaction();


      $c = new Criteria();
      $c->add(StudentApprovedCourseSubjectPeer::SCHOOL_YEAR_ID, 1);
      $c->add(StudentApprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
      //Se actualizan los cursos aprobados con la materia aprobada, esto existe por que hubo una version que no lo hacia de forma correcta y esto corrige dicho error.
      $update = 0;
      $news = 0;
      $student_approved_course_subjects_null = StudentApprovedCourseSubjectPeer::doSelect($c);


      foreach ($student_approved_course_subjects_null as $student_approved_course_subject)
      {
        $course_subject_student = $student_approved_course_subject->getCourseSubjectStudent();
        if ($course_subject_student)
        {
          $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($course_subject_student->getCourseSubject()->getCareerSubject()->getId(), $student_approved_course_subject->getCourseSubjectStudent()->getStudentId());
        }
        else
        {
          $this->logSection("Cuak!", $student_approved_course_subject->getId(), $student_approved_course_subject);
        }


        $student_approved_career_subject->setSchoolYearId(1);
        $student_approved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);

        $student_approved_career_subject->isNew() ? $news++ : $update++;
        ;

        $student_approved_course_subject->save($connection);


        $student_approved_career_subject->save($connection);
      }
      $this->logSection("Actualizados", $update);
      $this->logSection("Nuevos", $news);


      //Este foreach corrige el error de una version, que creaba tambien el student_approved_course_subject, cuando no es real.
      $c = new Criteria();
      $student_dissapproved_course_subjects = StudentDisapprovedCourseSubjectPeer::doSelect($c);
      $delete = 0;

      foreach ($student_dissapproved_course_subjects as $student_dissapproved_course_subject)
      {
        $course_subject_student = $student_dissapproved_course_subject->getCourseSubjectStudent();
        $c = new Criteria();
        $c->add(StudentApprovedCourseSubjectPeer::STUDENT_ID, $course_subject_student->getStudentId());
        $c->add(StudentApprovedCourseSubjectPeer::COURSE_SUBJECT_ID, $course_subject_student->getCourseSubjectId());

        $student_approved_course_subject = StudentApprovedCourseSubjectPeer::doSelectOne($c);

        if (!is_null($student_approved_course_subject))
        {
          $student_approved_course_subject->delete($connection);
          $delete++;
        }
      }
      $this->logSection("Borrados", $delete);
      $connection->commit();





      /*
        $c = new Criteria();
        $c->add(StudentDisapprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);

        $student_dissapproved_course_subjects = StudentDisapprovedCourseSubjectPeer::doSelect($c);
        foreach ($student_dissapproved_course_subjects as $student_dissapproved_course_subject)
        {
        $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent(
        $student_dissapproved_course_subject->getCourseSubjectStudent()->getCourseSubject()->getCareerSubject()->getId(),
        $student_dissapproved_course_subject->getCourseSubjectStudent()->getStudentId());

        if (!is_null($student_approved_career_subject) && !$student_approved_career_subject->isNew())
        {
        $student_dissapproved_course_subject->setStudentApprovedCareerSubjectId($student_approved_career_subject->getId());
        $student_dissapproved_course_subject->save($connection);
        }
        else
        {
        $student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveCourseSubjectStudent($student_dissapproved_course_subject->getCourseSubjectStudent());

        if (is_null($student_repproved_course_subject))
        {
        die(var_dump($student_dissapproved_course_subject->getId()));
        }
        }
        }
       */


      //$this->fixComissions($connection);
      //$this->fixCourseSubjectStudents($connection);
      //$this->fixStudentCareerSchoolYearStatus($connection);
      //$this->fixRepetidores($connection);
      //$this->fixApproved($connection);
    }
    catch (PropelException $e)
    {
      $connection->rollback();
      throw $e;
    }

    // add your code here

  }

  public function fixApproved($con)
  {
    $c = new Criteria();
    $c->add(StudentApprovedCareerSubjectPeer::MARK, null, Criteria::ISNULL);
    $school_year = SchoolYearPeer::retrieveByPk(1);

    foreach (StudentApprovedCareerSubjectPeer::doSelect($c) as $student_approved_career_subject)
    {
      $course_subject_student = CourseSubjectStudentPeer::retrieveByStudentApprovedCareerSubject($student_approved_career_subject, $school_year);
      $mark = $course_subject_student->getCourseResult()->getMark();
      $student_approved_career_subject->setMark($mark);
      $student_approved_career_subject->save($con);
    }

  }

  public function fixStudentCareerSchoolYearStatus($con)
  {
    $c = new Criteria();
    $c->add(StudentCareerSchoolYearPeer::STATUS, 3);

    foreach (StudentCareerSchoolYearPeer::doSelect($c) as $student_career_school_year)
    {
      $c = new Criteria();
      $c->add(StudentCareerSchoolYearPeer::STATUS, 1);
      $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $student_career_school_year->getStudentId());
      $last_year = StudentCareerSchoolYearPeer::doSelectOne($c);

      if (!is_null($last_year))
      {
        $student_career_school_year->setStatus(0);
        $student_career_school_year->save($con);
      }
    }

  }

  protected function createContextInstance($application = 'frontend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();

  }

  public function fixRepetidores($con)
  {
    $c = new Criteria();
    $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED);
    foreach (StudentCareerSchoolYearPeer::doSelect($c) as $student_career_school_year)
    {

      $c = new Criteria();
      $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, 2);
      $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $student_career_school_year->getStudentId());
      $last_year_student = StudentCareerSchoolYearPeer::doSelectOne($c);

      if ($last_year_student->getStatus() == StudentCareerSchoolYearStatus::APPROVED)
      {
        $year = $student_career_school_year->getYear() + 1;
        $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::IN_COURSE);
        $student_career_school_year->setYear($year);

        $c = new Criteria();
        $c->add(StudentCareerSubjectAllowedPeer::STUDENT_ID, $student_career_school_year->getStudentId());
        StudentCareerSubjectAllowedPeer::doDelete($c);

        $c = new Criteria();
        $c->add(CareerStudentPeer::STUDENT_ID, $student_career_school_year->getStudentId());
        $career_student = CareerStudentPeer::doSelectOne($c);
        $career_student->createStudentsCareerSubjectAlloweds($year, $con);
        $student_career_school_year->save($con);
      }
    }

    /*
      $career_school_year = CareerSchoolYearPeer::retrieveByPk(2);
      $student_career_school_years = StudentCareerSchoolYearPeer::doSelect(StudentCareerSchoolYearPeer::retrieveLastYearRepprovedStudentCriteria($career_school_year));
      foreach ($student_career_school_years  as $student_career_school_year)
      {
      $student = $student_career_school_year->getStudent();

      die(var_dump($student));
      }
     */

  }

  public function fixCourseSubjectStudents($con)
  {
    $c = new Criteria();
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->add(CoursePeer::SCHOOL_YEAR_ID, 1);
    $c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);

    //die(var_dump(count(CourseSubjectStudentPeer::doSelect($c))));
    foreach (CourseSubjectStudentPeer::doSelect($c) as $course_subject_student)
    {
      $result = $course_subject_student->getCourseResult();
      if (is_null($result->getId()))
      {
        $result->save($con);
      }
      if ($result instanceOf StudentApprovedCourseSubject)
      {
        $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($course_subject_student->getCourseSubject()->getCareerSubject()->getId(), $course_subject_student->getStudentId());

        $student_approved_career_subject->setSchoolYearId(1);
        $result->setStudentApprovedCareerSubject($student_approved_career_subject);
        $result->save($con);

        $student_approved_career_subject->save($con);
      }
      unset($result);
    }

  }

  public function fixComissions($connection)
  {
    $c = new Criteria();
    //$c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
    $c->add(CoursePeer::SCHOOL_YEAR_ID, 1);

    foreach (CoursePeer::doSelect($c) as $course)
    {
      $course_subjects = $course->getCourseSubjects();

      foreach ($course->getCourseSubjects() as $course_subject)
      {
        foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student)
        {
          $result = $course_subject_student->getCourseResult();
          if (is_null($result->getStudentApprovedCareerSubjectId()))
          {
            if (is_null($result->getId()))
            {
              $result->save($connection);

              if ($result instanceOf StudentApprovedCourseSubject)
              {
                $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($course_subject_student->getCourseSubject()->getCareerSubject()->getId(), $course_subject_student->getStudentId());

                $student_approved_career_subject->setSchoolYearId(1);
                $student_approved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
                $student_approved_course_subject->save($connection);

                $student_approved_career_subject->save($connection);
              }
            }
          }
        }
      }
    }

  }

}
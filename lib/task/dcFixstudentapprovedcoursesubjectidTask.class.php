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

class dcFixstudentapprovedcoursesubjectidTask extends sfBaseTask
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

    $this->namespace = 'dc';
    $this->name = 'fix-student-approved-course-subject-id';
    $this->briefDescription = 'Este task actualiza los student_approved_course_subject_id que estaban en null y no debian estarlo';
    $this->detailedDescription = <<<EOF
The [dc:fix-student-approved-course-subject-id|INFO] task does things.
Call it with:

  [php symfony dc:fix-student-approved-course-subject-id|INFO]
EOF;

  }

  protected function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();

  }

  public function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $course_subject_student = $course_subject_student_examination->getCourseSubjectStudent();

    // si aprueba la mesa de examen
    if ($course_subject_student_examination->getMark() >= 6)
    {
     
      // se crea el approved course subject
      $sacs = StudentApprovedCourseSubjectPeer::retrieveForCourseSujectStudent($course_subject_student_examination->getCourseSubjectStudent());

      if (is_null($sacs))
      {        
        $result = new StudentApprovedCourseSubject();
        $result->setCourseSubjectId($course_subject_student->getCourseSubjectId());
        $result->setStudentId($course_subject_student->getStudentId());
        $result->setSchoolYearId($course_subject_student_examination->getExaminationSubject()->getExamination()->getSchoolYearId());

        $average = (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);

        $average = sprintf('%.4s', $average);
        // se guarda la NOTA FINAL de la materia
        if ($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYearId() == $this->getCurrentHistoriaDelArte()->getId())
        {
          $average = $course_subject_student_examination->getMark();
        }
        $result->setMark($average);

        $result->save($con);
        $this->closeCourseSubjectStudent($result, $con, $course_subject_student);
      }      
    }
    else
    {

      // TODO: arreglar esto: pedir a la configuración
      // Pasa de diciembre a febrero (se copia el course_subject_student_examination con examination_number + 1)
      $student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveCourseSubjectStudent($course_subject_student);
      if (is_null($student_repproved_course_subject))
      {
        // se crea una previa
        $student_repproved_course_subject = new StudentRepprovedCourseSubject();
        $student_repproved_course_subject->setCourseSubjectStudentId($course_subject_student->getId());
        $student_repproved_course_subject->save($con);
      }
    }

  }

  public function getCurrentHistoriaDelArte()
  {
    return $this->getHistoriaDelArteForSchoolYear(SchoolYearPeer::retrieveCurrent());

  }

  public function getHistoriaDelArteForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, 134);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return CareerSubjectSchoolYearPeer::doSelectOne($c);

  }

  public function closeCourseSubjectStudent($result, PropelPDO $con = null, $course_subject_student)
  {

    if ($result instanceof StudentApprovedCourseSubject)
    {

      $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);
      if (is_null($student_approved_career_subject))
      {

        $student_approved_career_subject = new StudentApprovedCareerSubject();
        $student_approved_career_subject->setCareerSubject($result->getCourseSubject($con)->getCareerSubject($con));

        $student_approved_career_subject->setStudent($result->getStudent($con));
        $student_approved_career_subject->setSchoolYear($result->getSchoolYear($con));
        $student_approved_career_subject->setMark($result->getMark());

        $result->setStudentApprovedCareerSubject($student_approved_career_subject);

        /* para el caso de que se aprueba por mesa de examen, se debe asociar el student_approved_career_subject
         * con el student_disapproved_course_subject
         */
        $disapproved = StudentDisapprovedCourseSubjectPeer::retrieveByStudentApprovedCourseSubject($result, $con);
        if (!is_null($disapproved))
        {
          $disapproved->setStudentApprovedCareerSubject($student_approved_career_subject);
          $disapproved->save($con);
        }
        $student_approved_career_subject->save($con);
        $result->save($con);
      }
    }
    else
    {
      $this->createCourseSubjectStudentExamination($result->getCourseSubjectStudent(null, $con), $con);
    }

  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    $this->createContextInstance();

    $criteria = new Criteria();
    $criteria->addJoin(CourseSubjectStudentPeer::ID, CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID);
    $criteria->add(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNOTNULL);
    $criteria->addAnd(CourseSubjectStudentExaminationPeer::MARK, 6, Criteria::GREATER_EQUAL);
    $criteria->add(CourseSubjectStudentPeer::STUDENT_ID, 728);

    $array_csse = CourseSubjectStudentExaminationPeer::doSelect($criteria);

    $connection->beginTransaction();
    try
    {

      /* @var $csse CourseSubjectStudentExamination */
      foreach ($array_csse as $csse)
      {        

        $sacs = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($csse->getCourseSubjectStudent());
        if (is_null($sacs))
        {          
          $this->closeCourseSubjectStudentExamination($csse, $connection);
        }
      }
      $connection->commit();
    }
    catch (Exception $e)
    {
      $connection->rollback();
      throw $e;
    }

  }

}
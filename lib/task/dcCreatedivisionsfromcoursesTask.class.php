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

class dcCreatedivisionsfromcoursesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      //new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      //new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      // add your own options here
    ));

    $this->namespace        = 'dc';
    $this->name             = 'create-divisions-from-courses';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [dc:create-divisions-from-courses|INFO] task does things.
Call it with:

  [php symfony dc:create-divisions-from-courses|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    // add your code here
    $courses = CoursePeer::doSelect(new Criteria());

    foreach ($courses as $course)
    {
      $division = new Division();
      $division->setName($course->getName());
      $division->setSchoolYearId($course->getSchoolYearId());

      foreach ($course->getCourseSubjects() as $course_subject)
      {
        $course_students = $course->getCourseStudents();
        if (isset($course_students[0]))
        {
          $division->setCareerId($course_students[0]->getCareerSubject()->getCareerId());
        }
        else
        {
          $division->setCareerId(1);
        }
        $new_course = new Course();

        $course->copyInto($new_course);

        $new_course->setName($course_subject->getSubject().' - '.$course->getName());

        $new_course->save();

        $new_course_subject = new CourseSubject();
        $course_subject->copyInto($new_course_subject);
        $new_course_subject->setCourseId($new_course->getId());

        $new_course_subject->setCourseId($new_course->getId());
        $new_course_subject->save();
        $course_subject->delete();

        $c = new Criteria();
        $c->add(CourseStudentPeer::COURSE_ID, $course->getId());
        $c->addJoin(CourseStudentPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
        $c->add(CareerSubjectPeer::SUBJECT_ID, $new_course_subject->getSubjectId());
        $course_students = CourseStudentPeer::doSelect($c);
        foreach ($course_students as $course_student)
        {
          $new_course_student = new CourseStudent();
          $course_student->copyInto($new_course_student);
          $new_course_student->setCourseId($new_course->getId());
          $new_course_student->save();
          $course_student->delete();
        }

        $division_course = new DivisionCourse();
        $division_course->setCourse($new_course);
        $division->addDivisionCourse($division_course);
      }

      try
      {
        $course->delete();
      }
      catch (Exception $e)
      {
      }

      try
      {
        $division->save();
      }
      catch (Exception $e)
      {
      }

      $this->logSection('division+', 'Division '.$division.' created');
    }
  }
}
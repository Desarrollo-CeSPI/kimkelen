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

class promoteAllStudentsTask extends sfBaseTask
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
    $this->name             = 'promoteAllStudents';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [promoteAllStudents|INFO] task does things.
Call it with:

  [php symfony promoteAllStudents|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $con = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    $this->createContextInstance('backend');

    $c = new Criteria();
    $student_career_school_years = StudentCareerSchoolYearPeer::doSelect($c);

    try
    {
      $school_year = SchoolYearPeer::retrieveByPk(3);

      foreach ($student_career_school_years as $student_career_school_year)
      {
        $year = $student_career_school_year->getYear();

        $c = new Criteria();
        $c->add(StudentCareerSubjectAllowedPeer::STUDENT_ID, $student_career_school_year->getStudentId());
        StudentCareerSubjectAllowedPeer::doDelete($c);

        if ($year < 7)
        {
          $year++;

          if ($year == 1 || $year == 4)
          {
            $career = CareerPeer::retrieveByPk(8);
          }
          else
          {
            $career = CareerPeer::retrieveByPk(4);
          }

          $career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career, $school_year);
          
          $c = new Criteria();
          $c->add(CareerStudentPeer::STUDENT_ID, $student_career_school_year->getStudentId());
          $career_student = CareerStudentPeer::doSelectOne($c);
          $career_student->setCareer($career);
          $career_student->setFileNumber($career_student->getFileNumber() + rand());
          $career_student->save($con);
          $career_student->createStudentsCareerSubjectAlloweds($year, $con);

          $new_student_career_school_year = new StudentCareerSchoolYear();
          $new_student_career_school_year->setStudent($student_career_school_year->getStudent());
          $new_student_career_school_year->setCareerSchoolYear($career_school_year);
          $new_student_career_school_year->setYear($year);
          $new_student_career_school_year->save($con);
        }
        else
        {
          $student_career_school_year->delete($con);
        }

      }
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;      
    }
    // add your code here
  }

  protected  function createContextInstance($application = 'frontend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();
  }
}
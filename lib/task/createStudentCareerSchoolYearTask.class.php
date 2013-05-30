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

class createStudentCareerSchoolYearTask extends sfBaseTask
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
    $this->name = 'createStudentCareerSchoolYear';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [createStudentCareerSchoolYear|INFO] task does things.
Call it with:

  [php symfony createStudentCareerSchoolYear|INFO]
EOF;

  }

  protected function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
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
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();


    // add your code here
    $this->createContextInstance();
    $i = 0;
    $students = StudentPeer::doSelect(new Criteria());
    $career_school_year = CareerSchoolYearPeer::retrieveByPK(4);




    foreach ($students as $student)
    {

      $this->logSection("Evaluando student ID = ", $student->getId());
      $career_student = $student->getCareerStudent();
      if ($career_student == null)
      {
        $this->logSection("ceando CarrerStudent", $student->getId());

        $career_student = new CareerStudent();
        $career_student->setStudent($student);
        $career_student->setCareerId(1);
        $career_student->setStartYear(1);
        $career_student->setFileNumber($student->getGlobalFileNumber());
        $career_student->save($connection);

        #$career_student->createStudentsCareerSubjectAlloweds(1, $connection);

        $this->logSection("Fin creacion careerStudent", $career_student->getId());
      }

      $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);

      if ($student_career_school_year == null)
      {
        $i++;
        $this->logSection("Creando studentCareerSchoolYear", $student->getId());
        $student_career_school_year = new StudentCareerSchoolYear();
        $student_career_school_year->setCareerSchoolYear($career_school_year);
        $student_career_school_year->setStudent($student);
        $student_career_school_year->setYear($student->getCareerStudent()->getStartYear());
        $student_career_school_year->save($connection);
        $this->logSection("Fin creacion studentCareerSchoolYear", $career_student->getId());

        $this->logSection("Guardando", $student);

        echo $i;
      }
    }

  }

}
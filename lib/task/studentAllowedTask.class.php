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

class studentAllowedTask extends sfBaseTask
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
    $this->name             = 'studentAllowed';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [studentAllowed|INFO] task does things.
Call it with:

  [php symfony studentAllowed|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    $this->createContextInstance('backend');

    $c = new Criteria();
    //$c->add(CareerStudentPeer::STUDENT_ID, 1080);

    $school_year = SchoolYearPeer::retrieveCurrent();

    try
    {
      $connection->beginTransaction();
      foreach (CareerStudentPeer::doSelect($c) as $career_student)
      {      
        
        $student = $career_student->getStudent();

        $career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career_student->getCareer(), $school_year);
        $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);

        if (is_null($student_career_school_year))
        {
          $this->logSection("Continue",'1');           
          continue;
      
        }

        $year = $student_career_school_year->getYear();

        $c = new Criteria();
        $c->add(CareerSubjectPeer::CAREER_ID, $career_student->getCareerId());
        $c->add(CareerSubjectPeer::YEAR, $year);
        
        foreach (CareerSubjectPeer::doSelect($c) as $career_subject)
        {
          
          if (StudentCareerSubjectAllowedPeer::doCountStudentAndCareerSubject($student, $career_subject) == 0)
          {
            $obj = new StudentCareerSubjectAllowed();
            $obj->setStudentId($student->getId());
            $obj->setCareerSubject($career_subject);
            $obj->save($connection);

            $this->logSection("Allowed agregado: ", $career_subject->getId() );
          }
        }
       }
      $connection->commit();
    }
    catch (PropelException $e)
    {
      $connection->rollback();
      throw $e;
    }
      
  }


  protected  function createContextInstance($application = 'frontend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();
  }

    // add your code here  
}
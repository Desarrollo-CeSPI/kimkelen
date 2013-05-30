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

class lvm2013FixRepprovedStudentsTask extends sfBaseTask
{
  protected function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();
  }

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
    $this->name = 'lvm2013FixRepprovedStudents';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [lvm2013FixRepprovedStudents|INFO] task does things.
Call it with:

  [php symfony lvm2013FixRepprovedStudents|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $this->createContextInstance();

    $c1 = new Criteria();
    $career_school_year = CareerSchoolYearPeer::retrieveBySchoolYear();
    $c1->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year[0]->getId());
    $c1->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED);
    $students_career_school_year = StudentCareerSchoolYearPeer::doSelectJoinCareerSchoolYear($c1);

    foreach ($students_career_school_year as $student_career_school_year) {
     $this->logSection('STUDENT CAREER SCHOOL YEAR ID', $student_career_school_year->getId());
     $previous_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($student_career_school_year->getCareerSchoolYear()->getSchoolYear());
     if (is_null($previous_school_year)){
       $this->logSection('action', 'continue');
       continue;
     }
     $c = new Criteria();
     $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $student_career_school_year->getStudent()->getId());
     $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
     $c->addJoin(CareerSchoolYearPeer::CAREER_ID, CareerPeer::ID, Criteria::INNER_JOIN);

     $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $previous_school_year->getId());
     $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::APPROVED);

     if (StudentCareerSchoolYearPeer::doCount($c)) {
       $this->logSection('action', 'fix');
       $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::IN_COURSE);
       $student_career_school_year->save();
     }
   }
  }

}
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

class fixCourseSubjectStudentExaminationsWithoutExaminationSubjectTask extends sfBaseTask
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
    $this->name             = 'fixCourseSubjectStudentExaminationsWithoutExaminationSubject';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [fixCourseSubjectStudentExaminationsWithoutExaminationSubject|INFO] task does things.
Call it with:

  [php symfony fixCourseSubjectStudentExaminationsWithoutExaminationSubject|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->createContextInstance('backend');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    
    $c = new Criteria();
    $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, null, Criteria::ISNULL);
    
    foreach(CourseSubjectStudentExaminationPeer::doSelect($c) as $csse)
    {
      $csse->setExaminationSubject(ExaminationSubjectPeer::retrieveByCourseSubjectStudentExamination($csse));
      $csse->saveForTask();
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
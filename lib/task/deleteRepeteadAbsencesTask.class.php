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

class deleteRepeteadAbsencesTask extends sfBaseTask
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
    $this->name             = 'deleteRepeteadAbsences';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deleteRepeteadAbsences|INFO] task does things.
Call it with:

  [php symfony deleteRepeteadAbsences|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $c = new Criteria();
    foreach (StudentAttendancePeer::doSelect($c) as $student_attendance)
    {
      $c1 = new Criteria();
      $c1->add(StudentAttendancePeer::ID, $student_attendance->getId(), Criteria::NOT_EQUAL);
      $c1->add(StudentAttendancePeer::STUDENT_ID, $student_attendance->getStudentId());
      $c1->add(StudentAttendancePeer::DAY, $student_attendance->getDay(), Criteria::EQUAL);
      foreach (StudentAttendancePeer::doSelect ($c1) as $delete )
      {
        $delete->delete();
      }
    }     
      
    // add your code here
  }
}
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

class dcFixuppersTask extends sfBaseTask
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
    $this->name             = 'fix-uppers';
    $this->briefDescription = 'Arregla los objetos que antes tenían uppercase en el show y ahora en el save';
    $this->detailedDescription = <<<EOF
The [dc:fix-uppers|INFO] task does things.
Call it with:

  [php symfony dc:fix-uppers|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    // add your code here
    $count = $this->fixUpper("Student");
    $this->logSection("Student", "$count students fixed");
    $count = $this->fixUpper("Teacher");
    $this->logSection("Teacher", "$count teachers fixed");
    $count = $this->fixUpper("Personal");
    $this->logSection("Personal", "$count personnel fixed");
    $count = $this->fixUpper("Subject");
    $this->logSection("Subject", "$count subjects fixed");
    $count = $this->fixUpper("Tutor");
    $this->logSection("Tutor", "$count tutors fixed");
    $count = $this->fixUpper("Career");
    $this->logSection("Career", "$count careers fixed");
  }

  private function fixUpper($class_name)
  {
    $peer_class = $class_name."Peer";
    $objs = call_user_func(array($peer_class, 'doSelect') ,new Criteria());

    $count = 0;
    foreach ($objs as $obj)
    {
      $obj->save();
      $count++;
    }

    return $count;
  }
}
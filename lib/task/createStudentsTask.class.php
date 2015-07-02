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

class createStudentsTask extends sfBaseTask
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
    $this->name             = 'createStudents';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [createStudents|INFO] task does things.
Call it with:

  [php symfony createStudents|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    
    $names = array('Martin', 'Pedro', 'Lucas', 'Agustin', 'Cristian', 'Matias', 'Tomas', 'Cludio', 'Nancy', 'Emilia', 'Alejandra', 'Barbara', 'Luciana', 'Lucia', 'Belen', 'Natalia', 'Adriana', 'Patricio', 'Diego', 'Gonzalo', 'Juan', 'Pablo');
    $last_names = array('Ramirez', 'Rodriguez', 'Cordoba', 'Brown', 'Osorio', 'Diaz', 'Ayesa', 'Ramirez', 'Perez', 'Ripoll', 'Bottini', 'Ponce', 'Casella', 'Martinez', 'Erviti', 'Rodgriguez', 'Gonzalez', 'Fernandez', 'Benitez');
    
    $this->createContextInstance('backend');

    for ($i = 1; $i <= 100; $i++)
    {
      $person = new Person();
      $person->setLastname($last_names[rand(0,18)]);
      $person->setFirstName($names[rand(0,21)]);
      $person->setIdentificationType(1);
      $person->setIdentificationNumber($i);
      $person->setSex(rand(1,2));
      $person->setBirthDate('2000-06-30');

      $student = new Student();
      $student->setGlobalFileNumber($i);
      $student->setPerson($person);

      $person->save();
      $student->save();

      $this->logSection("Person created", $person->__toString());

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
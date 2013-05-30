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

class dcFixCourseStudentsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));

    $this->namespace        = 'dc';
    $this->name             = 'fix-course-students';
    $this->briefDescription = 'Arregla las inscripciones de los alumnos que antes no tenian career_subject_id definido';
    $this->detailedDescription = <<<EOF
The [dc:fix-course-students|INFO] task does things.
Call it with:

  [php symfony dc:fix-course-students|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // Initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();


    echo "Leyendo cursos en la base de datos...[No se puede hacer mas lento :)]\n";
    foreach(CoursePeer::doSelect(new Criteria()) as $course)
    {
      echo "Arreglando curso '".$course."'...\n";

      $students_ids = array();
      foreach($course->getCourseStudents() as $course_student)
      {
          if(!in_array($course_student->getStudentId(),$students_ids))
            $students_ids[] = $course_student->getStudentId();
          $course_student->delete();
      }

      foreach($students_ids as $id)
      {
        if(!CourseStudentPeer::generateInscriptionToCourse($id, $course->getId()))
          echo "Error. Curso con cupo maximo superado o inscripcion ya existente.\n";
      }

    }
  }

}
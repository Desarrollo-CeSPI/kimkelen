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

/**
 * Description of PersonBehavior
 *
 * @author gramirez
 */
class PersonBehavior
{
  /**
   * Check if the person of the object (Tutor, student, teacher, or personal) is the person of another reference. If not then delete the person too.
   *   
   * @param PropelPDO $con
   */
  public function deletePerson($object, PropelPDO $con = null)
  {
    $person = $object->getPerson();

    $count = $person->countPersonals() + $person->countStudents() + $person->countTeachers() + $person->countTutors();

    if ($count == 1)
      $person->delete($con);

  }
}
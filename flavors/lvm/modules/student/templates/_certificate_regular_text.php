<?php /*
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
<?php $division = DivisionPeer::retrieveByStudentCareerSchoolYear($student->getCurrentStudentCareerSchoolYear());?>
<p>
    Las autoridades del <?php echo SchoolBehaviourFactory::getInstance()->getSchoolName() ?> de la Universidad Nacional de La Plata,
    certifican que <b><?php echo $student .' '. $student->getPerson()->getFullIdentification() ?> </b>
    es alumno/a regular de <?php echo $division->getYear() . '° ' . $division->getDivisionTitle()->getName() ?> en el presente ciclo lectivo. 
</p>

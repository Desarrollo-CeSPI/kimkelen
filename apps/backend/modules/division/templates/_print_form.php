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
<table>
  <thead>
    <tr>
      <th>Alumnos</th>
      <?php foreach ($periods as $period): ?>
        <th><?php echo $period->getName(); ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($students as $student): ?>
      <tr>
        <td><?php echo $student; ?></td>
        <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear()); ?>
        <?php foreach ($periods as $period): ?>
          <td><?php echo $form['conduct_' . $student->getId() . '_' . $period->getId()]->render(); ?><?php include_partial('changelog', array('student_career_school_year_conduct' => StudentCareerSchoolYearConductPeer::retrieveOrCreate($student_career_school_year, $period))) ?></td>
          <?php echo $form->renderHiddenFields() ?>
        <?php endforeach; ?>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
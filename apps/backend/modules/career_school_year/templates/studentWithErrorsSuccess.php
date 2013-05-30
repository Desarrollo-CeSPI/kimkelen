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
<?php use_helper('I18N', 'Date', 'Javascript') ?>
<?php include_partial('career_school_year/assets') ?>

<div class ="sf_admin_list">
<table >
  <thead>
    <tr>
      <th class ="sf_admin_text"> Alumno </th>
      <th class ="sf_admin_text"> Materia </th>
      <th class ="sf_admin_text"> Cantidad de inscripciones </th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($result as $student_id=> $student_whith_errors): ?>
      <tr class ="sf_admin_row">
        <td class ="sf_admin_text"><?php echo StudentPeer::retrieveByPK($student_id); ?></td>
        <td class ="sf_admin_text"><?php echo CareerSubjectSchoolYearPeer::retrieveByPK(key($student_whith_errors)); ?></td>
        <td class ="sf_admin_text"><?php echo $student_whith_errors[key($student_whith_errors)]; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
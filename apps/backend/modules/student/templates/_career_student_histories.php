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
<div class="sf_admin_row sf_admin_Text sf_admin_form_field_history">
  <div>
    
    <table>
      <thead>
        <tr>
          <td class="sf_admin_text sf_admin_list_th_person_lastname">Carrera</td>
          <td class="sf_admin_text sf_admin_list_th_person_lastname">Analitico</td>
        </tr>
      </thead>
      <?php foreach ($student->getCareerStudents() as $career_student ):?>
        <tr>
          <td><?php echo $career_student->getCareer()?></td>
          <td><?php echo link_to(__("Analytical"), 'student/analytical?id=' . $career_student->getId()); ?></td>
        </tr>
      <?php endforeach?>
    </table>
  </div>
  <div style="margin-top: 1px; clear:both ;"></div>
</div>
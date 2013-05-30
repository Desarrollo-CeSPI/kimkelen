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
<!--asd-->
<div class="<?php echo ($has_to_show_attendances_per_day)? 'rowcom': 'rowins' ?>">
  <div class="titletable"><?php echo __('Examination repproved') ?></div>
  <table class="lefttable">

    <tr>
      <?php foreach ($examination_repproveds as $examination_repproved): ?>
        <?php if (is_null($examination_repproved->getStudentApprovedCareerSubject())): ?>
          <td><?php echo $examination_repproved->getSubject() ?></td>
          <td><?php echo $examination_repproved->getMarksStr() ? $examination_repproved->getMarksStr() : '-' ?></td>
      <?php endif; ?>
      </tr>
<?php endforeach; ?>
  </table>
</div>
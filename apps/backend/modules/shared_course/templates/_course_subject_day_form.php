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
<?php $weekday_from = $form->getWeekdayFrom(); ?>
<?php $weekday_to = $form->getWeekdayTo(); ?>
<?php for ($i = $weekday_from; $i <= $weekday_to; $i++): ?>
  <td class ="dc_horizontal_column">
    <table class="dc_horizontal_column_table">

        <tr><th><?php echo __($form->getCourseSubjectDayName($i)) ?></th></tr>
        <tr>
          <td>

                <?php for ($blocks = 1; $blocks <= $form->getBlocksPerCourseSubjectDay(); $blocks++): ?>

                  <?php include_partial('shared_course/course_subject_day_block_form', array('form' => $form, 'day' => $i, 'block' => $blocks)); ?>

                <?php endfor; ?>

          </td>
        </tr>
  </table>
  </td>
<?php endfor; ?>
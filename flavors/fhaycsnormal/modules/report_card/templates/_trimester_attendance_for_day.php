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
<div class="kkabsences <?php echo ($has_to_show_repproveds) ? 'rowcom' : 'rowins' ?>">
  <div class="titletable"><?php echo __('Students absences') ?></div>
  <table class="lefttable">
    <tr>
      <th><?php echo __('1°T') ?></th>
      <th><?php echo __('2°T') ?></th>
      <th><?php echo __('3°T') ?></th>
      <th>Total</th>
    </tr>
    <tr>
      <?php $total = 0; ?>
      <?php foreach ($periods as $period): ?>
        <?php $absences = $student->getTotalAbsences($division->getCareerSchoolYearId(), $period, null, false); ?>
        <?php if ($period->getIsClosed()): ?>
          <td><?php echo round($absences, 2) ?></td>
          <?php $total += $absences ?>
        <?php else: ?>
          <td></td>
        <?php endif; ?>

      <?php endforeach; ?>
      <td><?php echo round($total, 2) ?></td>
    </tr>
  </table>
</div>
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
<table cellspacing="0">
  <thead>
    <tr>
      <th colspan="2">
        <?php echo link_to_function(__('Filtros especificados', array(), 'sf_admin'), "$('filters_body').toggle();", array('class' => 'sf_admin_filter_toggle')) ?>
      </th>
    </tr>
  </thead>
  <tbody id="filters_body">
    <tr>
      <th class="th_stats"><?php echo __('School year') ?></th>
      <td><?php echo $school_year ?></td>
    </tr>
    <?php if ($career_school_year != ""): ?>
      <tr>
        <th class="th_stats"><?php echo __('Career') ?></th>
        <td><?php echo $career_school_year ?></td>
      </tr>
    <?php endif; ?>
    <?php if ($year != ""): ?>
      <tr>
        <th class="th_stats"><?php echo __('Career year') ?></th>
        <td><?php echo $year ?></td>
      </tr>
    <?php endif; ?>
    <?php if ($division != ""): ?>
      <tr>
        <th class="th_stats"><?php echo __('Division') ?></th>
        <td><?php echo DivisionPeer::retrieveByPk($division) ?></td>
      </tr>
    <?php endif; ?>
    <?php if ($shift != ""): ?>
      <tr>
        <th class="th_stats"><?php echo __('Shift'). ' (de la división)' ?></th>
        <td><?php echo ShiftPeer::retrieveByPk($shift) ?></td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
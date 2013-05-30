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
<?php use_helper('Javascript') ?>

<div class="sf_admin_filter">
  <?php $filters = $sf_user->getAttribute('student_stats.filters', null, 'admin_module'); ?>
  <table cellspacing="0">
    <thead>
      <tr>
        <th colspan="2">
          <?php echo link_to_function(__('Filtros', array(), 'sf_admin'), "$('sf_admin_filters_body').toggle();", array('class' => 'sf_admin_filter_toggle')) ?>
        </th>
      </tr>
    </thead>
    <tbody id="sf_admin_filters_body">
        <tr>
          <th class="th_stats"><?php echo __('School year') ?></th>
          <?php if (!isset($filters['school_year']) || is_null($filters['school_year'])): ?>
            <td><?php echo CareerSchoolYearPeer::retrieveByPK($filters['career_school_year'])->getSchoolYear() ?></td>
          <?php else: ?>
            <td><?php echo SchoolYearPeer::retrieveByPK($filters['school_year']) ?></td>
          <?php endif; ?>
        </tr>
      <tr>
        <th class="th_stats"><?php echo __('Career school year') ?></th>
        <?php if (!isset($filters['career_school_year']) || is_null($filters['career_school_year'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo CareerSchoolYearPeer::retrieveByPk($filters['career_school_year']) ?></td>
        <?php endif; ?>
      </tr>

      <tr>
        <th class="th_stats"><?php echo __('Career year') ?></th>
        <?php if (!isset($filters['year']) || is_null($filters['year'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo $filters['year'] ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <th class="th_stats"><?php echo __('Division') ?></th>
        <?php if (!isset($filters['division']) || is_null($filters['division'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo DivisionPeer::retrieveByPK($filters['division']) ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <th class="th_stats"><?php echo __('Shift') ?></th>
        <?php if (!isset($filters['shift']) || is_null($filters['shift'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo ShiftPeer::retrieveByPK($filters['shift']) ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <th class="th_stats"><?php echo __('Is graduated') ?></th>
        <?php if (!isset($filters['is_graduated']) || is_null($filters['is_graduated'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo $filters['is_graduated'] ? __('Yes') : 'No' ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <th class="th_stats"><?php echo __('Is entrant') ?></th>
        <?php if (!isset($filters['is_entrant']) || is_null($filters['is_entrant'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo $filters['is_entrant'] ? __('Yes') : 'No' ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <th class="th_stats"><?php echo __('Has disciplinary sanctions') ?></th>
        <?php if (!isset($filters['has_disciplinary_sanctions']) || is_null($filters['has_disciplinary_sanctions'])): ?>
          <td class="td_stats"><?php echo __('Not specified') ?></td>
        <?php else: ?>
          <td><?php echo $filters['has_disciplinary_sanctions'] ? __('Yes') : 'No' ?></td>
        <?php endif; ?>
      </tr>
    </tbody>
  </table>
</div>
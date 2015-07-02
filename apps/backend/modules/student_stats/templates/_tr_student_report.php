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
<?php foreach ($report_array as $key => $item_by_career): ?>
  <tr>
    <td class="item_career" colspan="<?php echo (!(isset($no_details) && $no_details))?'3':'2' ?>"><?php echo __($key) ?></td>
  </tr>
  <?php if (empty($item_by_career)): ?>
    <tr>
      <td colspan="<?php echo (!(isset($no_details) && $no_details))?'3':'2' ?>"><?php echo __('No results for this career') ?></td>
    </tr>
  <?php endif; ?>
  <?php foreach ($item_by_career as $item): ?>
    <tr>
      <td><?php echo $item['title'] ?></td>
      <td><?php echo $item['total'] ?></td>
      <?php $parameters = ""; ?>
      <?php if ($item['filters']): ?>
        <?php foreach ($item['filters'] as $key => $filter): ?>
          <?php $parameters = $parameters . $key . '=' . $filter . '&'; ?>
        <?php endforeach; ?>
        <?php $parameters = substr($parameters, 0, -1); ?>
        <?php if(!(isset($no_details) && $no_details)): ?>
        <td>
          <?php $item['total'] && print link_to(__('Show list that does total'), '@set_student_filters?' . $parameters, array('target' => '_blank')) ?>
        </td>
        <?php endif; ?>
      <?php else: ?>
        <?php if(!(isset($no_details) && $no_details)): ?>
        <td></td>
        <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endforeach ?>
</tr>
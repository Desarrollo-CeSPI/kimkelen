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
<?php use_helper('Date') ?>

<div id='content' style='font-size: 12px;'>
  <div id='sf_admin_container'>
    <?php if(0 == count($objects)):?>
      <div class="notice" style="padding: 20px; background-image: none; margin-bottom: 15px;">
        <?php echo __('The student has no approved subjects')?>
      </div>
    <?php else: ?>
      <?php foreach ($objects as $key => $subjects): ?>

        <table class="analytical">
          <thead>
            <tr>
              <th colspan="6" ><?php echo __('Año: '.$key) ?></th>
            </tr>
            <tr>
              <th><?php echo __("Subject") ?></th>
              <th><?php echo __("Year") ?></th>
              <th><?php echo __("Month") ?></th>
              <th><?php echo __("Result") ?></th>
              <th><?php echo __("Mark") ?></th>
              <th><?php echo __("Approved method") ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($subjects as $subject): ?>
            <tr>
              <td><?php echo $subject->getCareerSubject()->getSubject() ?></td>
              <td><?php echo $subject->getSchoolYear() ?></td>
              <td><?php  // echo format_date($subject->getApprovationDate(), "MMMM") ?></td>
              <td><?php echo $subject->getResult(false) ?></td>
              <td><?php echo $subject->getMark() ?></td>
              <td><?php echo $subject->getMethod() ?></td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
      <?php endforeach ?>
    <?php endif; ?>
  </div>
</div>
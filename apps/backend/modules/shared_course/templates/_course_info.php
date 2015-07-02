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
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_description">
  <div>
    <?php if($course->getDivision()):?>
      <label for="division"> <?php echo __('Division');?> </label>
      <?php echo $course->getDivision();?>
    <?php else: ?>
      <label for="commission"> <?php echo __('Commision');?> </label>
      <?php echo $course->getName();?>
    <?php endif?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_period">
  <div>
    <label for="division"> <?php echo __('Period');?> </label>
    <?php echo $course->getCurrentPeriod();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php if ($course->countTeachers()): ?>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_teachers">
    <div>
      <label for="teachers"> <?php echo __('Teachers');?> </label>
      <ul>
        <?php foreach ($course->getTeachers() as $teacher): ?>
          <li><?php echo $teacher ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <div style="margin-top: 1px; clear: both;"></div>
  </div>
<?php endif ?>
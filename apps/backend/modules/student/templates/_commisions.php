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
<?php use_helper('I18N')?>
<?php /* @var $student Student */ ?>
<?php $commisions = $student->getCommisions()?>
<?php if (count($commisions)): ?>
  <?php echo count($commisions) > 1 ?  __('Currently inscripted in commisions:') : __('Currently inscripted in commision:')?>
  <div class="student_commisions">
    <?php foreach( $commisions as $course):?>
    <div><?php echo link_to($course, 'course/show?id=' . $course->getId())?></div>
    <?php endforeach ?>
  </div>
<?php endif ?>
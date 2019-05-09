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
<?php 
	$commisions = $student->getCommisions();
  $show_commisions = array_slice($commisions, 0, 3);
  $hide_commisions = array_slice($commisions, 3);
?>
<?php use_helper('I18N')?>
<?php /* @var $student Student */ ?>

<?php $sys = $student->getSchoolYearStudentForSchoolYear(); ?>
<?php if(! is_null($sys) && ! $sys->getIsDeleted()):?>
<?php if (count($commisions)): ?>
  <div class="student_year">
  	<?php echo count($commisions) > 1 ?  __('Currently inscripted in commisions:') : __('Currently inscripted in commision:')?>
  </div>
  <div class="student_commisions">

  	<?php foreach( $show_commisions as $course):?>
    	<div style="margin-left: 5px"><?php echo link_to($course, 'course/show?id=' . $course->getId())?></div>
    <?php endforeach ?>

    <?php if (count($hide_commisions)): ?>
    	<?php foreach( $hide_commisions as $course):?>
      	<div class="hide" style="margin-left: 5px; display: none;"><?php echo link_to($course, 'course/show?id=' . $course->getId())?></div>
    	<?php endforeach ?>
    	<a class="toggle-link" href="#" onclick="jQuery(this).closest('.student_commisions').find('.hide').toggle(500);return false">Ver todas</a>
    <?php endif ?>
  </div>
<?php endif ;?>
<?php endif ;?>

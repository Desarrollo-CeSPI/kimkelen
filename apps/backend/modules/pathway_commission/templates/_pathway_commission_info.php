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

<div class='info_div'>
  <strong><?php echo $course->getName() ?></strong>
</div>
<div class='info_div' style="color:red">
  <strong><?php echo $course->getIsClosed() ? __('Closed') : '' ?></strong>
</div>

<?php if ($course->countSubjects()): ?>
  <div class="attribute"><?php echo __('Materia/s') ?>:&nbsp;
    <?php foreach ($course->getCourseSubjects() as $subject): ?>
	    <div style="font-size: 1.2em;">
	    	<?php echo $subject->getCareerSubjectSchoolYear() ?>
	    </div>

      <?php if ($subject->countCourseSubjectDays() > 0): ?>
        <ul style="color:#333; font-weight:normal">
          <?php foreach ($subject->getCourseSubjectDays() as $cd): ?>
            <li>
              <?php
              echo sprintf("%s (%s %s)", __($cd->getDayName()), is_null($cd->getStartsAt()) ? '' : $cd->getTimeRangeString(), $cd->getClassroom())
              ?>
            </li>
          <?php endforeach ?>
        </ul>
      <?php endif ?>  
    <?php endforeach ?>
  </div>
<?php endif ?>

<div class='info_div'>
	<?php foreach ($course->getPathways() as $pathway): ?>
  	<strong><?php echo $pathway ?></strong>
  <?php endforeach ?>
</div>

<?php if ($course->hasAttendanceForSubject() && $course->countCourseSubjectConfigurations() == 0): ?>
	<div class='info_div'><span class="alert"><?php echo __('This course has attendance for subject and may not be properly configurated') ?></span>
	</div>
<?php endif ?>
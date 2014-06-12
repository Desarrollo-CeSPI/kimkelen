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
  <strong><?php echo $course->getName() ?></strong> <em><?php echo $course->getSchoolYear() ?></em>
</div>
<div class='info_div'>
  <strong><?php echo __('Period:') ?></strong> <em><?php echo $course->getCurrentPeriod() ?></em>
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

<div id="course_teachers_text<?php echo $course->getId() ?>">
  <span class="attribute <?php !$course->getActiveTeachers() and print 'disabled' ?>"><?php echo __('Tiene docentes que lo dictan') ?> <?php echo ($course->countActiveTeachers()) ? "(" . $course->countActiveTeachers() . ")" : "" ?></span>

  <?php if ($course->getActiveTeachers()): ?>
    <ul>
      <?php foreach ($course->getTeachers() as $teacher): ?>
        <li><?php echo $teacher ?></li>
      <?php endforeach ?>
    </ul>

    <ul id="course_teachers_<?php echo $course->getId() ?>" style="display: none;" class="more_info">
      <?php foreach ($course->getActiveTeachers() as $t): ?>
        <li><?php echo $t ?></li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>
</div>

<div id="course_students_text<?php echo $course->getId() ?>">
  <?php include_partial('course_students_box', array('course' => $course)) ?>
</div>

<?php if ($course->hasAttendanceForSubject() && $course->countCourseSubjectConfigurations() == 0): ?>
  <div class='info_div'><span class="alert"><?php echo __('This course has attendance for subject and may not be properly configurated') ?></span>
  </div>
  <?php

 endif ?>
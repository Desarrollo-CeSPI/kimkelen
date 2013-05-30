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
  <strong><?php echo $course->getName()?></strong> <em><?php echo $course->getSchoolYear()?></em>
  <div style="color: #00577B"><strong><?php is_null($course->getDivisionId()) and print __('Related commision')?></strong></div>
</div>
<div class='info_div'>
  <strong><?php echo __('Period:')?></strong> <em><?php echo $course->getCurrentPeriod()?></em>
</div>
<div class='info_div' style="color:red">
  <strong><?php echo $course->getIsClosed() ? __('Closed') : ''?></strong>
</div>

<?php if ($course->countTeachers()): ?>
  <span class="attribute <?php ($course->countTeachers()==0) and print 'disabled' ?>"><?php echo __('Tiene docentes ') ?>  <?php echo $course->countTeachers()?></span>
  <ul>
    <?php foreach ($course->getTeachers() as $teacher): ?>
      <li><?php echo $teacher ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>


<?php if ($course->countSubjects()): ?>
  <span class="attribute <?php ($course->countCourseSubjects()==0) and print 'disabled' ?>"><?php echo __('Tiene materias ') ?>  <?php echo $course->countCourseSubjects()?></span>
  <ul>
    <?php foreach ($course->getCourseSubjects() as $course_subject): ?>

      <li><?php echo $course_subject ?>
        <?php if ($course_subject->countCourseSubjectDays() > 0): ?>
        :
          <?php foreach ($course_subject->getCourseSubjectDays() as $cd):?>
            <?php echo sprintf("%s (%s %s)",
            __($cd->getDayName()),
              is_null($cd->getStartsAt())?'':$cd->getTimeRangeString(),
              $cd->getClassroom())?>
          <?php endforeach?>
        <?php endif?>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>

<div class='info_div'>
  <strong><?php echo ($course->countStudents())?__('Students enrolled').': '.$course->countStudents():__('Without students');?> </strong>
</div>
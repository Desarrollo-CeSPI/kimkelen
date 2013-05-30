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
<a class="tab tab-selected" href="#course_info" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Curso</a>
<?php foreach ($course->getCourseSubjects() as $course_subject):?>
  <a class="tab" href="#course_subject_<?php echo $course_subject->getId()?>" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo $course_subject ?></a>
<?php endforeach?>


<fieldset id="course_info">
  <?php echo get_partial('shared_course/course_info', array('type' => 'list', 'course' => $course)) ?>
</fieldset>

<?php foreach ($course->getCourseSubjects() as $course_subject):?>
  <fieldset id="course_subject_<?php echo $course_subject->getId()?>">
    <?php include_partial('shared_course/course_subject_students', array('course_subject'=> $course_subject))?>
  </fieldset>
<?php endforeach ?>


<script type="text/javascript">
  jQuery('fieldset:gt(0)').hide();
</script>
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
<?php if (!is_null($marks)): ?>
  <?php foreach ($marks as $mark): ?>
    <td class="mark <?php echo $mark->getColor() ?>"><?php echo $mark != '' ? $mark : '-' ?></td>
  <?php endforeach; ?>

  <?php $course_subject_student = $mark->getCourseSubjectStudent(); ?>  
  <td class="mark <?php echo $course_subject_student->getAvgColor() ?>"><?php echo $course_subject_student->getMarksAverage() ?></td>
<?php else: ?>
  <?php for ($i = 1; $i <= $marksNumber; $i++): ?>
    <td>N/C</td>
  <?php endfor ?>
  <td>N/C</td>
<?php endif; ?>
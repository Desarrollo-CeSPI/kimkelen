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
<tr>
  <td colspan="<?php echo $configuration->getCourseMarks() + 5?>"></td>  
</tr>

<tr>
  <td colspan="2" style="padding-top: 20px;">
  	Promedios
  </td>

  <?php for ( $i = 1; $i <= $configuration->getCourseMarks(); $i++): ?>
    <td align="center"><?php echo $course_subject->getAverageForMarkNumber($i); ?></td>
  <?php endfor; ?>

  <td align="center"><?php echo $course_subject->getAverageForMarkNumber(); ?></td>  
</tr>

<tr>
  <td colspan="<?php echo $configuration->getCourseMarks() + 5?>"></td>  
</tr>

<tr>
  <td colspan="2" style="padding-top: 20px;">
  	Cantidad de examenes por fecha
  </td>
  <td colspan="<?php echo $configuration->getCourseMarks()?>"></td>
  <td></td>  
  <td align="center"><?php echo $course_subject->countCourseSubjectStudentExaminationForExaminationNumber(1); ?></td>  
  <td align="center"><?php echo $course_subject->countCourseSubjectStudentExaminationForExaminationNumber(2); ?></td>  
  <td align="center"><?php echo $course_subject->countStudentRepprovedCourseSubject(); ?></td>  
  <td></td>
  <td></td>
</tr>

<tr>
  <td colspan="2" style="padding-top: 20px;">
  	Alumnos que deben todavía la materia
  </td>
  <td colspan="<?php echo $configuration->getCourseMarks()?>"></td>
  <td></td>  
  <td align="center"><?php echo $course_subject->countCourseSubjectStudentExaminationApprovedForExaminationNumber(1); ?></td>  
  <td align="center"><?php echo $course_subject->countCourseSubjectStudentExaminationApprovedForExaminationNumber(2); ?></td>  
  <td align="center"><?php echo $course_subject->countStudentRepprovedCourseSubject($approved = false); ?></td>  
  <td></td>
  <td></td>
</tr>
<tr>
  <td colspan="<?php echo $configuration->getCourseMarks() + 5?>"></td>  
</tr>
<tr>
  <td colspan="<?php echo 9 + $configuration->getCourseMarks()?>" style="padding-top: 20px;">
    <?php echo __('Professor signature') ?>: &nbsp;
    <?php foreach (range(1, $configuration->getCourseMarks()) as $number):?>
      <?php echo $number?>___________________&nbsp;
    <?php endforeach; ?>
  </td>
</tr>
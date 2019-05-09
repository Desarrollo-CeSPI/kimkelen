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

<?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration(); ?>
<?php if ($course_subject->getCourse()->getIsClosed()): ?>
    <?php $course_subject_student = CourseSubjectStudentPeer::retrieveByCourseSubjectAndStudent($course_subject->getId(),$student->getId()); ?>  
    <?php $course_result = $course_subject_student->getCourseResult() ?>
    <td class="mark <?php echo $course_subject_student->getAvgColorDisapprovedReport() ?>"><?php echo $course_subject_student->getAverageByConfig($configuration) ?></td>
    <td class="mark <?php echo $course_subject_student->getColorDisapprovedReport(1) ?>" ><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStrByConfig() : '' ?></td>
    <td class="mark <?php echo $course_subject_student->getColorDisapprovedReport(2) ?>"><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStrByConfig() : '' ?></td>
<?php else: ?>
    <td>N/C</td>
    <td>N/C</td>
    <td>N/C</td>
<?php endif; ?>
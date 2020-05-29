<?php /*
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
<?php use_stylesheet('/css/report-card.css') ?>
<?php use_stylesheet('/css/print-report-card.css', '', array('media' => 'print')) ?>
<?php use_helper('Asset', 'I18N') ?>

<div class="non-printable">
   <div><a href="<?php echo url_for($back_url) ?>"><?php echo __('Go back') ?></a></div>
</div>

<?php foreach ($students as $student): ?>
   <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear()) ?>
   <div class="non-printable">
   	<div><a target="_blank" href="<?php echo url_for('report_card/exportObservationsCard')  . '?student_career_school_year_id=' . $student_career_school_year->getId() . '&sf_format=pdf' ?>"><?php echo __('Export') ?></a></div>
   </div>  

  <div class="report-wrapper">
    <?php include_partial('header', array('student' => $student, 'division' => $division, 'career_id' => $career_id, 'school_year' => $student_career_school_year->getSchoolYear(), 'student_career' => CareerStudentPeer::retrieveByCareerAndStudent($career_id, $student->getId()))); ?>
    <div class="report-content">

      <?php if ($student->hasCourseType(CourseType::TRIMESTER, $student_career_school_year)): ?>
        <?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php if ($course_subject_students_attendance_day = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay(CourseType::TRIMESTER, $student_career_school_year)): ?>
          <?php include_partial('course_subject_trimester_observations', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_day, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
        <?php if ($course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::TRIMESTER, $student_career_school_year)): ?>
          <?php include_partial('course_subject_trimester_observations', array('student' => $student, 'course_subject_students' => $course_subject_student_attendance_subject, 'periods' => $periods, 'has_attendance_for_subject' => true, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
      <?php endif; ?>

      <?php if ($student->hasCourseType(CourseType::QUATERLY, $student_career_school_year)): ?>
        <?php $periods = CareerSchoolYearPeriodPeer::getQuaterlyPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php if ($course_subject_students_attendance_day = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay(CourseType::QUATERLY, $student_career_school_year)): ?>

		      <?php $course_subject_students_attendance_day_3 = SchoolBehaviourFactory::getInstance()->divideQuaterlyCourseSubjectStudents($course_subject_students_attendance_day, 3); ?>
		      <?php $course_subject_students_attendance_day_2 =  SchoolBehaviourFactory::getInstance()->divideQuaterlyCourseSubjectStudents($course_subject_students_attendance_day, 2); ?>

          <?php if (count($course_subject_students_attendance_day_3) > 0): ?>
		        <?php include_partial('course_subject_quaterly_observations', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_day_3, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
			    <?php endif; ?>

			    <?php if (count($course_subject_students_attendance_day_2) > 0): ?>
		        <?php include_partial('course_subject_quaterly_2_marks_observations', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_day_2, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
		      <?php endif; ?>

	      <?php endif ?>

        <?php if ($course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::QUATERLY, $student_career_school_year)): ?>

		      <?php $course_subject_students_attendance_subject_3 = SchoolBehaviourFactory::getInstance()->divideQuaterlyCourseSubjectStudents($course_subject_student_attendance_subject, 3); ?>
		      <?php $course_subject_students_attendance_subject_2 =  SchoolBehaviourFactory::getInstance()->divideQuaterlyCourseSubjectStudents($course_subject_student_attendance_subject, 2); ?>


		      <?php if (count($course_subject_students_attendance_subject_3) > 0): ?>
		      <?php include_partial('course_subject_quaterly_observations', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_subject_3, 'periods' => $periods, 'has_attendance_for_subject' => true, 'student_career_school_year' => $student_career_school_year)) ?>
          <?php endif; ?>


		      <?php if (count($course_subject_students_attendance_subject_2) > 0): ?>
			      <?php include_partial('course_subject_quaterly_2_marks_observations', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_subject_2, 'periods' => $periods, 'has_attendance_for_subject' => true, 'student_career_school_year' => $student_career_school_year)) ?>
		      <?php endif; ?>


	      <?php endif ?>




      <?php endif; ?>

      <?php if ($student->hasCourseType(CourseType::BIMESTER, $student_career_school_year)): ?>

        <?php $periods = CareerSchoolYearPeriodPeer::getBimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php include_partial('course_subject_bimester_observations', array('student' => $student, 'periods' => array_chunk($periods, 2), 'division' => $division, 'student_career_school_year' => $student_career_school_year)) ?>

      <?php endif; ?>
         
       <div style="clear:both;"></div>
       

    </div>

  </div>

  <div style="clear:both;"></div>
  <div style="page-break-before: always;"></div>

<?php endforeach; ?>

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

class BbaEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

	const PROMOTION_NOTE = 7;
	const MIN_NOTE = 4;
	const POSTPONED_NOTE = 4;
	const DECEMBER = 1;
	const FEBRUARY = 2;
	const MAX_DISAPPROVED = 2;
	const EXAMINATION_NOTE = 6;
	const MINIMUN_MARK = 0; //nota minima de un examen
	const MAXIMUN_MARK = 10; //nota maxima de un examen

	protected
		$_examination_number = array(
		self::DECEMBER => 'Diciembre',
		self::FEBRUARY => 'Febrero',
	);

	/**
	 * This method returns the marks average of a student.
	 *
	 * @param CourseSubjectStudent $course_subject_student
	 * @return <type>
	 */
	public function getMarksAverage($course_subject_student, PropelPDO $con = null)
	{
		$sum = 0;
		$subject_configuration = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration();
		$tmp_sum = 0;
		$final_mark = 0;
		foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm)
		{
			$sum += $cssm->getMark();
		}

		$avg = (string) ($sum / $course_subject_student->countCourseSubjectStudentMarks());


		$avg = sprintf('%.4s', $avg);


		return $avg;

	}

	#bba solo  se fija en el promedio no en la ultima nota!
	public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
	{
		$minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
		return $average >= $minimum_mark;
	}

	public function getStudentDisapprovedResultStringShort(StudentDisapprovedCourseSubject $student_disapproved_course_subject)
	{
		return sprintf("%01.2f", $student_disapproved_course_subject->getCourseSubjectStudent()->getMarksAverage());
	}

}
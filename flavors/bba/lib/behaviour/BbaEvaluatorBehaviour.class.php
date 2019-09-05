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

	const PATHWAY_PROMOTION_NOTE = 6;
        const CBFE_1       = 9;
        const CBFE_2       = 10;

	protected
		$_examination_number = array(
		self::DECEMBER => 'Diciembre',
		self::FEBRUARY => 'Febrero',
	);
        
        protected $cbfe = array(
            self::CBFE_1,
            self::CBFE_2,
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

	public function getPathwayPromotionNote ()
	{
		return self::PATHWAY_PROMOTION_NOTE;
	}
        
    public function canPrintGraduateCertificate($student)
    {
        if(!is_null($student->getCareerStudent()) && !in_array($student->getCareerStudent()->getCareer()->getId(),$this->cbfe))
        {
            if ($student->getCareerStudent()->getStatus() == CareerStudentStatus::GRADUATE)
            {
                return true;
            }
            else
            {
               //chequeo que esté en 7mo y tenga todas las materias aprobadas.
                $this->student_career_school_years = $student->getStudentCareerSchoolYears();
                $scsy_cursed = $student->getLastStudentCareerSchoolYearCoursed();
                
                if(is_null($scsy_cursed))
                {
                    return false;
                }

                $max_year = $scsy_cursed->getCareerSchoolYear()->getCareer()->getMaxYear();

                if($scsy_cursed->getYear() != $max_year)
                    return false;

                foreach ($this->student_career_school_years as $scsy)
                {
                    if($scsy->getStatus() == StudentCareerSchoolYearStatus::APPROVED || $scsy->getStatus() == StudentCareerSchoolYearStatus::IN_COURSE || $scsy->getStatus() == StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED
                            || $scsy->getStatus() == StudentCareerSchoolYearStatus::FREE || ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN  && 
                             $scsy->getId() == $scsy_cursed->getId())){

                        $career_school_year = $scsy->getCareerSchoolYear();
                        $school_year = $career_school_year->getSchoolYear();

                        $csss = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($career_school_year, $student);
                        foreach ($csss as $css)
                        { 
                            if (is_null($css->getStudentApprovedCareerSubject()) && is_null($css->getStudentApprovedCourseSubject()))
                            {
                                return false;                                                
                            }
                        }
                    }
                }
                return true;
            }
        }

    return false;

    }

}
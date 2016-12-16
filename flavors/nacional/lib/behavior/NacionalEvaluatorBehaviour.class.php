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
/**
 * Copy and rename this class if you want to extend and customize
 */
class NacionalEvaluatorBehaviour extends BaseEvaluatorBehaviour
{
    const BIMESTER_POSTPONED_NOTE = 6;
    /*
     * Returns if a student has approved or not the course subject
     *
     * @param CourseSubjectStudent $course_subject_student
     * @param PropelPDO $con
     *
     * @return Object $object
     */
    public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
    {
        if (CourseType::BIMESTER == $course_subject_student->getCourseSubject()->getCourseType()
            && $course_subject_student->getCourseSubject()->getYear() > 4
            && ($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getIsOption())) {
            $last_mark_value = self::BIMESTER_POSTPONED_NOTE;
        }
        else {
            $last_mark_value = self::POSTPONED_NOTE;
        }
        $correct_last_note = $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() >= $last_mark_value;
        $minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
        return ($average >= $minimum_mark) && $correct_last_note;
    }
    /**
     * This method check the conditions of repetition of a year.
     *
     * @param Student $student
     * @param StudentCareerSchoolYear $student_career_school_year
     * @return boolean
     */
    public function checkRepeationCondition(Student $student, StudentCareerSchoolYear $student_career_school_year)
    {
        //IF the current year is the last year of the career, the students not repeat. OR if the year  = 1
        if ($student_career_school_year->isLastYear() || $student_career_school_year->getYear() == 1)
        {
            return false;
        }
        return parent::checkRepeationCondition($student, $student_career_school_year);
    }
    public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
    {
        if ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote())
        {
            $student_approved_career_subject = new StudentApprovedCareerSubject();
            $student_approved_career_subject->setCareerSubject($student_examination_repproved_subject->getExaminationRepprovedSubject()->getCareerSubject());
            $student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
            $student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());
            if($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == ExaminationRepprovedType::REPPROVED) {
                //Final average is the average of the course_subject_student and the mark of student_examination_repproved_subject
                $average = (string) (($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getMarksAverage() + $student_examination_repproved_subject->getMark()) / 2);
                $average = sprintf('%.4s', $average);
                if ($average < self::MIN_NOTE)
                {
                    $average = self::MIN_NOTE;
                }
                $student_approved_career_subject->setMark($average);
            } else {
                //Final calification is the mark of student_examination_repproved_subject
                $student_approved_career_subject->setMark($student_examination_repproved_subject->getMark());
            }
            $student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
            $student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
            $student_repproved_course_subject->save($con);
            $career = $student_repproved_course_subject->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer();
            ##se corrobora si la previa es la última y del último año, hay que egresarlo
            $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student_repproved_course_subject->getStudent(), $career);
            if ($student_repproved_course_subject->getStudent()->getCurrentOrLastStudentCareerSchoolYear()->getYear() >= CareerPeer::getMaxYear() && $previous == 0)
            {
                $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career->getId(), $student_repproved_course_subject->getStudent()->getId());;
                $career_student->setStatus(CareerStudentStatus::GRADUATE);
                //se guarda el school_year en que termino esta carrera
                $career_student->setGraduationSchoolYearId(SchoolYearPeer::retrieveCurrent()->getId());
                $career_student->save($con);
                //se guarda el estado en el student_career_school_year
                $scsy = $student_repproved_course_subject->getCourseSubjectStudent()->getStudent()->getCurrentOrLastStudentCareerSchoolYear();
                $scsy->setStatus(StudentCareerSchoolYearStatus::APPROVED);
                $scsy->save();
            }
            ##se agrega el campo en student_disapproved_course_subject a el link del resultado final
            $student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);
            $student_approved_career_subject->save($con);
        }
    }
}
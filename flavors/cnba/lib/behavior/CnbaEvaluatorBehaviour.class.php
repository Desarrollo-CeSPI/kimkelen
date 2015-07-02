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
class CnbaEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  const ED_FISICA = 22;
  const MAX_DISAPPROVED = 1;
  const EXAMINATION_NOTE = 4;
  const EXEMPT = 'Ex.';

  public function getExcludeRepprovedSubjects()
  {
    $c = new Criteria();
    $c->add(CareerSubjectPeer::SUBJECT_ID, self::ED_FISICA);
    $c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);

    $result = array();
    foreach (CareerSubjectSchoolYearPeer::doSelect($c) as $career_school_year)
    {
      $result[] = $career_school_year->getId();
    }

    return $result;

  }

  public function setFebruaryApprovedResult(StudentApprovedCareerSubject $result, $average, $examination_mark)
  {
    $result->setMark($examination_mark);

  }
    public function getExaminationNote()
  {
    return self::EXAMINATION_NOTE;

  }

  public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
  {
    $minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
    return $average >= $minimum_mark
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() > self::POSTPONED_NOTE
      && $course_subject_student->hasNotAbsense();
  }

    /**
   * If the student approves the previous, then it creates a student_approved_career_subject for this student
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
  public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
  {
    if ($student_examination_repproved_subject->getMark() >= self::EXAMINATION_NOTE)
    {
      $student_approved_career_subject = new StudentApprovedCareerSubject();
      $student_approved_career_subject->setCareerSubject($student_examination_repproved_subject->getExaminationRepprovedSubject()->getCareerSubject());
      $student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
      $student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

      //Final average is directly student_examination_repproved_subject mark
      $mark = (string) ($student_examination_repproved_subject->getMark());

      $mark = sprintf('%.4s', $mark);
      if ($mark < self::MIN_NOTE)
      {
        $mark = self::MIN_NOTE;
      }
      $student_approved_career_subject->setMark($mark);

      $student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
      $student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
      $student_repproved_course_subject->save($con);


      $student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);

      $student_approved_career_subject->save($con);
    }
  }

  public function getExemptString()
  {
    return self::EXEMPT;
  }
}
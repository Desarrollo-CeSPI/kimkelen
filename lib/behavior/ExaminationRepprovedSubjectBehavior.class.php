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
 * @author pmacadden
 */
class ExaminationRepprovedSubjectBehavior
{
  /**
   * Adds the reference for the ExaminationRepprovedSubject to StudentExaminationRepprovedSubjects.
   * If student is withdrawn then it does not add the reference.
   *
   * @param ExaminationRepprovedSubject $examination_repproved_subject
   * @param PropelPDO $con
   */
  public function updateStudentExaminationRepprovedSubjects($examination_repproved_subject, PropelPDO $con)
  {
    if($examination_repproved_subject->isNew())
    {
      $student_repproved_course_subjects = SchoolBehaviourFactory::getInstance()->getAvailableStudentsForExaminationRepprovedSubject($examination_repproved_subject);

      foreach ($student_repproved_course_subjects as $student_repproved_course_subject)
      {
        $student = $student_repproved_course_subject->getCourseSubjectStudent()->getStudent();
        $scsys = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($student, SchoolYearPeer::retrieveCurrent());
        if ($scsys[0]->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN)
        {
          $student_examination_repproved_subject = new StudentExaminationRepprovedSubject();
          $student_examination_repproved_subject->setStudentRepprovedCourseSubjectId($student_repproved_course_subject->getId());
          $examination_repproved_subject->addStudentExaminationRepprovedSubject($student_examination_repproved_subject);
        }
      }
    }
  }
}
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

class StudentAttendancePeer extends BaseStudentAttendancePeer
{

  static public function retrieveByDateAndStudent($date, Student $student, $course_subject_id, $career_school_year_id = null)
  {
    $c = new Criteria();
    $c->add(self::DAY, $date);
    $c->add(self::STUDENT_ID, $student->getId());

	if(is_null($course_subject_id))
	{	
		$c->add(self::COURSE_SUBJECT_ID, null, Criteria::ISNULL);
	}else{
		$c->add(self::COURSE_SUBJECT_ID, $course_subject_id);
	}
   

    if (!is_null($career_school_year_id))
    {
      $c->add(self::CAREER_SCHOOL_YEAR_ID,$career_school_year_id);
    }

    return self::doSelectOne($c);
  }

  /**
   * This method check if all the student_attendance_ids are for the same student.
   *
   * @param <array> $student_attendance_ids
   * @return boolean
   */
  static public function areAllFromSameStudent($student_attendance_ids)
  {
    $c = new Criteria();
    $c->add(self::ID, $student_attendance_ids, Criteria::IN);
    $c->addJoin(self::STUDENT_ID, StudentPeer::ID);

    return StudentPeer::doCount($c) == 1;

  }

  static public function retrieveOrCreate($student, $course_subject_id = null, $date, $career_school_year_id)
  {
    $student_attendance = self::retrieveByDateAndStudent($date, $student, $course_subject_id,$career_school_year_id);

    if (null != $student_attendance)
    {
      return $student_attendance;
    }
    else
    {
      $student_attendance = new StudentAttendance();
      $student_attendance->setStudent($student);
      $student_attendance->setDay($date);
      $student_attendance->setCourseSubjectId($course_subject_id);
      $student_attendance->setCareerSchoolYearId($career_school_year_id);

      return $student_attendance;
    }

  }

  static public function retrieveBySubject($course_subject_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject_id);

    return self::doSelect($c);

  }

  static public function doCountByCourseSubjectAndStudent($course_subject, $student)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject->getId());
    $c->add(self::STUDENT_ID, $student->getId());

    return self::doCount($c);

  }
  static public function doCountAbsenceByCourseSubjectAndStudent ($course_subject,$student)
  {

    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject->getId());
    $c->add(self::STUDENT_ID, $student->getId());
    $c->add(self::VALUE, 0, Criteria::NOT_EQUAL);
    
    $absences = self::doSelect($c);
    $total = 0;
    foreach ($absences as $a) {
      $total += $a->getValue();
    }
    return $total;
  }

}

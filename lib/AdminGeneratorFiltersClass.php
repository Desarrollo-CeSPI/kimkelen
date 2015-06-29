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
 * AdminGeneratorFiltersClass
 *
 * @author ncuesta
 */
class AdminGeneratorFiltersClass
{

  /**
   * Event listener function that should be registered to
   * 'admin.build_criteria' event in order to add some
   * personalized criteria restrictions.
   *
   * @param sfEvent $event
   * @param Criteria $criteria
   */
  static function applyRestrictions(sfEvent $event, $criteria)
  {
    $user = sfContext::getInstance()->getUser();

    if ($event->getSubject() instanceof schoolyearActions)
    {
      // Restrictions for schoolyear module
      // $criteria->add(...);
    }
    elseif ($event->getSubject() instanceof career_subjectActions)
    {
      // Restrictions for careersubject module
      if ($user->getReferenceFor('career'))
      {
        $criteria->add(CareerSubjectPeer::CAREER_ID, $user->getReferenceFor('career'));
        CareerSubjectPeer::OrderByYearAndName($criteria);
      }
    }
    elseif ($event->getSubject() instanceof career_subject_optionActions)
    {
      // Restrictions for careersubject module
      if ($user->getReferenceFor('career'))
      {
        $criteria->add(CareerSubjectPeer::IS_OPTION, true);
        $criteria->add(CareerSubjectPeer::CAREER_ID, $user->getReferenceFor('career'));
      }
    }
    elseif ($event->getSubject() instanceOf career_school_yearActions)
    {
      if ($school_year_id = $user->getReferenceFor('schoolyear'))
      {
        $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year_id);
      }
    }
    elseif ($event->getSubject() instanceOf career_subject_school_yearActions)
    {
      if ($career_school_year_id = $user->getReferenceFor('career_school_year'))
      {
        $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
        $criteria->add(CareerSubjectPeer::IS_OPTION, false);
        $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);

        CareerSubjectSchoolYearPeer::sorted($criteria);
      }
    }
    elseif ($event->getSubject() instanceOf optional_school_yearActions)
    {
      if ($career_school_year_id = $user->getReferenceFor('career_school_year'))
      {
        $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
        $criteria->add(CareerSubjectPeer::HAS_OPTIONS, true);
        $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
      }
    }
    elseif ($event->getSubject() instanceOf division_courseActions)
    {
      if ($division_id = $user->getReferenceFor('division'))
      {
        $criterion = $criteria->getNewCriterion(CoursePeer::DIVISION_ID, $division_id);
        $criterion->addOr($criteria->getNewCriterion(CoursePeer::RELATED_DIVISION_ID, $division_id));

        $criteria->add($criterion);
      }

      if ($user->isPreceptor())
      {
        self::addCoursePreceptorCriteria($criteria, $user);
      }

      if ($user->isTeacher())
      {
        self::addCourseTeacherCriteria($criteria, $user);
      }

      $criteria->setDistinct();
    }
    elseif ($event->getSubject() instanceOf divisionActions)
    {
      DivisionPeer::sorted($criteria);

      if ($user->isPreceptor())
      {
        self::addDivisionPreceptorCriteria($criteria, $user);
      }
      elseif ($user->isTeacher())
      {
        self::addDivisionTeacherCriteria($criteria, $user);
      }
      elseif ($user->isHeadPreceptor())
      {
        self::addDivisionHeadPersonalCriteria($criteria, $user);
      }
    }
    else if ($event->getSubject() instanceOf shared_studentActions)
    {
      $reference_array = sfContext::getInstance()->getUser()->getReferenceFor("shared_student");

      $peer = $reference_array["peer"];
      $fk = $reference_array["fk"];

      if (isset($reference_array["object_id"]))
        $object_id = $reference_array["object_id"];
      else
        $object_ids = $reference_array["object_ids"];

      $criteria->addJoin(constant("$peer::STUDENT_ID"), StudentPeer::ID);
      $criteria->addGroupByColumn(StudentPeer::ID);

      if (isset($object_id))
        $criteria->add(constant("$peer::$fk"), $object_id);
      else
        $criteria->add(constant("$peer::$fk"), $object_ids, Criteria::IN);
      $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
      $criteria->add(PersonPeer::IS_ACTIVE,true);

    }
    else if (($event->getSubject() instanceOf examinationActions) || ($event->getSubject() instanceOf manual_examinationActions))
    {
      $school_year_id = sfContext::getInstance()->getUser()->getReferenceFor("schoolyear");

      $criteria->add(ExaminationPeer::SCHOOL_YEAR_ID, $school_year_id);

      if ($user->isTeacher())
      {
        $criteria->addJoin(ExaminationPeer::ID, ExaminationSubjectPeer::EXAMINATION_ID);
        $criteria->addJoin(ExaminationSubjectPeer::ID, ExaminationSubjectTeacherPeer::EXAMINATION_SUBJECT_ID);
        $criteria->addJoin(ExaminationSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    else if ($event->getSubject() instanceOf examination_subjectActions)
    {
      $examination_id = sfContext::getInstance()->getUser()->getReferenceFor("examination");

      $criteria->add(ExaminationSubjectPeer::EXAMINATION_ID, $examination_id);

      if ($user->isTeacher())
      {
        $criteria->addJoin(ExaminationSubjectPeer::ID, ExaminationSubjectTeacherPeer::EXAMINATION_SUBJECT_ID);
        $criteria->addJoin(ExaminationSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    else if ($event->getSubject() instanceOf manual_examination_subjectActions)
    {
      $examination_id = sfContext::getInstance()->getUser()->getReferenceFor("manual_examination");

      $criteria->add(ExaminationSubjectPeer::EXAMINATION_ID, $examination_id);

      if ($user->isTeacher())
      {
        $criteria->addJoin(ExaminationSubjectPeer::ID, ExaminationSubjectTeacherPeer::EXAMINATION_SUBJECT_ID);
        $criteria->addJoin(ExaminationSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    elseif ($event->getSubject() instanceOf examination_repprovedActions)
    {
      $school_year_id = sfContext::getInstance()->getUser()->getReferenceFor("schoolyear");

      $criteria->add(ExaminationRepprovedPeer::SCHOOL_YEAR_ID, $school_year_id);

      if ($user->isTeacher())
      {
        $criteria->addJoin(ExaminationRepprovedPeer::ID, ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID);
        $criteria->addJoin(ExaminationRepprovedSubjectPeer::ID, ExaminationRepprovedSubjectTeacherPeer::EXAMINATION_REPPROVED_SUBJECT_ID);
        $criteria->addJoin(ExaminationRepprovedSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    elseif ($event->getSubject() instanceOf examination_repproved_subjectActions)
    {
      $examination_repproved_id = sfContext::getInstance()->getUser()->getReferenceFor("examination_repproved");

      $criteria->add(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, $examination_repproved_id);

      ExaminationRepprovedSubjectPeer::sortedBySubject($criteria);

      if ($user->isTeacher())
      {
        $criteria->addJoin(ExaminationRepprovedSubjectPeer::ID, ExaminationRepprovedSubjectTeacherPeer::EXAMINATION_REPPROVED_SUBJECT_ID);
        $criteria->addJoin(ExaminationRepprovedSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    else if ($event->getSubject() instanceOf courseActions)
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
      $criteria->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
      $criteria->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());

      if ($user->isPreceptor())
      {
        PersonalPeer::joinWithCourse($criteria, $user->getGuardUser()->getId());
      }

      if ($user->isTeacher())
      {
        $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
        $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
        $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
        $criteria->setDistinct();
      }
    }
    else if ($event->getSubject() instanceOf commissionActions)
    {
      /*
      $school_year = SchoolYearPeer::retrieveCurrent();
      $criteria->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
      */
      CoursePeer::sorted($criteria);
      $criteria->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);

      if ($user->isPreceptor())
      {
        PersonalPeer::joinWithCourse($criteria, $user->getGuardUser()->getId());
      }
      elseif ($user->isTeacher())
      {
        TeacherPeer::joinWithCourses($criteria, $user->getGuardUser()->getId(), true);
      }
      if ($user->isHeadPreceptor())
      {
        self::addCommissiionHeadPreceptorCriteria($criteria, $user);
      }
    }
    else if ($event->getSubject() instanceOf final_examinationActions)
    {
      $school_year_id = sfContext::getInstance()->getUser()->getReferenceFor("schoolyear");
      $criteria->add(FinalExaminationPeer::SCHOOL_YEAR_ID, $school_year_id);
    }
    else if ($event->getSubject() instanceOf final_examination_subjectActions)
    {
      $final_examination_id = sfContext::getInstance()->getUser()->getReferenceFor("final_examination");

      $criteria->add(FinalExaminationSubjectPeer::FINAL_EXAMINATION_ID, $final_examination_id);

      if ($user->isTeacher())
      {
        $criteria->addJoin(FinalExaminationSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
        $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
        $criteria->add(PersonPeer::IS_ACTIVE,true);
        $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());
      }
    }
    else if ($event->getSubject() instanceOf equivalenceActions)
    {
      $student_id = sfContext::getInstance()->getUser()->getReferenceFor("student");
      $criteria->add(StudentCareerSchoolYearPeer::STUDENT_ID, $student_id);
    }
    else if ($event->getSubject() instanceOf sub_orientationActions)
    {
      $orientation_id = sfContext::getInstance()->getUser()->getReferenceFor("orientation");
      $criteria->add(SubOrientationPeer::ORIENTATION_ID, $orientation_id);
    }
    else if ($event->getSubject() instanceOf student_reincorporationActions)
    {
      $student_id = sfContext::getInstance()->getUser()->getReferenceFor("student");

      if (is_null($student_id))
      {
        $student_id = $user->getAttribute('student_id');
      }

      $criteria->add(StudentReincorporationPeer::STUDENT_ID, $student_id);
      $criteria->addJoin(StudentReincorporationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, CareerSchoolYearPeriodPeer::ID);
      $criteria->addJoin(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    }
    elseif ($event->getSubject() instanceof shared_course_subjectActions)
    {
      $teacher_id = sfContext::getInstance()->getUser()->getReferenceFor("teacher");
      $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
      $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, $teacher_id);
      $criteria->setDistinct();
    }
    elseif ($event->getSubject() instanceof personalActions)
    {
      $criteria->add(PersonalPeer::PERSONAL_TYPE, PersonalType::PRECEPTOR);
    }
    elseif ($event->getSubject() instanceof head_personalActions)
    {
      $criteria->add(PersonalPeer::PERSONAL_TYPE, PersonalType::HEAD_PRECEPTOR);
    }
    elseif ($event->getSubject() instanceof student_officeActions)
    {
      $criteria->add(PersonalPeer::PERSONAL_TYPE, PersonalType::STUDENTS_OFFICE);
    }
    elseif ($event->getSubject() instanceof studentActions)
    {
      if ($user->isPreceptor())
      {
        SchoolBehaviourFactory::getInstance()->joinPreceptorWithStudents($criteria, $user->getGuardUser()->getId());
      }
      elseif ($user->isTeacher())
      {
        TeacherPeer::joinWithStudents($criteria, $user->getGuardUser()->getId());
      }
      if ($user->isHeadPreceptor())
      {
        $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
        $criteria->addJoin(DivisionStudentPeer::DIVISION_ID, DivisionPeer::ID);
        self::addDivisionHeadPersonalCriteria($criteria, $user);
      }
    }
    elseif ($event->getSubject() instanceof licenseActions)
    {
      if (!is_null(sfContext::getInstance()->getUser()->getReferenceFor("teacher")))
      {
        $person_id = TeacherPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("teacher"))->getPersonId();
      }
      else
      {
        $person_id = PersonalPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("personal"))->getPersonId();
      }
      $criteria->add(LicensePeer::PERSON_ID, $person_id);
    }
    elseif ($event->getSubject() instanceof teacherActions)
    {
      $criteria->setDistinct();
    }
    else if ($event->getSubject() instanceOf career_school_year_periodActions)
    {
      $career_school_year_id = sfContext::getInstance()->getUser()->getReferenceFor("career_school_year");
      $criteria->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    }
    else if ($event->getSubject() instanceOf student_freeActions)
    {
      $student_id = sfContext::getInstance()->getUser()->getReferenceFor("student");

      if (is_null($student_id))
      {
        $student_id = $user->getAttribute('student_id');
      }

      $criteria->add(StudentFreePeer::STUDENT_ID, $student_id);
    }
    else if ($event->getSubject() instanceOf course_subject_student_examinationActions)
    {
      $examination_subject_id = sfContext::getInstance()->getUser()->getReferenceFor("examination_subject");

      $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $examination_subject_id);
    }
    else if ($event->getSubject() instanceOf student_examination_repproved_subjectActions)
    {
      $examination_repproved_subject_id = sfContext::getInstance()->getUser()->getReferenceFor("examination_repproved_subject");

      $criteria->add(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, $examination_repproved_subject_id);
    }

    return $criteria;

  }

  public static function addCoursePreceptorCriteria(Criteria $criteria, $user)
  {
    $criteria->addJoin(DivisionPeer::ID, DivisionPreceptorPeer::DIVISION_ID);
    $criteria->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
    $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::IS_ACTIVE,true);
    $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());

  }

  public static function addCourseTeacherCriteria(Criteria $criteria, $user)
  {
    $criteria->addJoin(DivisionPeer::ID, CoursePeer::DIVISION_ID);
    TeacherPeer::joinWithCourses($criteria, $user->getGuardUser()->getId());

  }

  public static function addDivisionPreceptorCriteria(Criteria $criteria, $user)
  {
    $criteria->addJoin(DivisionPeer::ID, DivisionPreceptorPeer::DIVISION_ID);
    $criteria->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
    $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::IS_ACTIVE,true);
    $criteria->add(PersonPeer::USER_ID, $user->getGuardUser()->getId());

  }

  public static function addDivisionTeacherCriteria(Criteria $criteria, $user)
  {
    TeacherPeer::joinWithDivisions($criteria, $user->getGuardUser()->getId());

  }

  public static function addDivisionHeadPersonalCriteria(Criteria $criteria, $user)
  {
    $personal_in = $user->getPersonalIds();
    $criteria->add(PersonalPeer::ID, $personal_in, Criteria::IN);
    $criteria->addJoin(PersonalPeer::ID, DivisionPreceptorPeer::PRECEPTOR_ID);
    $criteria->addJoin(DivisionPreceptorPeer::DIVISION_ID, DivisionPeer::ID);
    $criteria->setDistinct();

  }

  public static function addCommissiionHeadPreceptorCriteria($criteria, $user)
  {
    $personal_in = $user->getPersonalIds();

    $criteria->add(CoursePreceptorPeer::PRECEPTOR_ID, $personal_in, Criteria::IN);
    $criteria->addJoin(CoursePreceptorPeer::COURSE_ID, CoursePeer::ID);
    $criteria->setDistinct();

  }

}

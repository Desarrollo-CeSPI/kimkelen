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
class LvmSchoolBehaviour extends BaseSchoolBehaviour
{
	protected $school_name = "Liceo Víctor Mercante";
        protected $araucano_code = 3185;
        protected $letter = "L";


  public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState', 'label' => 'Cambiar vigencia', 'credentials' => array(0 => 'edit_school_year',),),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array(0 => 'show_school_year',), 'label' => 'Registered students',),
      'careers' => array('action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' => array(0 => 'show_career',),),
      'examinations' => array('action' => 'examinations', 'label' => 'Examinations', 'credentials' => array(0 => 'show_examination',), 'condition' => 'canExamination',),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved',), 'condition' => 'canExamination',),
      '_delete' => array('credentials' => array(0 => 'edit_school_year',), 'condition' => 'canBeDeleted',),
    );

  }

  /**
   * Get every student that isnt inscripted in other division.
   * The inscription depends on the aproval method implemented by each school
   *
   * @param  Division     $division
   *
   * @return array Student[]
   */
  public function getAvailableStudentsForDivision(Division $division)
  {
    $students_in = array();
    foreach ($division->getCourses() as $course)
    {
      foreach ($course->getNonOptionCourseSubjects() as $course_subject)
      {
        $criteria_course = $this->getAvailableStudentsForCourseSubjectCriteria($course_subject);
        $criteria_course->clearSelectColumns();
        $criteria_course->addSelectColumn(StudentPeer::ID);
        $stmt = StudentPeer::doSelectStmt($criteria_course);
        $students_in = array_merge($stmt->fetchAll(PDO::FETCH_COLUMN), $students_in);
      }
    }
    $c = new Criteria();
    //$c->addAnd(StudentPeer::ID,$not_in,Criteria::NOT_IN);
    $c->add(StudentPeer::ID, $students_in, Criteria::IN);

    return StudentPeer::doSelect($c);

  }

  /*
   * Rounds absences up and down if decimal part is greater than 90 or if it is less than 10, respectively.
   */

  private function roundAbsences($total)
  {
    if ($total == null || false === strpos($total, '.'))
    {
      $array = array(0, 0);
    }
    else
    {
      $array = explode(".", $total);
    }

    $decimal = $array[1];

    if ($decimal > 90)
    {
      $total = round($total, 0);
    }
    elseif ($decimal < 10)
    {
      $total = $array[0];
    }

    return $total;

  }

  /**
   * This methods returns the students available four a course that have a Division
   *
   * @see getAvailableStudentsForCourseSubjectCriteria
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   * @return Criteria
   */
  public function getAvailableStudentsForDivisionCourseSubject(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = false)
  {
    $filter_by_orientation = false;
    return StudentPeer::doSelect($this->getAvailableStudentsForDivisionCourseSubjectCriteria($course_subject, $criteria, $filter_by_orientation));

  }

  public function getCareerSubjectSchoolYearSpecialIds($year)
  {
    if ($year == 4)
    {
      return array(205,206,207);
    }
    elseif ($year == 6)
    {
      return array(LvmEvaluatorBehaviour::HISTORIA_DEL_ARTE);
    }

    return array(0);
  }

  public function getCourseSubjectStudentsForCourseType($student ,$course_type , $school_year = null)
  {
    $not_in = SchoolBehaviourFactory::getEvaluatorInstance()->getLvmSpecialSubjectIds($school_year);

    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $not_in, Criteria::NOT_IN);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($c);

    $course_subject_students = $student->getCourseSubjectStudents($c);

    $results = array();

    foreach ($course_subject_students as $css)
    {
      if ($css->getCourseSubject()->getCourseType() == $course_type)
      {
        $results[] = $css;
      }

    }

    return $results;
  }

  public function showReportCardRepproveds()
  {
    return true;
  }

  protected function getClassSubjectStudentAnalytic(){

    return 'LvmSubjectStudentAnalytic';

  }
  
  public function getCourseSubjectStudentsForAnalytics($student, $school_year, $student_career_school_year)
  {
    $introduccion=null;
    $ret = array();
    /*Trimester*/
    foreach ($student->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay(CourseType::TRIMESTER,$student_career_school_year) as $css){
      $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
    }
    
     if ($student_career_school_year->getYear() == 4)
     {
        $introduccion = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentsForIntroduccion($student, $student_career_school_year->getCareerSchoolYear());
        if(count($introduccion) > 0)
        {
            $ret[] = $this->getInstanceSubjectStudentAnalytic($introduccion[0],$school_year);
        }
        
     }else{
         $course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::TRIMESTER,$student_career_school_year);
        foreach ($course_subject_student_attendance_subject as $css){
            $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
        }
    }
    /*Quaterly*/
    
    foreach ($this->getCourseSubjectStudentsForCourseType($student, CourseType::QUATERLY,$school_year) as $css){
      $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
    }
    
    foreach ($this->getCourseSubjectStudentsForCourseType($student, CourseType::QUATERLY_OF_A_TERM,$school_year) as $css){
      $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
    }
   
    if ($student->hasCourseType(CourseType::BIMESTER, $student_career_school_year))
    {
        $course_subject_students_first_q = $student->getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year);
        
        foreach ($course_subject_students_first_q as $css){
            $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
        }
        
        $course_subject_students_second_q = $student->getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year);
        foreach ($course_subject_students_second_q as $css){
            $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
        }
    }
    
    $course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::TRIMESTER,$student_career_school_year);
    if(!is_null($introduccion))
        $course_subject_student_attendance_subject = array_udiff($course_subject_student_attendance_subject, $introduccion, array('CourseSubjectStudent', 'compare'));
    
    if ($student_career_school_year->getYear() == 4)
    {
    foreach ($course_subject_student_attendance_subject as $css){
      $ret[] = $this->getInstanceSubjectStudentAnalytic($css,$school_year);
    }
  }
    
    return $ret;
  }
  
  public function isFreeStudent(Student $student, CareerSchoolYearPeriod $career_school_year_period = null, CourseSubject $course_subject = null, CareerSchoolYear $career_school_year)
  {
    $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);

    $c = new Criteria();
    $c->add(StudentFreePeer::STUDENT_ID, $student_career_school_year->getStudentId());
    $c->add(StudentFreePeer::IS_FREE, true);
    $c->add(StudentFreePeer::CAREER_SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYearId());

    if ( !is_null($career_school_year_period) && !is_null($career_school_year_period->getMaxAbsences()))
    { 
      $c->add(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $career_school_year_period->getId());  
    }    

    if (!is_null($course_subject))
    {
      $c->add(StudentFreePeer::COURSE_SUBJECT_ID, $course_subject->getId());
    }else
    {
        $c->add(StudentFreePeer::COURSE_SUBJECT_ID, Criteria::ISNULL);
    }

    $student_free = StudentFreePeer::doSelectOne($c);
    
    
    return is_null($student_free) ? false : $student_free->getIsFree();
  }
  
  public function getCourseSubjectStudentsForCourseTypeArray($student, $course_type = null, $school_year = null)
  {
    if (is_null($school_year))
    {	
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($c);
	
    return $student->getCourseSubjectStudents($c);

  }

}
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
class NacionalSchoolBehaviour extends BaseSchoolBehaviour
{
  protected $school_name = "Colegio Nacional Rafael Hernández";
  protected $MINUTES_FOR_SCHEMA = array(0 => "00", 5 => "5",  10 => "10", 15 => "15", 20 => "20", 25=>"25", 30 => "30", 35 => "35", 40 => "40", 45 => "45", 50 => "50", 55 => "55");
  protected $araucano_code = 3230;
  protected $letter = "N";

  public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState',  'label' => 'Cambiar vigencia',  'credentials' =>   array( 0 => 'edit_school_year' ,), ),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array( 0 => 'show_school_year', ), 'label' => 'Registered students',  ),
      'careers' => array( 'action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' =>  array( 0 => 'show_career', ),),
      'examinations' => array( 'action' => 'examinations', 'label' => 'Examinations', 'credentials' =>  array( 0 => 'show_examination',),'condition' => 'canExamination',  ),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved', ),'condition' => 'canExamination', ),
      '_delete' => array('credentials' => array( 0 => 'edit_school_year',),'condition' => 'canBeDeleted',),
    );
  }

  public function getFileNumberIsGlobal()
  {
    return true;
  }

  protected function getClassSubjectStudentAnalytic()
  {
    return 'NacionalSubjectStudentAnalytic';
  }
  
  public function getAvailableStudentsForExaminationRepprovedSubject(ExaminationRepprovedSubject $examination_repproved_subject, $is_new=null)
  {
      if(!is_null($is_new) && !$is_new)
      {
        $c = new Criteria();
        $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
        $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
        $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
        $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
        $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $examination_repproved_subject->getCareerSubjectId());

        if($examination_repproved_subject->getExaminationRepproved()->getExaminationType() == ExaminationRepprovedType::FREE_GRADUATED)
        {    
            $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, CourseSubjectStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
            $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::FREE);           
        }
        else
        {
            $free_criteria = new Criteria();
            $free_criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
            $free_criteria->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::FREE);
            $free_criteria->clearSelectColumns();
            $free_criteria->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
            $stmt_f = StudentCareerSchoolYearPeer::doSelectStmt($free_criteria);
            $not_in_free = $stmt_f->fetchAll(PDO::FETCH_COLUMN);

            $c->add(CourseSubjectStudentPeer::STUDENT_ID, $not_in_free, Criteria::NOT_IN);

        }
        return StudentRepprovedCourseSubjectPeer::doSelect($c);
      }
    }
    
    public function getStudentsForDivision($c,$division)
    {
        $ret = array();

        $c =($c == null) ? new Criteria: $c ; 
        $c->addJoin(DivisionStudentPeer::STUDENT_ID,  StudentPeer::ID);
        $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID, Criteria::INNER_JOIN);
        $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        $c->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);

        foreach ($division->getDivisionStudents($c) as $ds)
        {
          if ($ds->getStudent()->getPerson()->getIsActive())
           {
              $ret[] = $ds->getStudent();
           }

        }
        return $ret;
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

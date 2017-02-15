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

class CareerSchoolYear extends BaseCareerSchoolYear
{

  public function showStudentCourseError()
  {
//    $criteria = new Criteria();
//    $criteria->add(StudentPeer::ID, " (
//     select count(*) cant
//     from student s
//     inner join course_subject_student css on css.student_id = s.id
//     inner join course_subject cs on cs.id = css.course_subject_id
//     inner join career_subject_school_year cssy on cssy.id = cs.career_subject_school_year_id
//     where s.id =" .  StudentPeer::ID . "
//     group by cssy.id, s.id
//     having cant > 1)", Criteria::CUSTOM);
//
//    return StudentPeer::doSelect($criteria);

  }

  /**
   * This method try to find if there are new CareerSubjects for our Career, recently
   * created but without a CareerSubjectSchoolYear created
   *
   */
  public function checkCareerSubjectOptionsIntegrity()
  {

    $criteria = new Criteria();
    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $criteria->addAnd(CareerSchoolYearPeer::CAREER_ID, $this->getCareerId());

    $all_career_subjects_sy = array_map(create_function('$cs', 'return $cs->getCareerSubjectId();'), CareerSubjectSchoolYearPeer::doSelect($criteria));
    $criteria->clear();
    $criteria->addAnd(CareerSubjectPeer::CAREER_ID, $this->getCareerId());
    $criteria->addAnd(CareerSubjectPeer::ID, $all_career_subjects_sy, Criteria::NOT_IN);
    $con = Propel::getConnection(SchoolYearPeer::DATABASE_NAME);
    try
    {
      foreach (CareerSubjectPeer::doSelect($criteria) as $career_subject)
      {
        $career_subject_school_year = new CareerSubjectSchoolYear();
        $career_subject_school_year->setCareerSchoolYear($this);
        $career_subject_school_year->setCareerSubject($career_subject);
        $career_subject_school_year->save($con);
      }
      $con->commit();
    }
    catch (PropelPDOException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function __toString()
  {
    return $this->getCareer() . ' | ' . $this->getSchoolYear();

  }

  /**
   * The career school year can be edited if havent been processed.
   *
   * @param PropelPDO $con
   * @return Boolean
   */
  public function canBeEdited(PropelPDO $con = null)
  {
    if (!$this->getSchoolYear()->getIsActive())
      return false;

    if ($this->getIsProcessed())
      return false;

    return $this->countStudentCareerSchoolYears() == 0;

  }

  public function canShowConfiguration()
  {
    return !$this->canBeEdited();
  }

  public function canBeDeleted(PropelPDO $con = null)
  {
    foreach ($this->getCareerSubjectSchoolYears() as $cs)
    {
      /* @var $cs CareerSubjectSchoolYear */
      foreach ($cs->getCourseSubjects() as $c)
      {
        /* @var $c Course */
        if (!$c->canBeDeleted($con))
          return false;
      }
    }
    return true;

  }

  /**
   * This function returns an array of career subject objects that are in $year
   *
   * @param  int   $year Represents a school year
   *
   * @return array Career subject objects
   */
  public function getCareerSubjectForYear($year, $exclude_option = false)
  {
    $c = new Criteria();
    CareerSubjectPeer::sorted($c);
    $c->add(CareerSubjectPeer::YEAR, $year);

    if ($exclude_option)
    {
      $c->add(CareerSubjectPeer::IS_OPTION, false);
    }

    return $this->getCareerSubjectSchoolYearsJoinCareerSubject($c);

  }

  /*
   * This method returns an array of students inscripted in the career and in the school_year
   *
   * @param PropelPDO $con
   * @return array Students[]
   */

  public function getStudents(Criteria $c = null, PropelPDO $con = null)
  {
    if ($c == null)
    {
      $c = new Criteria();
    }
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $this->getId());
    $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);

    return StudentPeer::doSelect($c, $con);

  }

  /**
   * This Function returns true if the school year is active and all the courses are closed.
   *
   *  @return boolean
   */
  public function canClose(PropelPDO $con = null)
  {
    if ($this->getIsProcessed()) return false;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::ID, $this->getId());
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $this->getSchoolYear()->getId());
    $courses_actives = CoursePeer::doCount($c);

    if ($courses_actives == 0)
    {
      return false;
    }

    $c->add(CoursePeer::IS_CLOSED, false);

    return CoursePeer::doCount($c) == 0;
  }

  public function getMessageCantClose()
  {
    return ($this->getIsProcessed()) ? "The career can´t be closed, because it´s been already processed" : "The career can't be closed because some courses haven't been closed.";

  }

  public function getMessageCantProcessRemainingStudents()
  {
    return "The remaining students can be processed because there are no students to be processed.";

  }

  /*
   * This function
   * @param PropelPDO $con
   */

  public function close(array $errors = Array(), PropelPDO $con = null)
  {
    $con = (is_null($con)) ? Propel::getConnection() : $con;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::ID, $this->getId());
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(StudentPeer::ID, StudentCareerSchoolYearPeer::STUDENT_ID);
    //$c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::IN_COURSE);
    $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, false);
    $c->setDistinct(StudentPeer::ID);

    sfConfig::set('sf_logging_enabled', FALSE);

    $cant = 0;
    try
    {
      $pager = new sfPropelPager('Student', 500);
      $pager->setCriteria($c);
      $pager->init();

      $last_page = $pager->getLastPage();

      for ($i = 1; $i <= $last_page; $i++)
      {

        $pager->setPage($i);
        $pager->init();
        $students = $pager->getResults();
        $con->beginTransaction();
        Propel::disableInstancePooling();


        foreach ($students as $student)
        {
          $cant++;

          $student_errors = $student->getErrorsWithCourseSubjectsStudent($this);
          //si el alumno tiene errores guardo el error y sigo evaluando a los demas alumnos
          if (!empty($student_errors))
          {
            $errors[$student->getId()] = $student_errors;
            if (count($errors) > sfConfig::get('app_close_course_subject_schol_year_max_error'))
            {
              throw new Exception('Maximo de errores alcanzado >' . sfConfig::get('app_close_course_subject_schol_year_max_error'));
            }
          }
          else
          {	
			$student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $this);
            //si el alumno tiene reserva de banco no deberia hacer nada.
            
            if($student_career_school_year->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE)
            {
				$student->closeCareerSchoolYear($this, $con);
            
				$student_career_school_year->setIsProcessed(true);
				$student_career_school_year->save($con);
				$student_career_school_year->clearAllReferences(true);
				unset($student_career_school_year);
			}
            
          }
          ####Liberando memoria###
          $student->clearAllReferences(true);
          unset($student_errors);
          unset($student);
          ##################*/

        }

        ####Liberando memoria###
        StudentPeer::clearInstancePool();
        unset($students);
        ##############################
        $con->commit();

      }

      if (count($errors))
      {
        throw new Exception('Hay errores no se puede cerrar el año!');
      }
      //Se setea a la carrera como procesada.
      $this->setIsProcessed(true);
      $this->save($con);
      Propel::enableInstancePooling();
    }

    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
    return true;

  }

  public function canCopyConfiguration()
  {
    if (!is_null($this->getSubjectConfiguration()))
    {
      return false;
    }

    if (!$this->canBeEdited())
      return false;

    if (!$this->getSchoolYear()->getIsActive())
      return false;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::CAREER_ID, $this->getCareerId());
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId(), Criteria::NOT_EQUAL);

    return CareerSchoolYearPeer::doCount($c) != 0;

  }

  public function getMessageCantCopyConfiguration()
  {
    if (!is_null($this->getSubjectConfiguration()))
    {
      return 'already has a current configuration';
    }
    elseif (!$this->canBeEdited())
    {
      return 'You can not edit the current school year';
    }
    elseif (!$this->getSchoolYear()->getIsActive())
    {
      return 'The school year is not active';
    }

  }

  public function copyConfiguration(PropelPDP $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::CAREER_ID, $this->getCareerId());
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId(), Criteria::NOT_EQUAL);
    $c->addDescendingOrderByColumn(CareerSchoolYearPeer::ID);

    try
    {
      $con->beginTransaction();

      //First copy the career configuration.
      $last_career = CareerSchoolYearPeer::doSelectOne($c);
      $configuration = $last_career->getSubjectConfiguration()->copy();
      $this->setSubjectConfiguration($configuration);

      //Second copy the career_subjects configurations
      foreach ($this->getCareerSubjectSchoolYears() as $cssy)
      {
        $c = new Criteria();
        $c->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $cssy->getCareerSubjectId());
        $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $last_career->getId());

        $last_career_subject = CareerSubjectSchoolYearPeer::doSelectOne($c);
        if (!is_null($last_career_subject) && !is_null($last_career_subject->getSubjectConfiguration()))
        {
          $cssy->setSubjectConfiguration($last_career_subject->getSubjectConfiguration()->copy());
          $cssy->save($con);
        }
      }
      $this->save($con);

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e->getMessage();
    }

  }

  public function canMatriculateStudentsFromLastYear()
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

    return $last_school_year && $last_school_year->getIsClosed();
  }

  public function getMessageCantMatriculateStudentsFromLastYear()
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

     if ($last_school_year && !$last_school_year->getIsClosed())
    {
      return 'Last year school year is still open';
    }
    else{
      return 'No previous school year';
    }
  }

  public function matriculateLastYearStudents()
  {
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();

      $criteria = StudentCareerSchoolYearPeer::retrieveLastYearStudentNotGraduatedCriteria($this);
      $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

      $pager = new sfPropelPager('Student', 100);
      $pager->setCriteria($criteria);
      $pager->init();
      $last_page = $pager->getLastPage();

      for ($i = 1; $i <= $last_page; $i++)
      {
        $pager->setPage($i);
        $pager->init();
        $students = $pager->getResults();

        foreach ($students as $student)
        {
			//no tiene reserva de banco y no es libre
			  if($student->getLastStudentCareerSchoolYear()->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN && $student->getLastStudentCareerSchoolYear()->getStatus()  != StudentCareerSchoolYearStatus::FREE)	 
			  {
				   if ($student->getPerson()->getIsActive()) {
			  
					  $shift = $student->getShiftForSchoolYear($last_school_year);

					  if (!$student->getIsRegistered($this->getSchoolYear()))
					  {
						$student->registerToSchoolYear($this->getSchoolYear(), $shift, $con);
					  }

					  if (!is_null($shift)) $shift->clearAllReferences(true);
					  $student->clearAllReferences(true);
					  unset($student);
					  unset($shift);
					}
			  }	
         
        }

        StudentPeer::clearInstancePool();
        unset($students);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  /**
  * This method check:
  * * If exists more than one school year
  * * The school year is active
  * * Note: It used to check if there are not divisions in the school year. Since we allow to pre-charge several school year at the same time
   * we quit this check.
  */
  public function canCreateLastYearDivisions()
  {
    $has_students = $this->getSchoolYear()->countSchoolYearStudents() > 0;

    return $this->getSchoolYear()->getIsActive() && SchoolYearPeer::doCount(new Criteria()) > 1 && $has_students;
  }

  /**
  * This method checks:
  * * If exists more than one school year
  * * The school year is active
  * * There are not commissions in the school year that belongs to this career school year
  */
  public function canCreateLastYearCommissions()
  {
    $has_students = $this->getSchoolYear()->countSchoolYearStudents() > 0;

    $has_commissions = count(CoursePeer::retrieveComissionsForCareerSchoolYear($this))> 0;

    return $this->getSchoolYear()->getIsActive() && SchoolYearPeer::doCount(new Criteria()) > 1 && !$has_commissions && $has_students;
  }

  public function createLastYearDivisions()
  {
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());
    $last_year_career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($this->getCareer(), $last_year_school_year);

    SchoolYearPeer::clearInstancePool();
    CareerSchoolYearPeer::clearInstancePool();
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();

      $criteria = new Criteria();
      $criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $last_year_career_school_year->getId());
      $pager = new sfPropelPager('Division', 10);
      $pager->setCriteria($criteria);
      $pager->init();
      $last_page = $pager->getLastPage();


      for ($i = 1; $i <= $last_page; $i++)
      {
        $pager->setPage($i);
        $pager->init();
        $divisions = $pager->getResults();

        //This creates all the divisions, courses and courses subjects of the last year.
        foreach ($divisions as $division)
        {
          $division->createCopyForSchoolYear($con, $this);

          $division->clearAllReferences(true);
          unset($division);
        }
        DivisionPeer::clearInstancePool();
      }
      unset($criteria);

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;

    }
  }

    public function createLastYearCommissions()
  {
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());
    $last_year_career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($this->getCareer(), $last_year_school_year);

    SchoolYearPeer::clearInstancePool();
    CareerSchoolYearPeer::clearInstancePool();
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();

      $commissions = CoursePeer::retrieveComissionsForCareerSchoolYear($last_year_career_school_year);

        foreach ($commissions as $commission)
        {
          $commission->createCopyForSchoolYear($con, null, $this);
          $commission->clearAllReferences(true);
          unset($commission);
        }
        unset($commissions);


      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;

    }
  }

  public function delete(PropelPDO $con = null)
  {
    #student_career_school_year
    $this->getStudentCareerSchoolYears();

    parent::delete($con);
  }

  public function getCourseTypeForYear($year)
  {
    return $this->getSubjectConfiguration()->getCourseTypeForYear($year);
  }

  public function getIsAbsenceForPeriodInYear($year)
  {
    return $this->getSubjectConfiguration()->getIsAbsenceForPeriodInYear($year);
  }

  public function getMaxAbsenceInYear($year)
  {
    return $this->getSubjectConfiguration()->getMaxAbsenceInYear($year);
  }

  public function countNotMatriculatedStudents()
  {
    $matriculated_school_year_students = $this->getSchoolYear()->countSchoolYearStudents(null, false);
    $matriculated_career_school_year_students = $this->countStudentCareerSchoolYears(null, true);

    return bcsub($matriculated_school_year_students, $matriculated_career_school_year_students, 0);
  }


  public function getMaxAbsenceForPeriod($period, $year)
  {

    if ($this->getIsAbsenceForPeriodInYear($year))
    {
      return $period->getMaxAbsences($year);
    }
    else
    {
      return $this->getMaxAbsenceInYear($year);
    }
  }



  public function getCareerSchoolYearPeriodsForYearAndCourseType($year, $courseType)
  {
    $courseType = is_null($courseType)
        ? $this->getCourseTypeForYear($year)
        : $courseType
    ;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, $courseType);

    return $this->getCareerSchoolYearPeriods($c);
  }

  public function canMatriculateGraduatedFromOtherCareer()
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

    return $last_school_year && $last_school_year->getIsClosed() && CareerPeer::moreThanOneCareer();
  }

  public function getMessageCantMatriculateGraduatedFromOtherCareer()
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

     if ($last_school_year && !$last_school_year->getIsClosed())
    {
      return 'Last year school year is still open';
    }
    elseif (!($last_school_year)){
      return 'No previous school year';
    }
    else {
      return 'School has only one career';
    }
  }

  public function isAttendanceForDay()
  {
    return $this->getSubjectConfiguration()?$this->getSubjectConfiguration()->getAttendanceType() == SchoolBehaviourFactory::getInstance()->getAttendanceDay():null;

  }

}


try { sfPropelBehavior::add('CareerSchoolYear', array('changelog')); }catch(sfConfigurationException $e) {}

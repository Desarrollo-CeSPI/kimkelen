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

class SchoolYear extends BaseSchoolYear
{

  /**
   * This Function returns true if the school year is active and arent any student inscripted.
   *
   *  @return boolean
   */
  public function canExamination(PropelPDO $con = null)
  {
    $count_careers = $this->countCareerSchoolYears();
    if ($count_careers == 0)
      return false;

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::IS_PROCESSED, true);
    $count_procesed = $this->countCareerSchoolYears($c);
    return ($count_careers == $count_procesed);
  }

  public function canManualExamination(PropelPDO $con = null)
  {
    $count_careers = $this->countCareerSchoolYears();
    if ($count_careers == 0)
      return false;

    return $this->getId() == SchoolYearPeer::retrieveCurrent()->getId();
  }

  public function getMessageCantExamination()
  {
    $count_careers = $this->countCareerSchoolYears();
    if ($count_careers == 0)
    {
      return 'Cant manage examination because the school year, dont have any career asociated.';
    }
    else
    {
      return 'Cant manage examination because the school year dont have all careers procesed.';
    }

  }

  /**
   * This Function returns true if the school year is active and arent any student inscripted.
   *
   *  @return boolean
   */
  public function canBeEdited(PropelPDO $con = null)
  {
    return $this->getIsActive() && ($this->countSchoolYearStudents($con) == 0 );

  }

  /*
   * This method returns why the school year cant be edited.
   *
   * return string
   */

  public function getMessageCantBeEdited()
  {
    $msj = (!$this->getIsActive()) ? "No se puede editar el año lectivo por que el mismo no se encuentra activo" : "";
    $msj = ($this->countStudentSchoolYears()) ? "No se puede editar el año lectivo, por que este ya tiene inscriptos" : "";
    return $msj;

  }

  /**
   * This function redefines the __toString() method of the career
   *
   * @see parent::__toString()
   *
   * @return string
   */
  public function __toString()
  {
    return '' . $this->getYear();

  }

  /**
   * This method return true or false, based on the result of the method isActive()
   *
   * @see isActive()
   *
   * @return <boolean
   */
  public function canChangeState()
  {
    return !$this->getIsActive() && !$this->getIsClosed() && !$this->hasTentativeRepprovedStudents();
  }

  /**
   * This method returns true or false, if getIsActive returns true then return false
   * If getIsActive is false, then check every CareerSchoolYears to see if we can delete them
   *
   * @return boolean
   * @see CareerSchoolYear::canBeDeleted
   */
  public function canBeDeleted(PropelPDO $con = null)
  {
    if ($this->getIsActive())
    {
      return false;
    }
    elseif ($this->hasStudents() || $this->getCareerSchoolYears())
    {
      return false;
    }
    foreach ($this->getCourses() as $c)
    {
      /* @var $c Course */
      if (!$c->canBeDeleted($con))
        return false;
    }

    return true;

  }

  /*
   * This method return why the school year cant be deleted
   *
   * return string
   */

  public function getMessageCantBeDeleted()
  {
    if ($this->getIsActive())
    {
      $msj = "No se puede borrar el año lectivo por que el mismo se encuentra activo. ";
    }
    elseif ($this->hasStudents())
    {
      $msj = 'No se puede borrar el año lectivo por que el mismo posee alumnos matriculados en alguna de las carreras';
    }
	  elseif ($this->getCareerSchoolYears()){
	    $msj = "Debe eliminar primero la/s carrera/s de este año lectivo";
    }

	  return $msj;

  }

  /* this method return true when have 1 or more students in 1 o more carreer_shool_years
   *
   */

  public function hasStudents()
  {
    $cant = 0;
    foreach ($this->getCareerSchoolYears() as $career_school_year)
    {
      $cant += count($career_school_year->getStudents());
    }
    if ($cant > 0)
    {
      return true;
    }
    return false;

  }

  /*
   * This method activates this school_year and set all the others as inactive.
   * If the schoolYear can be edited then creates for this school year the career_school_year of all the careers
   *
   * @see canBeEdited
   * @see createAllTheCareerSchoolYears
   */

  public function active(PropelPDO $con = null)
  {
    SchoolYearPeer::setAllUnactive();
    $this->setIsActive(true);

    return $this->save($con);

  }

  /**
   * This method creates all the careerSchoolYear, for the careers unrelated
   *
   * @see createCareerSchoolYear()
   */
  public function createAllTheCareerSchoolYears()
  {
    foreach ($this->getUnrelatedCareers() as $career)
    {
      $this->createCareerSchoolYear($career);
    }

  }

  /*
   * This method create the career_school_year for this school year and all the careerSubjectSchoolYear for the career
   *
   * @param Career $career
   */

  public function createCareerSchoolYear($career)
  {
    $con = Propel::getConnection(SchoolYearPeer::DATABASE_NAME);
    try
    {
      $con->beginTransaction();
      $career_school_year = new CareerSchoolYear();
      $career_school_year->setSchoolYear($this);
      $career_school_year->setCareer($career);

      $subject_configuration = $this->createOrCopyLastYearSubjectConfiguration($career, $con);
      $career_school_year->setSubjectConfiguration($subject_configuration);
      $career_school_year->save($con);

      foreach ($career->getCareerSubjects() as $career_subject)
      {
        $career_subject_school_year = new CareerSubjectSchoolYear();
        $career_subject_school_year->setCareerSchoolYear($career_school_year);
        $career_subject_school_year->setCareerSubject($career_subject);
        $career_subject_school_year->copyLastYearConfiguration();
        $career_subject_school_year->copyLastYearSort();
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

  public function createOrCopyLastYearSubjectConfiguration($career, PropelPDO $con = null)
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this);
    $last_year_career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career, $last_school_year);

    if (is_null($last_year_career_school_year))
    {
      $subject_configuration = new SubjectConfiguration();
    }
    else
    {
      $subject_configuration = $last_year_career_school_year->getSubjectConfiguration()->copy();
    }
    $subject_configuration->save($con);

    return $subject_configuration;

  }

  /*
   * This method returns all the careers that dont have this schoolYear
   *
   * return array Career[]
   */

  public function getUnrelatedCareers()
  {
    $already = array();
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $this->getId());
    foreach (CareerSchoolYearPeer::doSelect($c) as $csy)
      $already[] = $csy->getCareerId();

    $c2 = new Criteria();
    $c2->add(CareerPeer::ID, $already, Criteria::NOT_IN);
    return CareerPeer::doSelect($c2);

  }

  /**
   * Returns the maximum course examination count. This is given by the configuration.
   *
   * @return integer
   */
  public function getMaxCourseExaminationCount()
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $this->getId());
    $c->addJoin(CareerSchoolYearPeer::SUBJECT_CONFIGURATION_ID, SubjectConfigurationPeer::ID);
    $c->addDescendingOrderByColumn(SubjectConfigurationPeer::COURSE_EXAMINATION_COUNT);

    $career_conf = SubjectConfigurationPeer::doSelectOne($c);

    if (is_null($career_conf))
    {
      return 0;
    }

    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $this->getId());
    $c->addJoin(CareerSchoolYearPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::SUBJECT_CONFIGURATION_ID, SubjectConfigurationPeer::ID);
    $c->addDescendingOrderByColumn(SubjectConfigurationPeer::COURSE_EXAMINATION_COUNT);

    $subject_conf = SubjectConfigurationPeer::doSelectOne($c);

    if (is_null($subject_conf))
    {
      return $career_conf->getCourseExaminationCount();
    }
    else
    {
      return max($career_conf->getCourseExaminationCount(), $subject_conf->getCourseExaminationCount());
    }

  }

  public function close(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    try
    {
      $con->beginTransaction();

      SchoolBehaviourFactory::getEvaluatorInstance()->closeSchoolYear($this, $con);

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  /**
   * This Function returns true if the school year is active and all the examinations are closed.
   *
   *  @return boolean
   */
  public function canClose(PropelPDO $con = null)
  {
    return $this->countCareerSchoolYears() > 0 && !$this->getIsClosed() && $this->getIsActive() && $this->areAllExaminationsClosed();

  }

	public function currentYearIsClosed() {
		return $this->getIsClosed() && $this->getIsActive();
	}

  public function getMessageCantClose()
  {
    if ($this->getIsClosed())
    {
      return 'Cant close the school year because has already been closed.';
    }
    elseif (!$this->getIsActive())
    {
      return 'Cant close the school year because inst active.';
    }
    elseif (!$this->areAllExaminationsClosed())
    {
      return 'Cant close the school year because, there are some examinations opened.';
    }

  }

  public function areAllExaminationsClosed()
  {
    if (count(ExaminationPeer::retrieveForSchoolYearAndExaminationNumber($this, SchoolBehaviourFactory::getEvaluatorInstance()->getFebruaryExaminationNumber())) == 0)
    {
      return false;
    }

    foreach ($this->getExaminations() as $examination)
    {
      if (!$examination->isClosed())
      {
        return false;
      }
    }

    return true;
  }

  public function canExaminationRepproved()
  {
    return $this->getIsActive() && StudentRepprovedCourseSubjectPeer::doCount(new Criteria()) > 0;

  }

  public function getMessageCantExaminationRepproved()
  {
    if (!$this->getIsActive())
    {
      return "The school year is not active";
    }
    elseif (StudentRepprovedCourseSubjectPeer::doCount(new Criteria()) <= 0)
    {
      return "No students to take examination repproved";
    }

  }

  public function canShowStudents(PropelPDO $con = null)
  {
    return $this->getIsActive();

  }

  public function getMessageCantShowStudents()
  {
    return 'Cant show students, because the school year inst active.';

  }

	public function getMessageCantTentativeRepproveStudents()
	{
		return 'There are no problematic students to resolve.';
	}

	public function canTentativeRepproveStudents()
	{
		return (TentativeRepprovedStudentPeer::countPending() > 0 && $this->currentYearIsClosed());
	}

	public function hasTentativeRepprovedStudents()
	{
		return (TentativeRepprovedStudentPeer::countPending() > 0);
	}
}

try { sfPropelBehavior::add('SchoolYear', array('changelog')); } catch(sfConfigurationException $e) {}


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

class CareerSubject extends BaseCareerSubject
{
  const TALLER_S_NACIO = 235;
  public function __toString()
  {
    $orientation = is_null($this->getOrientation()) ? '' : '( ' . $this->getOrientation() . ' )';
    return sprintf('%2d año - %s %s', $this->getYear(), $this->getSubject(), $orientation);
  }

  public function toStringWithCareer()
  {
    return sprintf('%2d año - %s (%s)', $this->getYear(), $this->getSubject()->getFantasyName(), $this->getCareer());
  }

  /**
   * Written ONLY for clarification purposes: 'Options' can be misleading.
   *
   * @return boolean
   */
  public function getHasChoices()
  {
    return $this->getHasOptions();
  }

  /**
   * Written ONLY for clarification purposes: 'Option' can be misleading.
   *
   * @return boolean
   */
  public function getIsChoice()
  {
    return $this->getIsOption();
  }

  public function getSubjectName()
  {
    return $this->getSubject()->getName();
  }

  /**
   * Answer whether this object can be deleted. It depends on the status
   * of the Career.
   *
   * @param  PropelPDO $con Database connection (optional).
   *
   * @return boolean
   */
  public function canBeDeleted(PropelPDO $con = null)
  {

    return $this->getIsChoice() ?
      $this->countCareerSubjectSchoolYears(null, true, $con) == 0 :
      $this->getCareer($con)->canBeDeleted($con);
  }

  /**
   * String representation of the cantBeDeleted cause
   *
   * @return string
   */
  public function getMessageCantBeDeleted()
  {
    if ($this->getIsChoice())
    {
      return 'La opción de materia no puede eliminarse porque existen años lectivos que la utilizan';
    }
    return 'La materia no puede eliminarse porque el plan de estudio es inmodificable en esta instancia: tiene asociados alumnos y/o años lectivos';
  }

  /**
   * Answer wheter this object's can be edited. It depends on Career status
   *
   * @param  PropelPDO $con Database connection (optional).
   *
   * @return boolean
   */
  public function canBeEdited(PropelPDO $con = null)
  {
    return $this->getIsChoice() ?
      ($this->countCareerSubjectSchoolYears(null, true, $con) == 0) :
      $this->getCareer($con)->canBeEdited($con);
  }

  /**
   * String representation of the cantBeEdited cause
   *
   * @return string
   */
  public function getMessageCantBeEdited()
  {
    if ($this->getIsChoice())
    {
      return 'La opción de materia no puede editarse porque existen años lectivos que la utilizan';
    }
    return 'La materia no puede editarse porque el plan de estudio es inmodificable en esta instancia: tiene asociados alumnos y/o años lectivos';
  }

  /**
   * Answer wheter this object's correlatives can be edited.
   *
   * @param  PropelPDO $con Optional database connection.
   *
   * @return boolean
   */
  public function canBeEditedCorrelatives(PropelPDO $con = null)
  {
    return !$this->getIsChoice()
    &&
    !$this->getHasCorrelativePreviousYear()
    &&
    $this->getYear() > $this->getCareer()->getMinYear()
    &&
    !$this->getIsCorrelative();
  }

  /**
   * String representation of the cantBeEditedCorrelatives cause
   *
   * @return string
   */
  public function getMessageCantBeEditedCorrelatives()
  {
    return 'No es posible cambiar las correlatividades de la materia porque ha utilizado la propiedad de correlatividad de todas las materias del año previo o es una opción, o el plan de estudio es inmodificable en esta instancia: tiene asociados alumnos y/o años lectivos';
  }

  /**
   * Get a string representation of the credit hours.
   *
   * @param  string $suffix Text to append to the credit hours.
   *
   * @return string
   */
  public function getFormattedCreditHours($suffix = ' hs semanales')
  {
    return (!empty($this->credit_hours) ? $this->credit_hours . $suffix : '');
  }

  /**
   * Answer TRUE if this object has Correlatives related.
   *
   * @return boolean
   */
  public function hasCorrelatives()
  {
    return count($this->getCorrelativeCareerSubjects()) > 0;
  }

  /**
   * Returns correlatives career subjects for this object.
   *
   * @return array CareerSubject[]
   */
  public function getCorrelativeCareerSubjects(PropelPDO $con=null)
  {
    return SchoolBehaviourFactory::getInstance()->getCorrelativesForCareerSubject($this, null, $con);
  }

  /**
   * Returns an array of CareerSubjects whose correlative is $this
   * If new returns an empty array
   *
   * @param PropelPDO $con
   * @return array careerSubject
   */
  public function getCareerSubjectsCorrelatives(PropelPDO $con=null)
  {
    return $this->isNew() ? array() : SchoolBehaviourFactory::getInstance()->getCareerSubjectCorrelativesOf($this, null, $con);
  }

  /**
   * Check if $this is correlative for same other CareerSubject
   *
   * @param PropelPDO $con
   * @return bool
   *
   * @see CareerSubject::getCareerSubjectsCorrelatives
   *
   */
  public function getIsCorrelative(PropelPDO $con=null)
  {
    return count($this->getCareerSubjectsCorrelatives($con)) > 0;
  }

  public function retrieveInstanceForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $this->getId());
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    return CareerSubjectSchoolYearPeer::doSelectOne($c);
  }

  public function retrieveCurrent()
  {
    $school_year = SchoolYearPeer::retrieveCurrent();
    return $this->retrieveInstanceForSchoolYear($school_year);
  }

  public function getCareerSubjectSchoolYearsJoinCareerSchoolYearOne($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
  {    
    $aux = parent::getCareerSubjectSchoolYearsJoinCareerSchoolYear($criteria, $con, $join_behavior);
    return $aux[0];
  }

  public function canAddToCurrentCareerSchoolYear()
  {    
    return is_null($this->retrieveCurrent());
  }

  public function getCareerSchoolYear()
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::CAREER_ID, $this->getCareerId());
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    return CareerSchoolYearPeer::doSelectOne($c);
  }

  public function addToCurrentCareerSchoolYear(PropelPDO $con = null)
  {
    if (is_null($con)) 
    {
      $con = Propel::getConnection();
    }

    $career_school_year = $this->getCareerSchoolYear();

    try
    {
      $con->beginTransaction();

      $career_subject_school_year = new CareerSubjectSchoolYear();
      $career_subject_school_year->setCareerSubject($this);
      $career_subject_school_year->setCareerSchoolYear($career_school_year);

      $career_subject_school_year->save($con);

      $students = StudentPeer::retrieveForCareerSchoolYearAndYear($career_school_year, $this->getYear());

      foreach ($students as $student)
      {
        $student_career_subject_allowed = new StudentCareerSubjectAllowed();
        $student_career_subject_allowed->setCareerSubject($this);
        $student_career_subject_allowed->setStudent($student);
        $student_career_subject_allowed->save($con);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;
      
    }    
  }

	public function getCorrelativeCareerSubject() {

		$correlatives = $this->getCorrelativeCareerSubjects();
		
                if(count($correlatives) == 1)
                {
                    return $correlatives[0];
                }
                foreach ($correlatives as $c) {
                    /*if == name or is into correlative table*/
                    if ($c->getSubject() == $this->getSubject() ) {
                            return $c;
                    }
                        
		}
	}
  
}

try { sfPropelBehavior::add('CareerSubject', array('career_subject_school_year_update')); }catch(sfConfigurationException $e) {}

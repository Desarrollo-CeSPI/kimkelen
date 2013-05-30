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

class Career extends BaseCareer
{
  /**
   * Get a range of years from 1 to $this object quantity_years attribute.
   * If $combined is true, a combined array will be returned: that is, both the
   * keys and the values will be the range (suitable for <select> tags).
   *
   * @param  boolean $combined If set to true, return a combined array.
   *
   * @return array
   */
  public function getYearsRange($combined = false)
  {

    $range = range($this->getMinYear(), $this->getMaxYear());

    if (true === $combined)
    {
      $range = array_combine($range, $range);
    }

    return $range;
  }

/**
 * Returns the number that represents the first year for this career
 * @return int the first year
 */
  public function getMinYear()
  {
    return SchoolBehaviourFactory::getInstance()->getMinimumCareerYear();
  }

/**
 * Returns the last year for this career. This number is the minYear + quantityOfYears -1
 * For example, if minYear is 1 and quantityOfYears is 5, then it will returns
 * 1 + 5 - 1 = 5
 *
 * @return int The last year
 */
  public function getMaxYear()
  {
    return $this->getMinYear()+$this->getQuantityYears()-1;
  }

  /**
   * Returns the subject's year with the maximum value. If there are no subjects,
   * return 1 by default
   *
   * @return int  The last year of the set of subjects
   */
  public function getMaxYearSubject()
  {
     $criteria = new Criteria();
     $criteria->addDescendingOrderByColumn(CareerSubjectPeer::YEAR);
     $career_subjects = $this->getCareerSubjects($criteria);
     $max = array_shift($career_subjects);
     return is_null( $max )?1: $max->getYear();
  }


  /**
   * This function returns an array of career subject objects that are in $year
   *
   * @param  int   $year Represents a school year
   *
   * @return array Career subject objects
   */
  public function getCareerSubjectsForYear($year, $exclude_option = false)
  {
    $criteria = new Criteria();
    $criteria->add(CareerSubjectPeer::YEAR, $year);

    if (true === $exclude_option)
    {
      $criteria->add(CareerSubjectPeer::IS_OPTION, false);
    }

    return $this->getCareerSubjectsJoinSubject($criteria);
  }

  /**
   * This function returns and array based on the quantity of years of the career, for example, quantity_of_years = 2 will be array[1] => Año 1, array[2] => Año 2
   *
   * @param  bool   $add_empty Adds an empty option?
   *
   * @return array of strings
   */
  public function getYearsForOption($add_empty=false)
  {
    $years = $add_empty?array(''=>''):array();
    for($i=1;$i<=$this->getQuantityYears();$i++)
    {
      $years[$i] = 'Año '.$i;
    }
    return $years;
  }

  /**
   * This function returns true or false, that depends if the career has students inscripted
   *
   * @return array
   */
  public function hasInscripted()
  {
    return $this->countCareerStudents();
  }

  /**
   * This function returns returns true only if the career has not students nor school years
   *
   * @param PropelPDO $con database connection
   *
   * @return boolean
   */
  public function canBeEdited(PropelPDO $con = null)
  {
    return ($this->countCareerStudents($con) == 0) && ($this->countCareerSchoolYears($con) == 0 );
  }

  /**
   * String representation of the cantBeEdited cause
   *
   * @return string
   */
  public function getMessageCantBeEdited()
  {
    $ret = ($this->countCareerStudents() > 0 )?"El plan de estudios no puede editarse porque tiene alumnos inscriptos. ":'';
    $ret.= ($this->countCareerSchoolYears() > 0 )?"El plan de estudios no puede editarse porque se han creado años lectivos asociados a él. ":"";
    return $ret;
  }

  /**
   * This function returns true or false, that depends if the career has students inscripted
   *
   * @param PropelPDO $con database connection
   *
   * @return boolean
   */
  public function canBeDeleted(PropelPDO $con = null)
  {
    return $this->canBeEdited($con);
  }

  /**
   * String representation of the cantBeDeleted cause
   *
   * @return string
   */
  public function getMessageCantBeDeleted()
  {
    $ret = ($this->countCareerStudents() > 0 )?"El plan de estudios no puede eliminarse porque tiene alumnos inscriptos":'';
    $ret.= ($this->countCareerSchoolYears() > 0 )?"El plan de estudios no puede eliminarse porque se han creado años lectivos asociados a el":"";
    return $ret;
  }

  /**
   * This function returns true only if the career has not students nor school years
   *
   * @param PropelPDO $con database connection
   *
   * @return boolean
   */
  public function canCreateNewCareerSubject(PropelPDO $con = null)
  {

    return ($this->countCareerStudents($con) == 0) && ($this->countCareerSchoolYears($con) == 0 );
  }

  /**
   * String representation of the cantBeEdited cause
   *
   * @return string
   */
  public function getMessageCantCreateNewCareerSubject()
  {
    return "No es posible crear una materia en un plan de estudio con inscriptos o años lectivos asociados";
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
    return $this->getCareerName()." - ".$this->getPlanName();
  }


/**
 * Copy an instance for this object and returns new version of this career
 *
 * @return Career
 */
  public function getCopy()
  {
    $copy = new Career();
    $this->copyInto($copy, false);

    // EXTRACT FROM parent::copyInto
    //
    // important: temporarily setNew(false) because this affects the behavior of
    // the getter/setter methods for fkey referrer objects.
    $copy->setNew(false);

    foreach ($this->getCareerSubjects() as $relObj) {
        /* @var $relObj CareerSubject */
      if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
        $career_subject = $relObj->copy(true);
        $career_subject->clearCareerSubjectSchoolYears();
        $copy->addCareerSubject($career_subject);
      }
    }

    $copy->setNew(true);
    $copy->setId(NULL);

    return $copy;
  }

  public function getOrientations(Criteria $c=null)
  {
    $criteria = is_null($c)? new Criteria(): $c;
    $criteria->addAnd(CareerSubjectPeer::ORIENTATION_ID, null, Criteria::ISNOTNULL);
    return array_unique(array_map(create_function('$cs','return $cs->getOrientation();'),$this->getCareerSubjects($criteria)));

  }

  /**
   * Returns the next file number for this career and updates self, considering $con
   *
   * @param PropelPDO $con
   * @return int Next file number
   */
  public function getNextFileNumber(PropelPDO $con = null)
  {
    $next = $this->getFileNumberSequence();
    $this->setFileNumberSequence($next+1);
    $this->save($con);
    return $next;
  }

  public function  getCareerSchoolYear($school_year)
  {
    $criteria = new Criteria();
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID,$school_year->getId());
    $career_school_years = parent::getCareerSchoolYears($criteria);
    return isset($career_school_years[0])?$career_school_years[0]:null;
  }

}
sfPropelBehavior::add('Career', array('changelog'));
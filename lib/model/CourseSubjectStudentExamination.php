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

class CourseSubjectStudentExamination extends BaseCourseSubjectStudentExamination
{
  public function close(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    SchoolBehaviourFactory::getEvaluatorInstance()->closeCourseSubjectStudentExamination($this, $con);
  }

  public function getResultString()
  {
    list($clazz, $string) = SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationResult($this);

    return $string;
  }

  public function getResultClass()
  {
    list($clazz, $string) = SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationResult($this);

    return $clazz;
  }

  public function getStudent()
  {
    return $this->getCourseSubjectStudent()->getStudent();
  }

  public function getCourseSubject()
  {
    return $this->getCourseSubjectStudent()->getCourseSubject();
  }

  public function getCourseSubjectToString()
  {
    return $this->getCourseSubjectStudent()->getCourseSubject()->getCourse();
  }

  public function getValueString()
  {
    return $this->getIsAbsent() ? __('Absence') : $this->getMark();
  }

  public function getMarkStr()
  {
    if ($this->getIsAbsent()) {
      return __('A');
    }
    elseif ($this->getMark() != SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark()){
      return $this->getMark();
    }
  }

  public function __toString()
  {
    return $this->getMarkStr();
  }

  public function saveForTask(PropelPDO $con = null)
  {
    if ($con === null)
    {
      $con = Propel::getConnection(CourseSubjectStudentExaminationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $con->beginTransaction();
    try
    {
      $affectedRows = $this->doSave($con);
      $con->commit();
      CourseSubjectStudentExaminationPeer::addInstanceToPool($this);
      return $affectedRows;
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

  public function getMarkText()
  {
	$config = $this->getExaminationSubject()->getCareerSubjectSchoolYear()->getConfiguration();
	
	if(! is_null($config) && !$config->isNumericalMark())
	{
		return "-";
	
	}
	else
	{
		$c = new num2text();
		return $c->num2str($this->getMark());
    }
    
  }

    public function getFormattedDate()
  {
    if ($this->getDate())
    {
      return' (' . $this->getDate('d/m/Y') . ') ';
    }
  }
  
  public function getMarkStrByConfig($config = null)
  {    
    if ($this->getIsAbsent()) {
      return __('A');
      
    }else
    {
		if(is_null($config) && !is_null($this->getExaminationSubject()))
			$config = $this->getExaminationSubject()->getCareerSubjectSchoolYear()->getConfiguration();
		
		if($this->getMark() != SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark())
		{
			if(! is_null($config) && !$config->isNumericalMark())
			{
				$letter_mark = LetterMarkPeer::getLetterMarkByValue($this->getMark());
				return $letter_mark->getLetter(); 	   
			}else
			{
				return $this->getMark();
			}
		}
		
     }
    
  }

}

try { sfPropelBehavior::add('CourseSubjectStudentExamination', array('changelog')); }catch(sfConfigurationException $e) {}


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

class Examination extends BaseExamination
{
  public function getMessageCantShowExaminationSubjects()
  {
    return 'No students in a position to take the exam.';
  }

  public function  __toString()
  {
    return $this->getName();
  }

  public function isClosed()
  {
    if (count($this->getExaminationSubjects())== 0) {
      return false;
    }
    
    foreach ($this->getExaminationSubjects() as $examination_subject)
    {
      if (!$examination_subject->getIsClosed())
        return false;
    }

    return true;
  }

	public function getExaminationTypeStr()
	{
		$examinations= SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNumbersLong();
		return $examinations[$this->getExaminationNumber()];
	}

  public function createExaminationSubjectsForYear($year)
  {
	  $career_subject_school_years = CareerSubjectSchoolYearPeer::retrieveForExaminationAndYear($this, $year);

	  foreach ($career_subject_school_years as $career_subject_school_year)
	  {
		  $examination_subject = new ExaminationSubject();
		  $examination_subject->setCareerSubjectSchoolYearId($career_subject_school_year->getId());

		  $this->addExaminationSubject($examination_subject);
	  }
  }

	public function countExaminationSubjectsForYear($year) {
	  $c = new Criteria();

	  $c->addJoin(ExaminationSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
		$c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
		$c->add(CareerSubjectPeer::YEAR, $year);

		return $this->countExaminationSubjects($c);
  }
}
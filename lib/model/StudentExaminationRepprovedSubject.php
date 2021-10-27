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

class StudentExaminationRepprovedSubject extends BaseStudentExaminationRepprovedSubject
{
    public function getResultClass()
    {
        list($clazz, $string) = SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationRepprovedResult($this);

        return $clazz;
    }

    public function getStudent()
    {
        return $this->getStudentRepprovedCourseSubject()->getStudent();
    }

    public function getResultString()
    {
        list($clazz, $string) = SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationRepprovedResult($this);

        return $string;
    }

    public function close(PropelPDO $con = null)
    {
        $con = is_null($con) ? Propel::getConnection() : $con;

        SchoolBehaviourFactory::getEvaluatorInstance()->closeStudentExaminationRepprovedSubject($this, $con);
    }

    public function closeNotAverageableCalifications(PropelPDO $con = null)
    {
        $con = is_null($con) ? Propel::getConnection() : $con;

        SchoolBehaviourFactory::getEvaluatorInstance()->closeStudentExaminationRepprovedSubjectNotAverageableCalifications($this, $con);
    }

    public function getValueString()
    {
        return $this->getIsAbsent() ? __('Absence') : $this->getMark();
    }

    public function getShortValueString()
    {
        return $this->getIsAbsent() ? __('A') : $this->getMark();
    }

    public function renderChangeLog()
    {
        return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
    }


    public function getMarkText()
    {
		$config = $this->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getSubjectConfiguration();
	
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
    
    public function getMarkStrByConfig($config=null)
    {
		if(is_null($config))
		{
			$config = $this->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getSubjectConfiguration();
		}
		
		if(! is_null($config) && !$config->isNumericalMark())
		{
			$letter_mark = LetterMarkPeer::getLetterMarkByValue($this->getMark());
			return $letter_mark->getLetter(); 	   
		}else
		{
			if(!is_null($this->getNotAverageableMark()))
                        {
                            return BaseCustomOptionsHolder::getInstance('NotAverageableCalificationType')->getStringFor($this->getNotAverageableMark());
                        }
                        else
                        {
                           return $this->getMark();
                        }
		}
		
    }



}

sfPropelBehavior::add('StudentExaminationRepprovedSubject', array('changelog'));

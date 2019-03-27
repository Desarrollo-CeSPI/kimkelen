<?php /*
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

class BaseSubjectStudentAnalytic
{

    protected $approved_date = null;
    protected
    $roman_number = array(
      1 => 'I',
      2 => 'II',
      3 => 'III',
      4 => 'IV',
      5 => 'V',
      6 => 'VI',
      7 => 'VII',
    );

    public function __construct($css,$school_year)
    {
        $this->css = $css;

        $this->approved = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->css,$school_year);

        $this->school_year = $this->css->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear();

        $this->approvationInstance = null;
        
        $this->orientation = $this->css->getCourseSubject()->getCareerSubject()->getOrientation();
        
        $this->sub_orientation =  $this->css->getCourseSubject()->getCareerSubject()->getSubOrientation();
        
        $this->option = $this->css->getCourseSubject()->getCareerSubject()->getIsOption();
    }

    public function approvationInstance()
    {
        if ($this->approved && !$this->approvationInstance)
        {
            $this->approvationInstance = $this->approved->getApprovationInstance();
        }// es para mostrar las materias aprobadas del año en curso
        elseif (! $this->approved && !is_null($this->css->getStudentApprovedCourseSubject()) ) 
        {
            $this->approvationInstance = $this->css->getStudentApprovedCourseSubject();
        }
       
        return $this->approvationInstance;
    }

    public function getSubjectName()
    {
        return $this->css->getCourseSubject()->getSubject()->getName();
    }

    public function getNullLabel()
    {
        return;
    }
    
    protected function getDefaultApprovedExaminationString($date)
    {
        return 'Diciembre';
    }

    protected function getDefaultRepprovedExaminationString($date)
    {
        if ($date instanceof DateTime)
        {
            return $date->format('M');
        }
        return ;
    }


    public function getExaminationInstance()
    {
        $instance = $this->approvationInstance();
        switch (get_class($instance))
        {
            case 'StudentApprovedCourseSubject':
                //@TODO: Para el caso de regular no hay asociada una examination, agregarla en la base o personalizarla para cada flavor (Si no es diciembre)...
                return $this->getDefaultApprovedExaminationString($this->getApprovedDate(false));
            case 'StudentDisapprovedCourseSubject':
                return $instance->getClass();
            case 'StudentRepprovedCourseSubject':
                return $this->getDefaultRepprovedExaminationString($this->getApprovedDate(false));
        }
        
        return ;
    }
    
    public function getCondition()
    {
        $instance = $this->approvationInstance();
        switch (get_class($instance))
        {
            case 'StudentApprovedCourseSubject':
                return 'Regular';
            case 'StudentDisapprovedCourseSubject':
							  if ($instance->getExaminationNumber() == 1) {
                return 'R. Dic.';
							  } else {
								  return 'R. Comp.';
							  }
            case 'StudentRepprovedCourseSubject':
	            if ($instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == 1) {
		            return 'R. Prev.';
	            } else {
		            return 'Libre';
	            }
        }
        return;
    }

    public function getApprovedDate($as_label = true)
    {
        if ($this->approved_date)
        {	
            return $this->approved_date;
        }
        if ($this->approved)
        {	
            $approvationInstance = $this->approved->getApprovationInstance();
            $approvation_date = AnalyticalBehaviourFactory::getInstance($this->css->getStudent())->getApprovationDateBySubject($approvationInstance);
            if ($approvation_date)
            {	
                return $this->approved ? $this->approved_date = new DateTime(AnalyticalBehaviourFactory::getInstance($this->css->getStudent())->getApprovationDateBySubject($approvationInstance)) : ($as_label ? $this->getNullLabel() : null);
            }
        } 
        elseif (!is_null($this->css->getStudentApprovedCourseSubject())) 
        { 
            $approvation_date = AnalyticalBehaviourFactory::getInstance($this->css->getStudent())->getApprovationDateBySubject($this->css->getStudentApprovedCourseSubject());
            if ($approvation_date)
            {	
                return $this->approved_date = new DateTime(AnalyticalBehaviourFactory::getInstance($this->css->getStudent())->getApprovationDateBySubject($this->css->getStudentApprovedCourseSubject()));
            }
        }
        return ($as_label ? $this->getNullLabel() : null);
    }

    public function getSchoolYear()
    {
        return $this->approved ? $this->school_year : $this->getNullLabel();
    }

    public function getYear()
    {
        return $this->css->getCourseSubject()->getYear();
    }

    public function getMark($as_label = true)
    {
        if($this->approved)
        {
            return $this->approved->getMark();
        }
        else
        {
            $sacs = $this->css->getStudentApprovedCourseSubject();          
            return (!is_null($sacs) ? $sacs->getMark() : ($as_label ? $this->getNullLabel() : null));
        }
 
    }

    public function getMarkAsSymbol()
    {
        if (!$this->approved && is_null($this->css->getStudentApprovedCourseSubject()))
            return $this->getNullLabel();
        $c = new num2text();
        $mark = $this->getMark();
        $mark_parts = explode(',', $mark);
        if (1 === count($mark_parts))
        {
            $mark_parts = explode('.', $mark);
        }
        $mark_symbol = trim($c->num2str($mark_parts[0])) . ('00' !== $mark_parts[1]?','.$mark_parts[1]:'');
        
        return $mark_symbol;
    }

    public function getSchoolName()
    {
        if ($this->approved)
        {
            if ($this->approved->getIsEquivalence())
            {
                return "Establecimiento anterior";
            }
            else
            {
                return 'escuela_nombre'; //$this->css->getStudent()->getCareerStudent()->getCareer()->getCareerName();
            }
        }
        return $this->getNullLabel();
    }
    
    public function getOrientation()
    {
        return $this->orientation;
    }

    public function getSubOrientation()
    {
        return $this->sub_orientation;
    }

    public function getOption()
    {
        return $this->option;
    }

    public function getSubjectId()
    {
        return $this->css->getCourseSubject()->getSubject()->getId();
    }
    
    public function getOptionalCareerSubject()
    {
        $career_subject_school_year = $this->css->getCourseSubject()->getCareerSubjectSchoolYear();
        $c = new Criteria();
        $c->add(OptionalCareerSubjectPeer::CHOICE_CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_year->getId());
        $c->addJoin(OptionalCareerSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
       
        $cssy = CareerSubjectSchoolYearPeer::doSelectOne($c);
        return ($cssy) ? $cssy->getCareerSubject() : NULL ;

    }
    
    public function getNumber($number)
    {
        return $this->roman_number[$number];
    }
    
    public function getCourseSubjectStudent()
    {
        return $this->css;
    }
    
    public function getIsEquivalence()
    {
        if (!is_null($this->approved))
        {	
            return $this->approved->getIsEquivalence();
        }
        return false;
    }
    
}

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

    public function __construct($css)
    {
        $this->css = $css;

        $this->approved = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->css);

        $this->school_year = $this->css->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear();

        $this->approvationInstance = null;
    }

    public function approvationInstance()
    {
        if ($this->approved && !$this->approvationInstance)
        {
            $this->approvationInstance = $this->approved->getApprovationInstance();
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
	            if ($instance->getentExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == 1) {
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
            $approvation_date = StudentApprovedCareerSubjectPeer::retrieveApprovationDate($this->approved);
			
            if ($approvation_date)
            {	
                return $this->approved ? $this->approved_date = new DateTime(StudentApprovedCareerSubjectPeer::retrieveApprovationDate($this->approved)) : ($as_label ? $this->getNullLabel() : null);
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
        return ( $this->approved ? $this->approved->getMark() : ($as_label ? $this->getNullLabel() : null) );
    }

    public function getMarkAsSymbol()
    {
        if (!$this->approved)
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

}

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

class LvmSubjectStudentAnalytic extends BaseSubjectStudentAnalytic
{
    
    public function getCondition()
    {
        $instance = $this->approvationInstance();
        switch (get_class($instance))
        {
            case 'StudentApprovedCourseSubject':
                return 'Regular';
            case 'StudentDisapprovedCourseSubject':
                return 'Regular';
            case 'StudentRepprovedCourseSubject':
	            if ($instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == 1) {
		            return 'Regular';
	            } else {
		            return 'Libre';
	            }
        }
        return;
    }
    
    public function getSubjectName()
    {
        if( $this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de Gestión de las organizaciones'
           ||$this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de las Ciencias Sociales'
           ||$this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de las Ciencias Naturales'){
            return 'Introducción a la problemática de las Ciencias';
        }else{
             return $this->css->getCourseSubject()->getSubject()->getName();
        }
    }
    
    public function getMark($as_label = true)
    {
        
        if( $this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de Gestión de las organizaciones'
           ||$this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de las Ciencias Sociales'
           ||$this->css->getCourseSubject()->getSubject()->getName() == 'Introducción a la problemática de las Ciencias Naturales'){
            
            
           
        }else{
           
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

}
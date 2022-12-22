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

class NacionalSubjectStudentAnalytic extends BaseSubjectStudentAnalytic
{
    const INGLES = 10;
    const FRANCES = 9 ;
    
    protected
    $language = array(
      self::INGLES,
      self::FRANCES
    );

    public function getCondition()
    {
        $instance = $this->approvationInstance();
        switch (get_class($instance))
        {
            case 'StudentApprovedCourseSubject':  
                if ($this->getIsEquivalence())
                {
                    return "Equivalencia";
                }
                else
                {
                    return 'Regular';
                }
            case 'StudentDisapprovedCourseSubject':
                if ($instance->getExaminationNumber() == 1) 
                {
                        return 'Regular';
                }
                else
                {
                        return 'R. Comp.';
                }
            case 'StudentRepprovedCourseSubject':
                if (is_null($instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()) || $instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == 1)
                {
                        return 'R. Prev.';
                }
                else
                {
                        return 'Libre';
                }
        }
        return;
    }
        
    public function getSubjectName()
    {
        if($this->getOption())
        {
            return $this->css->getCourseSubject()->getSubject()->getName();
        }
        else
        {
            $year = $this->css->getCourseSubject()->getCareerSubject()->getYear();
            if(in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->language) && $year == 6) 
            {
                return "Idioma " .$this->getNumber($year). ': ' . $this->css->getCourseSubject()->getSubject()->getName();
            }
            else
            {
                return $this->css->getCourseSubject()->getSubject()->getName() . ' ' . $this->getNumber($year);
            }
        }
        
    }

    public function getMarkAsSymbol()
    {
        if (!$this->approved && is_null($this->css->getStudentApprovedCourseSubject()))
            return $this->getNullLabel();
        
        
        if($this->css->getIsNotAverageable() )
        {
            if(! is_null($this->css->getNotAverageableCalification()) && $this->css->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED)
            {
                return "Aprobado";
            }
            elseif(! is_null($this->css->getNotAverageableCalification()) && $this->css->getNotAverageableCalification() >= 7)
            {
                $c = new num2text();
                $mark = $this->css->getNotAverageableCalification();
                $mark_symbol = trim($c->num2str($mark));
        
                return $mark_symbol;
            }
            elseif(! is_null($this->css->getNotAverageableCalification()) && !is_null($this->approved))
            {
               if (is_null($this->getMark()))
               {
                   return "Aprobado";
               }
               else
               {
                  $c = new num2text();
                  $mark = $this->approved->getMark();
                  $mark_symbol = trim($c->num2str($mark));

                  return $mark_symbol;
               }
            }
            else
            {
              return $this->getNullLabel();
            }
            
            
        }
        
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

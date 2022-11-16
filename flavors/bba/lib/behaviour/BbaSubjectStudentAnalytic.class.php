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
class BbaSubjectStudentAnalytic extends BaseSubjectStudentAnalytic
{
	
	public function getMark($as_label = true)
    {
        if($this->approved)
        {
            $instance = $this->approvationInstance();
                if(get_class($instance) == 'StudentRepprovedCourseSubject')
                {
                    $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($instance);
                    if(!is_null($sers->getNotAverageableMark()))
                    {
                       return BaseCustomOptionsHolder::getInstance('NotAverageableCalificationType')->getStringFor($sers->getNotAverageableMark());
                          
                    }
                }
            return $this->approved->getMark();
        }
        else
        {
            $sacs = $this->css->getStudentApprovedCourseSubject(); 
            
            if($this->css->getIsNotAverageable() )
            {
                if(! is_null($this->css->getNotAverageableCalification()) && $this->css->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED)
                {
                    return "Aprobado";
                }
            	elseif(! is_null($this->css->getNotAverageableCalification()) && $this->css->getNotAverageableCalification() >= 7)
            	{
             
        		$mark = $this->css->getNotAverageableCalification();
        
       			 return $mark;
            	}
            	else
            	{
            		  return $this->getNullLabel();
           	 }


            }else{
                
                return (!is_null($sacs) ? $sacs->getMark() : ($as_label ? $this->getNullLabel() : null));
            }
        
        }
 
    }


    public function getMarkAsSymbol()
    {
        if (!$this->approved && is_null($this->css->getStudentApprovedCourseSubject()))
            return $this->getNullLabel();
       
        $instance = $this->approvationInstance();
                if(get_class($instance) == 'StudentRepprovedCourseSubject')
                {
                    $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($instance);
                    if(!is_null($sers->getNotAverageableMark()))
                    {
                       return BaseCustomOptionsHolder::getInstance('NotAverageableCalificationType')->getStringFor($sers->getNotAverageableMark());

                    }
                }   
        
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
            elseif(! is_null($this->approved->getMark()))
            {
		$c = new num2text();
                $mark = $this->approved->getMark();
                $mark_symbol = trim($c->num2str($mark));	
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

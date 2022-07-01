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
    protected $materias_introduccion = array(28,29,30);
    
    public function getCondition()
    {
        if(in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->materias_introduccion)){
           
            $introduccion = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentsForIntroduccion($this->css->getStudent(), $this->css->getCourseSubject()->getCareerSchoolYear());
            
            $count_approved = 0 ;
            $approved = true;
            $free =false;
            foreach ($introduccion as $course_subject_student)
            {
             $course_result = $course_subject_student->getCourseResult();
             if ($course_result)
             {
               $approved = ($approved && $course_result->isApproved()) ;
               
               if ($approved)
               {
                 $count_approved++; 
                 if(get_class($course_result) == 'StudentRepprovedCourseSubject' && $course_result->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() != 1 )
                 {
                   $free=true;
                 }
                
               }     
             }
             
            } 
           if ($count_approved == 3)
           {
                if($free)
                {
                    return 'Libre';
                }
                return 'Regular';
           }
           else
           {
               return NULL;
           }       
            
        }else{
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
        
        }
       
    }
    
    public function getSubjectName()
    {
        if( in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->materias_introduccion) ){
            return 'Introducción a la Problemática de las Ciencias Sociales, Ciencias Naturales y Gestión de las Organizaciones';
        }else{
             return $this->css->getCourseSubject()->getSubject()->getName();
        }
    }
    
    public function getMark($as_label = true)
    {
       
        if(in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->materias_introduccion))
        {
            
            $introduccion = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentsForIntroduccion($this->css->getStudent(), $this->css->getCourseSubject()->getCareerSchoolYear());
            
            $avg_mark = 0 ;
            $count_approved = 0 ;
            $approved = true;
         
            foreach ($introduccion as $course_subject_student)
            {
             $course_result = $course_subject_student->getCourseResult();
             if ($course_result)
             {
             $approved = ($approved && $course_result->isApproved()) ;
               if ($approved)
               {
                $count_approved++; 
                
                $avg_mark += $course_result->getFinalMark();
                }
             }
            } 
           if ($count_approved == 3)
           {
            return number_format(sprintf('%.4s', ($avg_mark / 3)), 2, '.', '');
           }
           else{
               return NULL;
           }
         
           
        }else{
           
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
                if($this->css->getIsNotAverageable() )
                {
                    if(! is_null($this->css->getNotAverageableCalification()) && $this->css->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED)
                    {
                        return "Aprobado";
                    }
                    else
                    {
                        return $this->getNullLabel();
                    }


                }
                else
                {
                
                $sacs = $this->css->getStudentApprovedCourseSubject();          
                return (!is_null($sacs) ? $sacs->getMark() : ($as_label ? $this->getNullLabel() : null));
            
                }
                
            }  
            
        }
    }
    public function getMarkAsSymbol()
    {
        if(in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->materias_introduccion))
        {
            $mark = $this->getMark();
            if($mark){
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
        else
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
                else
                { 
                    if (!$this->approved)
                    {
                    return $this->getNullLabel();
                    }
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
    
    public function getApprovedDate($as_label = true)
    {
        if(in_array($this->css->getCourseSubject()->getSubject()->getId(), $this->materias_introduccion))
        {
            
            $introduccion = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentsForIntroduccion($this->css->getStudent(), $this->css->getCourseSubject()->getCareerSchoolYear());
            
            $count_approved = 0 ;
            $free =false;
            
            $approvation_instance= null;
            foreach ($introduccion as $course_subject_student)
            { 
                $course_result = $course_subject_student->getCourseResult();
                if ($course_result)
                {
                  if ($course_result->isApproved())
                   {
                       $count_approved++; 
                       if(get_class($course_result) == 'StudentApprovedCourseSubject' && is_null($approvation_instance))
                       {
                           $approvation_instance=$course_result;
                           
                       }elseif(get_class($course_result) == 'StudentDisapprovedCourseSubject' && 
                               (is_null($approvation_instance) || get_class($approvation_instance) == 'StudentApprovedCourseSubject')) {
                           
                           $srcs = StudentRepprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($course_result->getStudentApprovedCareerSubject());
                           
                           if(is_null($srcs))
                           {
                               $approvation_instance=$course_result;
                           }
                           else
                           {
                               $approvation_instance=$srcs;
                           }
                       }elseif(get_class($course_result) == 'StudentRepprovedCourseSubject')
                       {
                           $approvation_instance=$course_result;
                       }

                       
                   }     
                }            
            }
            return ($count_approved == 3) ? new DateTime(AnalyticalBehaviourFactory::getInstance($this->css->getStudent())->getApprovationDateBySubject($approvation_instance)) : ($as_label ? $this->getNullLabel() : null); 
        }else
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
            
        }
        return ($as_label ? $this->getNullLabel() : null);
    }
}

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
 */

/**
 * Description of BaseAnalyticalBehaviour
 *
 * @author lucianoc
 */

class BaseAnalyticalBehaviour
{
    const YEAR_COMPLETE = 'C';
    const YEAR_INCOMPLETE = 'I';
    
    protected $_str_year_statuses = array(
        self::YEAR_COMPLETE => 'Curso C',
        self::YEAR_INCOMPLETE => 'Curso I',
    );
    
    protected $valid_status = array(
        StudentCareerSchoolYearStatus::APPROVED,
        StudentCareerSchoolYearStatus::IN_COURSE,
        StudentCareerSchoolYearStatus::FREE,
        StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED  
    );
    
    /* @var $student Student */
    protected $student = null;
    protected $objects = array();
    protected $missing_subjects = array();
    protected $years_in_career = array();
    protected $last_exam_date = null;
    protected $total_average = null;
    /* @var $career_student CareerStudent */
    protected $career_student = null;
    protected $remaining_years = null;
    protected $approved_subject = null;


    public function __construct(Student $a_student)
    {
        $this->student = $a_student;
    }
    
    public function is_empty()
    {
        return (0 === count($this->objects));
    }
    
    public function get_objects()
    {
        return $this->objects;
    }
    
    public function get_years_in_career()
    {
        return $this->years_in_career;
    }
    
    public function get_subjects_in_year($year)
    {
	return (isset($this->objects[$year]) ? $this->objects[$year]['subjects'] : NULL);
    }

    public function get_graduated_date()
    {
        $last = $this->get_years_in_career();
        $count = count($this->get_years_in_career()) - 2;
        $last_date = date('Y-m-d');

        for ( $i = $count ; $i <  count($this->get_years_in_career()) ; $i++)
        {
             foreach ($this->objects[$last[$i]]['subjects'] as $css) {
                $date = $css->getApprovedDate();
                  if ($date > $last_date) {
                    $last_date = $date;
                  }
              }
        }
        return $last_date;
    }
    
    public function get_missing_subjects()
    {
        return $this->missing_subjects;
    }
    
    public function get_year_average($year)
    {
      return ($this->objects[$year]) ? $this->objects[$year]['average'] : NULL;
    }
    
    public function get_year_status($year)
    { 
        return (!is_null($this->objects[$year])) ? $this->objects[$year]['status'] : NULL;
    }

    public function get_str_year_status($year)
    {
        return (!is_null($this->get_year_status($year)))? $this->_str_year_statuses[$this->get_year_status($year)] : ""; 
    }
    
    public function get_total_average()
    {
        return $this->total_average;
    }
    
    public function get_student()
    {
        return $this->student;
    }

    public function get_career_student()
    {
        return $this->career_student;
    }
    
    public function get_plan_name()
    {
        return $this->get_career_student()->getCareer()->getPlanName();
    }

    public function get_resolution_number()
    {
       return $this->get_career_student()->getCareer()->getResolutionNumber();
    }

    public function get_orientation()
    {
        return $this->get_career_student()->getOrientation();
    }
    
    public function get_current_division_string()
    {
        if ($this->has_completed_career())
        {
            //@TODO: Buscar la division para los egresados (ya que el current utiliza la fecha y al estar egresado la division es nula)
            return $this->get_career_student()->getCareer()->getMaxYear();
        }
        return $this->get_student()->getCurrentDivisionsString();
    }
    
    public function get_current_school_year()
    {
        return $this->get_student()->getCurrentOrLastStudentCareerSchoolYear();
    }
    
    public function get_remaining_years_string()
    {
        $years = $this->get_remaining_years();
        foreach ($years as &$year)
        {
            $year = 'Year '.$year;
        }
        return $years;
    }


    public function get_remaining_years()
    {
        if (is_null($this->remaining_years) and !$this->has_completed_career())
        {
            $this->remaining_years = array();
            $years = $this->get_career_student()->getCareer()->getYearsRange();
            $current_year = $this->get_current_school_year();
            $scsy_cursed = $this->get_student()->getLastStudentCareerSchoolYearCoursed();
            if(!is_null($current_year))
            {
                if($scsy_cursed)
                {
                    foreach ($years as $year)
                    {
                        if ($current_year->getYear() < $year ||( $current_year->getYear() == $year && $current_year->getId() != $scsy_cursed->getId() 
                                    && $scsy_cursed->getStatus() != StudentCareerSchoolYearStatus::REPPROVED ))
                            {
                                    $this->remaining_years[] = $year;
                            }   	
                    }
                }
                else
                {
                    foreach ($years as $year)
                    {
                        if ($current_year->getYear() < $year)
                        {
                            $this->remaining_years[] = $year;
                        }
                    }
                }			
            }   
        }
        return $this->remaining_years;
    }

    public function has_missing_subjects()
    {
        return (count($this->missing_subjects) > 0);
    }
    
    public function has_remaining_years()
    {
        return (count($this->get_remaining_years()) > 0);
    }
    
    public function has_completed_year($year)
    {
        return (isset($this->objects[$year]['status']) and self::YEAR_COMPLETE == $this->objects[$year]['status']);
    }
    
    public function has_completed_career()
    {
        return $this->get_career_student()->isGraduate();
    }


    public function subject_is_averageable($subject)
    {
        return true;
    }
    
    public function get_last_exam_date()
    {
        //$dt = DateTime::createFromFormat('Y-m-d', $this->last_exam_date);
        if (!$this->last_exam_date instanceof DateTime)
        {
            return new DateTime($this->last_exam_date);
        }
        else
        {
            return $this->last_exam_date;
        }
    }
    
    protected function init()
    {
        $this->objects = array();
        $this->years_in_career = array();
        $this->missing_subjects = array();
        $this->last_exam_date = null;
        $this->career_student = $this->get_student()->getCareerStudent();
        $this->approved_subject = false;
    }
    
    protected function add_year_in_career($year)
    {
        if (!in_array($year, $this->years_in_career))
        {
            $this->years_in_career[] = $year;
        }
    }
    
    protected function add_missing_subject($css)
    {
        $this->missing_subjects[] = $css;
    }
    
    protected function add_subject_to_year($year, $css)
    {
        if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
            $this->objects[$year]['subjects'] = array();
        }
        $this->objects[$year]['subjects'][] = $css;
    }
    
    protected function check_last_exam_date($date)
    {
        if ($this->last_exam_date === null or $date > $this->last_exam_date)
        {
            $this->last_exam_date = $date;
        }
    }
    
    protected function set_year_status($year, $status)
    {
        if (!isset($this->_str_year_statuses[$status]))
        {
            throw new Exception('Analytical year status not fund: '.$status);
        }
        $this->objects[$year]['status'] = $status;
    }
    
    protected function process_year_average($year, $sum, $count)
    {
        if (self::YEAR_COMPLETE === $this->get_year_status($year))
        {
            // Si el curso está completo, calculo el promedio
            $this->objects[$year]['average'] = ($sum / $count);
        }
        else
        {
            // Si el curso no está completo, no se muestra el promedio
            $this->objects[$year]['average'] = null;
        }
    }
    protected function process_total_average($avg_mark_for_year)
    {
        $sum = 0;
        $prom_anual = 0;
        foreach ($avg_mark_for_year as $year => $data)
        {
            if (self::YEAR_COMPLETE === $this->get_year_status($year))
            {
                $prom_anual = $data['sum'] / $data['count'];
                $sum += $prom_anual;
            }
            else
            {
                $this->total_average = null;
                return;
            }
        }
        $this->total_average = ($sum/count($avg_mark_for_year));
    }
    
    public function process()
    {
        $this->student_career_school_years = $this->get_student()->getStudentCareerSchoolYears();
	$scsy_cursed = $this->get_student()->getLastStudentCareerSchoolYearCoursed();	

        //Deberia recorrer todos los "scsy" y recuperar por c/año las materias
        $this->init();
        $avg_mark_for_year = array();

        foreach ($this->student_career_school_years as $scsy)
        {
            //Si está en el arreglo de estados válidos o está retirado y cursó materias en ese año o si repitio ero fue el ultimo año que cursó
            if (in_array($scsy->getStatus(), $this->valid_status) || ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN  && 
                         $scsy->getId() == $scsy_cursed->getId()) || ($scsy->getStatus() == StudentCareerSchoolYearStatus::REPPROVED && 
                         $scsy->getId() == $scsy_cursed->getId()) )
            {
                $year_in_career = $scsy->getYear(); 
                $this->add_year_in_career($year_in_career);
                $career_school_year = $scsy->getCareerSchoolYear();
                $school_year = $career_school_year->getSchoolYear();

                $approved = StudentApprovedCareerSubjectPeer::retrieveByStudentAndSchoolYear($this->get_student(), $school_year);
                $csss = SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForAnalytics($this->get_student(), $school_year, $scsy);
				
                foreach ($csss as $css)
                {	
                    if (!isset($this->objects[$year_in_career]))
                    {
                        // Inicialización por año
                        $this->set_year_status($year_in_career, self::YEAR_COMPLETE);
                        $avg_mark_for_year[$year_in_career]['sum'] = 0;
                        $avg_mark_for_year[$year_in_career]['count'] = 0;
                    }
                    
                    if (!$css->getCourseSubjectStudent()->getIsNotAverageable())
                    {
                        $avg_mark_for_year[$year_in_career]['sum'] += $css->getMark();
                        $avg_mark_for_year[$year_in_career]['count'] += ($css->getMark(false) ? 1 : 0);
                        if (!$css->getMark(false))
                        {
                            // No tiene nota -> el curso está incompleto
                            $this->set_year_status($year_in_career, self::YEAR_INCOMPLETE);
                            $this->add_missing_subject($css);
                        }
                    }
                   
                    $this->add_subject_to_year($year_in_career, $css);
                    $this->check_last_exam_date($css->getApprovedDate(false));
                }

                // Cálculo del promedio por año
                foreach ($this->objects as $year => $data)
                {
                    $this->process_year_average($year, $avg_mark_for_year[$year]['sum'], $avg_mark_for_year[$year]['count']);
                }
                $this->process_total_average($avg_mark_for_year);
                
            }            
        }
    }

	public function showCertificate() {
		return false;
	}
        
    public function getApprovationDateBySubject($approvationInstance)
    {
        switch(get_class($approvationInstance)) {
          case 'StudentApprovedCourseSubject':

            $period = $approvationInstance->getCourseSubject()->getLastCareerSchoolYearPeriod();
            if(!is_null($period))
            {
              return $period->getEndAt();
            }
            break;
          case 'StudentDisapprovedCourseSubject': 
            $cssid = $approvationInstance->getCourseSubjectStudentId();
            $csse = CourseSubjectStudentExaminationPeer::retrieveLastByCourseSubjectStudentId($cssid);
            $exam = $csse->getExaminationSubject()->getExamination();

            return ($csse->getExaminationSubject()->getDate()) ? $csse->getExaminationSubject()->getDate() : $exam->getDateFrom();

          case 'StudentRepprovedCourseSubject':
              
            $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($approvationInstance); 
            if(is_null($sers->getExaminationRepprovedSubject()))
            {
                //Estuvo en trayectorias. Es el año de la trayectoria + 1
                $cssp = CourseSubjectStudentPathwayPeer::retrieveByCourseSubjectStudent($approvationInstance->getCourseSubjectStudent());
                $year = $cssp->getPathwayStudent()->getPathway()->getSchoolYear()->getYear();
                $year += 1;
                return $year .'-07-01';
            }
            else
            {
                $exam = $sers->getExaminationRepprovedSubject()->getExaminationRepproved();
                return ($sers->getExaminationRepprovedSubject()->getDate()) ? $sers->getExaminationRepprovedSubject()->getDate() : $exam->getDateFrom(); 
                
            }
           
        }

        //couldn't find when was approved. return null ¿error?
        return;
        
    }
    
    public function getSpecialityTypeString($career_student)
    {
        return;
    }
    
    public function is_approved_subject()
    {
        return $this->approved_subject;
    }
}

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

class BbaAnalyticalBehaviour extends DefaultAnalyticalBehaviour
{
    const
        GRABADO    = 214,
        PINTURA    = 114,
        ESCULTURA  = 215,
        ESPACIO_IA = 126,
        CBFE_1       = 9,
        CBFE_2       = 10,
        CONTEXTUALIZACION = 135,
        PROYECTOS    = 136,
        ARTE_Y_SUBJETIVIDAD = 140,
        PROBLEMATICA_SOCIAL = 141,
        PRACTICAS_PROFESIONALIZANTES = 155;
    
    protected $optional_specific = array(
        self::GRABADO,
        self::PINTURA,
        self::ESCULTURA,
    );
    
    protected $subjects = array(
        self::ESPACIO_IA,
        self::PROYECTOS,
        self::CONTEXTUALIZACION,
        self::ARTE_Y_SUBJETIVIDAD,
        self::PROBLEMATICA_SOCIAL,
        self::PRACTICAS_PROFESIONALIZANTES,
    );
    
    protected $cbfe = array(
        self::CBFE_1,
        self::CBFE_2,
    );
    
    protected $suborientation_subjects = array(
        self::CONTEXTUALIZACION,
        self::PROYECTOS,
        self::ARTE_Y_SUBJETIVIDAD,
        self::PROBLEMATICA_SOCIAL,
        self::PRACTICAS_PROFESIONALIZANTES,

    );
    
    public function process()
    {
        $this->student_career_school_years = $this->get_student()->getStudentCareerSchoolYears();
		
        //recorrer todos los "scsy" y recuperar por c/año las materias
        $this->init();
        $avg_mark_for_year = array();

        foreach ($this->student_career_school_years as $scsy)
        {
            //chequeo que la carrera no sea el ciclo basico.
            $career_school_year = $scsy->getCareerSchoolYear();
            $career = $career_school_year->getCareer();
            $scsy_cursed = $this->get_student()->getLastStudentCareerSchoolYearCoursed();	
            
            if(!in_array($career->getId(),$this->cbfe)) 
            {
                if (in_array($scsy->getStatus(), $this->valid_status) || ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN  && 
                $scsy->getId() == $scsy_cursed->getId()) || ($scsy->getStatus() == StudentCareerSchoolYearStatus::REPPROVED && 
                $scsy->getId() == $scsy_cursed->getId()) )
                {

                    $year_in_career = $scsy->getYear(); 
                    $this->add_year_in_career($year_in_career);
                    $career_school_year = $scsy->getCareerSchoolYear();
                    
                    $school_year = $scsy->getCareerSchoolYear()->getSchoolYear();
                    
                    $approved = StudentApprovedCareerSubjectPeer::retrieveByStudentAndSchoolYear($this->get_student(), $school_year);
                    $csss = SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForAnalytics($this->get_student(), $school_year,$scsy);

                    foreach ($csss as $css) // $css CareerSubjectStudent
                    {
                        if (!isset($this->objects[$year_in_career]))
                        {
                            // Inicialización por año
                            $this->set_year_status($year_in_career, self::YEAR_COMPLETE);
                            $avg_mark_for_year[$year_in_career]['sum'] = 0;
                            $avg_mark_for_year[$year_in_career]['count'] = 0;
                        }

                        if ($this->subject_is_averageable($css))
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
                      
                        //si la materia no tiene orientacion ni optativas es general. Y no es Espacio de Integración Artística
                        if(is_null($css->getOrientation()) && !$css->getOption() && ! in_array($css->getSubjectId(), $this->subjects))
                        {
                            $this->add_general_subject_to_year($year_in_career, $css);
                        }
                        else
                        {
                            //chequeo si es una asignatura optativa
                            if($css->getOption())
                            {   //si es optativa, verifico la configuración de la materia que contiene a esta opción
                                $career_subject = $css->getOptionalCareerSubject();
                              
                                if(is_null($career_subject->getOrientation()))
                                {   //si no tiene orientación es general, 
                                    $this->add_general_subject_to_year($year_in_career, $css);
                                     
                                }elseif(in_array($css->getSubjectId(), $this->optional_specific))
                                {
                                    $this->add_specific_subject_to_year($year_in_career, $css);
                                }else
                                {
                                   $this->add_optional_subject_to_year($year_in_career, $css); 
                                } 
                            }
                            else
                            {   //chequeo si es propia de la especialidad
                                if((is_null($css->getSubOrientation()) && in_array($css->getSubjectId(), $this->suborientation_subjects))||
                                        ! is_null($css->getSubOrientation()) )
                                {
                                   $this->add_suborientation_subject_to_year($year_in_career, $css);   
                                }
                                else
                                {
                                    $this->add_specific_subject_to_year($year_in_career, $css);
                                }
                            }
                        }

                        $this->check_last_exam_date($css->getApprovedDate(false));
                    }

                    // Cálculo del promedio por año
                    foreach ($this->objects as $year => $data)
                    {
                            $this->process_year_average($year, $avg_mark_for_year[$year]['sum'], $avg_mark_for_year[$year]['count']);
                    }

                    $this->process_total_average($avg_mark_for_year);
                    
                    $this->add_school_year_to_year($year_in_career,$school_year);
                }

                $divisions = $this->get_student()->getCurrentDivisions( $career_school_year->getId());

                foreach ($divisions as  $d)
                { 
                        $this->add_division_to_year($year_in_career ,$d->getName());
                }
            }         
        }
    }
    
    protected function add_school_year_to_year($year, $school_year)
    {
        if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
        }
        $this->objects[$year]['school_year'] = $school_year;
    }
    
    protected function add_general_subject_to_year($year, $css)
    {
        if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
            $this->objects[$year]['general_subjects'] = array();
        }
        $this->objects[$year]['general_subjects'][] = $css;
    }
    
    protected function add_optional_subject_to_year($year, $css)
    {
		if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
            $this->objects[$year]['optional_subjects'] = array();
        }
        $this->objects[$year]['optional_subjects'][] = $css;
	}
	
    protected function add_specific_subject_to_year($year, $css)
    {
        if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
            $this->objects[$year]['specific_subjects'] = array();
        }
        $this->objects[$year]['specific_subjects'][] = $css;
    }
    
    protected function add_division_to_year($year,$division)
    {
		if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
         
        }
        $this->objects[$year]['division'] = $division;
	}
    
    public function add_suborientation_subject_to_year($year, $css)
    {
		
		if (!isset($this->objects[$year]))
        {
            $this->objects[$year] = array();
            $this->objects[$year]['suborientation_subjects'] = array();
        }
        $this->objects[$year]['suborientation_subjects'][] = $css;
	}
    
    public function get_general_subjects_in_year($year)
    {
        return $this->objects[$year]['general_subjects'];
    }
    
    public function get_specific_subjects_in_year($year)
    {
        return $this->objects[$year]['specific_subjects'];
    }
    
    public function get_suborientation_subjects_in_year($year)
    {
        return $this->objects[$year]['suborientation_subjects'];
    }
    
    public function get_optional_subjects_in_year($year)
    {
		return $this->objects[$year]['optional_subjects'];
	}
	
    public function get_career_name($year)
    {
		if($year > 3)
		{
			return "Educación Secundaria Superior";
		}else{
			return "Educación Secundaria Básica";
		}
		
	}
	
	public function get_school_year($year)
	{
		return $this->objects[$year]['school_year'];
	}
	
	public function get_division($year)
	{
		return $this->objects[$year]['division'];
	}
	
}

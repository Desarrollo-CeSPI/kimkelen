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

require_once dirname(__FILE__) . '/../lib/student_statsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/student_statsGeneratorHelper.class.php';

/**
 * student_stats actions.
 *
 * @package    sistema de alumnos
 * @subpackage student_stats
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class student_statsActions extends autoStudent_statsActions
{
  public function executeFilterForStudentStats(sfWebRequest $request)
  {
    $this->form = new StudentReportsForm();
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->getUser()->setReferenceFor($this, 'student_stats');
        $this->forward('student_stats', 'studentReports');
      }
    }
  }

  private function generateReportArray($title, $total, $filters = null)
  {
    return array('title' => $title,
      'total' => $total,
      'filters' => $filters
    );
  }

  public function executeSetStudentFilters(sfWebRequest $request)
  {
    $parameters = $request->getGetParameters();
    $this->getUser()->setAttribute('student_stats.filters', array(), 'admin_module');
    $this->getUser()->setAttribute('student_stats.filters', $parameters, 'admin_module');
    $this->getUser()->setAttribute('career_school_year', $parameters['career_school_year']);

    $this->redirect('@student_stats');
  }

  private function generateStudentReports($params)
  {
    if ($params['career_school_year_id'] != "")
    {
      $career_school_years = array(CareerSchoolYearPeer::retrieveByPK($params['career_school_year_id']));
    }
    else
    {
      $career_school_years = CareerSchoolYearPeer::retrieveBySchoolYear();
    }

    $this->shift = isset($params['shift_id']) ? $params['shift_id'] : "";
    $this->division = isset($params['division_id']) ? $params['division_id'] : "";
    $this->year = isset($params['year']) ? $params['year'] : "";

    $this->stats_table = array();
    $this->school_year = $career_school_years[0]->getSchoolYear();
    if (count($career_school_years) == 1){
      $this->career_school_year = $career_school_years[0];
    }
    $this->stats_table['student_reports'] = $this->generateGeneralReports($career_school_years);
    $this->stats_table['shift_reports'] = $this->generateShiftReports($career_school_years, $this->shift);
    $this->stats_table['shift_division_reports'] = $this->generateShiftDivisionReports($career_school_years, $this->shift, $this->division);
    $this->stats_table['year_reports'] = $this->generateYearReports($career_school_years, $this->year);
    $this->stats_table['year_shift_reports'] = $this->generateYearShiftReports($career_school_years, $this->year, $this->shift);
    $this->stats_table['year_shift_division_reports'] = $this->generateYearShiftDivisionReports($career_school_years, $this->year, $this->shift, $this->division);
  }

  public function executeStudentReports(sfWebRequest $request)
  {
    $params = $request->getParameter('student_reports');
    unset($params['_csrf_token']);

    $this->generateStudentReports($params);

    $this->params = $params;
  }

  /*
   * Generates reports for general tab
   */
  private function generateGeneralReports($career_school_years)
  {
    $student_reports_by_career = array();

    $filters = array('school_year' => $this->school_year->getId());
    $student_reports = array($this->generateReportArray('Estudiantes matriculados', $this->school_year->countSchoolYearStudents(null, false), $filters));

    if (!is_null($last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->school_year)))
    {

      //NO SE SACAN ASI LOS INGRESANTES... tendria que ser que en el career_school_year tengan 1 sola tupla, el estado sea cursando y el año sea 1

      //$total = bcsub($this->school_year->countSchoolYearStudents(null, true), $last_year_school_year->countSchoolYearStudents(), 0);

      $filters['is_entrant'] = true;
      $student_reports[] = $this->generateReportArray('Ingresantes con respecto al año lectivo anterior', $total, $filters);
    }

    $filters = array('school_year' => $this->school_year->getId(), 'has_disciplinary_sanctions' => true);
    $disciplinary_sanctions = StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForSchoolYear($this->school_year);
    $student_reports[] = $this->generateReportArray('Estudiantes con sanciones disciplinarias', $disciplinary_sanctions, $filters);

    $student_reports_by_career['For school year'] = $student_reports;

    foreach ($career_school_years as $csy)
    {
      $student_reports = array();
      $filters = array('career_school_year' => $csy->getId());
      $student_reports[] = $this->generateReportArray('Estudiantes inscriptos', $csy->countStudentCareerSchoolYears(null, true), $filters);

      //Total  no inscriptos en la carrera
      $not_inscripted_in_csy = $csy->countNotMatriculatedStudents();
      if ($not_inscripted_in_csy < 0){
        $not_inscripted_in_csy=0;
      }
      $student_reports[] = $this->generateReportArray('Estudiantes no inscriptos', $not_inscripted_in_csy, null);

      //Total graduated students for career
      $filters['is_graduated'] = true;
      $student_reports[] = $this->generateReportArray('Estudiantes graduados', CareerStudentPeer::doCountGraduatedForCareerSchoolYear($csy), $filters);

      $student_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $student_reports;
    }

    return $student_reports_by_career;
  }

  /*
   * Generates reports for shift tab
   */
  private function generateShiftReports($career_school_years, $shift)
  {
    $shift_reports_by_career = array();

    foreach ($career_school_years as $csy)
    {
      $shift_reports = array();
      if ($shift == "")
      {
        foreach (ShiftPeer::doSelect(new Criteria()) as $s)
        {
          $title = 'Estudiantes inscriptos en divisiones del turno ' . strtolower($s);
          $filters = array('shift' => $s->getId(), 'career_school_year' => $csy->getId());
          $shift_reports[] = $this->generateReportArray($title, $s->countStudentsInDivisions(DivisionStudentPeer::doSelectDivisionsForCareerSchoolYearAndShift($csy, $s)), $filters);
        }
      }
      else
      {
        $title = 'Estudiantes inscriptos en divisiones del turno ' . strtolower(ShiftPeer::retrieveByPK($shift));
        $filters = array('shift' => ShiftPeer::retrieveByPK($shift)->getId(), 'career_school_year' => $csy->getId());
        $shift_reports[] = $this->generateReportArray($title, ShiftPeer::retrieveByPK($shift)->countStudentsInDivisions(DivisionStudentPeer::doSelectDivisionsForCareerSchoolYearAndShift($csy, ShiftPeer::retrieveByPK($shift))), $filters);
      }

      $title = 'Estudiantes sin inscripciones en alguna división';
      $shift_reports[] = $this->generateReportArray($title, StudentCareerSchoolYearPeer::countStudentsNotInAnyDivisionForCareerSchoolYear($csy), null);

      $shift_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $shift_reports;
    }

    return $shift_reports_by_career;
  }

  private function generateShiftDivisionReports($career_school_years, $shift, $division)
  {
    $shift_division_reports_by_career = array();

    if ($division != "")
    {
      $division = DivisionPeer::retrieveByPK($division);
    }

    foreach ($career_school_years as $csy)
    {
      $shift_division_reports = array();

      if ($shift == "")
      {
        foreach (ShiftPeer::doSelect(new Criteria()) as $s)
        {
          $filters = array('shift' => $s->getId(), 'career_school_year' => $csy->getId());

          if ($division == "")
          {
            foreach (DivisionStudentPeer::doSelectDivisionsForCareerSchoolYearAndShift($csy, $s) as $d)
            {
              $title = 'Estudiantes en el turno ' . strtolower($s) . ', división ' . $d;
              $filters['division'] = $d->getId();
              $shift_division_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftAndDivision($csy, $s, $d), $filters);
            }
          }
          else
          {
            $title = 'Estudiantes en el turno ' . strtolower($s) . ', división ' . $division;
            $filters = array('shift' => $s->getId(), 'division' => $division->getId(), 'career_school_year' => $csy->getId());
            $shift_division_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftAndDivision($csy, $s, $division), $filters);
          }
        }
      }
      else
      {
        $title = 'Estudiantes en el turno ' . strtolower(ShiftPeer::retrieveByPK($shift)) . ', división ' . $division;
        $filters = array('shift' => ShiftPeer::retrieveByPK($shift)->getId(), 'division' => $division->getId(), 'school_year' => $csy->getSchoolYearId(), 'career_school_year' => $csy->getId());
        $shift_division_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftAndDivision($csy, ShiftPeer::retrieveByPK($shift), $division), $filters);
      }

      $shift_division_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $shift_division_reports;
    }
    return $shift_division_reports_by_career;
  }

  private function generateYearReports($career_school_years, $year)
  {
    $year_reports_by_career = array();

    foreach ($career_school_years as $csy)
    {
      $year_reports = array();
      if ($year == "")
      {
        for ($y = 1; $y <= $csy->getCareer()->getQuantityYears(); $y++)
        {
          $title = 'Estudiantes en el año N°' . $y . ' de la carrera';
          $filters = array('year' => $y, 'career_school_year' => $csy->getId(), 'school_year' => $this->school_year->getId());
          $year_reports[] = $this->generateReportArray($title, StudentCareerSchoolYearPeer::doCountForCareerSchoolYearAndYear($csy, $y), $filters);
        }
      }
      else
      {
        $title = 'Estudiantes en el año N°' . $year . ' de la carrera';
        $filters = array('year' => $year, 'career_school_year' => $csy->getId(), 'school_year' => $this->school_year->getId());
        $year_reports[] = $this->generateReportArray($title, StudentCareerSchoolYearPeer::doCountForCareerSchoolYearAndYear($csy, $year), $filters);
      }

      $year_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $year_reports;
    }
    return $year_reports_by_career;
  }

  private function generateYearShiftReports($career_school_years, $year, $shift)
  {
    $year_shift_reports_by_career = array();

    foreach ($career_school_years as $csy)
    {
      $year_shift_reports = array();
      if ($shift == "")
      {
        foreach (ShiftPeer::doSelect(new Criteria()) as $s)
        {
          $filters = array('shift' => $s->getId(), 'career_school_year' => $csy->getId());

          if ($year == "")
          {
            for ($y = 1; $y <= $csy->getCareer()->getQuantityYears(); $y++)
            {
              $filters['year'] = $y;
              $title = 'Estudiantes en el turno ' . strtolower($s) . ' y año N°' . $y . ' de la carrera';
              $year_shift_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountStudentsForCareerSchoolYearShiftAndYear($csy, $s, $y), $filters);
            }
          }
          else
          {
            $filters = array('shift' => $s->getId(), 'year' => $year, 'career_school_year' => $csy->getId(), 'school_year' => $this->school_year->getId());
            $title = 'Estudiantes en el turno ' . strtolower($s) . ' y año N°' . $year . ' de la carrera';
            $year_shift_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountStudentsForCareerSchoolYearShiftAndYear($csy, $s, $year), $filters);
            //$year_shift_reports[] = $this->generateReportArray($title, StudentCareerSchoolYearPeer::doCountForCareerSchoolYearAndShiftAndYear($csy, $s, $year), $filters);
          }
        }
      }
      else
      {
        $filters = array('shift' => ShiftPeer::retrieveByPK($shift)->getId(), 'year' => $year, 'career_school_year' => $csy->getId(), 'school_year' => $this->school_year->getId());
        $title = 'Estudiantes en el turno ' . strtolower(ShiftPeer::retrieveByPK($shift)) . ' y año N°' . $year . ' de la carrera';
        $year_shift_reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountStudentsForCareerSchoolYearShiftAndYear($csy, ShiftPeer::retrieveByPK($shift), $year), $filters);
      }

      $year_shift_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $year_shift_reports;
    }
    return $year_shift_reports_by_career;
  }

  private function generateYearShiftDivisionReports($career_school_years, $year, $shift, $division)
  {
    $year_shift_division_reports_by_career = array();
    foreach ($career_school_years as $csy)
    {
      $reports = array();
      if ($division != "")
      {
        $division = DivisionPeer::retrieveByPK($division);
      }

      if ($shift == "")
      {
        foreach (ShiftPeer::doSelect(new Criteria()) as $s)
        {
          $filters = array('shift' => $s->getId(), 'career_school_year' => $csy->getId());
          if ($year == "")
          {
            for ($y = 1; $y <= $csy->getCareer()->getQuantityYears(); $y++)
            {
              $filters['year'] = $y;
              if ($division == "")
              {
                foreach (DivisionStudentPeer::doSelectForCareerSchoolYearShiftAndYear($csy, $s, $y) as $d)
                {
                  $filters['division'] = $d->getId();
                  $title = 'Estudiantes en el turno ' . strtolower($s) . ', año N°' . $y . ' de la carrera y división ' . $d;
                  $reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftYearAndDivision($csy, $s, $y, $d), $filters);
                }
              }
              else //la division no fue nula
              {
                $filters['division'] = $division->getId();
                $title = 'Estudiantes en el turno ' . strtolower($s) . ', año N°' . ' ' . $y . ' ' . ' de la carrera y división ' . ' ' . $division;
                $reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftYearAndDivision($csy, $s, $y, $division), $filters);
              }
            }
          }
          else //el año no fue nulo
          {
            $filters['year']=$year;
            if ($division == "")
            {
              foreach (DivisionStudentPeer::doSelectForCareerSchoolYearShiftAndYear($csy, $s, $year) as $d)
              {
                $filters['division'] = $d->getId();
                $title = 'Estudiantes en el turno ' . strtolower($s) . ', año N°' . $year . ' de la carrera y división ' . $d;
                $reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftYearAndDivision($csy, $s, $year, $d), $filters);
              }
            }
            else //la division no fue nula
            {
              $filters['division'] = $division->getId();
              $title = 'Estudiantes en el turno ' . strtolower($s) . ', año N°' . $year . ' de la carrera y división ' . $division;
              $reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftYearAndDivision($csy, $s, $year, $division), $filters);
            }
          }
        }
      }
      else //el turno no fue nulo, por lo tanto nada es nulo
      {
        $shift = ShiftPeer::retrieveByPK($shift);
        $filters = array('shift' => $shift->getId(), 'year' => $year, 'division' => $division->getId(), 'career_school_year' => $csy->getId());
        $title = 'Estudiantes en el turno ' . strtolower($shift) . ', año N°' . $year . ' de la carrera y división ' . $division;
        $reports[] = $this->generateReportArray($title, DivisionStudentPeer::doCountForCareerSchoolYearShiftYearAndDivision($csy, $shift, $year, $division), $filters);
      }

      $year_shift_division_reports_by_career['Carrera: ' . $csy->getCareer()->getCareerName()] = $reports;
    }
    return $year_shift_division_reports_by_career;
  }

  public function executeEdit(sfWebRequest $request)
  {
    throw new sfError404Exception();
  }

  public function executeNew(sfWebRequest $request)
  {
    throw new sfError404Exception();
  }

  public function executeShow(sfWebRequest $request)
  {
    throw new sfError404Exception();
  }

  public function executeIndex(sfWebRequest $request)
  {
    if ($this->getUser()->getReferenceFor('student_stats'))
    {
      parent::executeIndex($request);
    }
    else
      $this->redirect('student_stats/filterForStudentStats');
  }

  public function preExecute()
  {
    parent::preExecute();
    ini_set('max_execution_time', 0);
  }

  public function executeStudentReportsToPDF(sfWebRequest $request)
  {
    $this->params = $request->getGetParameters();

    $this->generateStudentReports($this->params);

    $this->setLayout('cleanLayout');
  }

}
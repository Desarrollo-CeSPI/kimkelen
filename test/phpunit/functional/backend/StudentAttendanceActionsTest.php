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
require_once dirname(__FILE__).'/../../bootstrap/functional.php';

class functional_backend_StudentAttendanceActionsTest extends BaseFunctionalTestCase
{
  const COURSE_1 = "1 A Inglés";           //asist. x DIA | 2 alumnos: 1) regular en todos los periodos; 2) libre en T1, casi libre en T2, regular en T3
  const COURSE_2 = "2 D Matemática";       //asist. x DIA | Solo 1 alumno, sin ausencias
  const COURSE_3 = "1 B Educación Física"; //asist. x materia | periodos SIN CONFIGURAR
  const COURSE_4 = "1 A Educación Física"; //asist. x materia | periodos configurados correctamente

  protected function getApplication()
  {
    return 'backend';
  }

  public function testAttendanceByPeriodFormIsWellDrawn()
  {
    $this->goToAttendanceByDay('10/09/2012');
    
    $browser = $this->getBrowser();

    $browser->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#sf_admin_container h1:contains("Cargar asistencias para")')->
        checkElement('td.period', 3)->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(7)',  '/SJ/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(8)',  '/J/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(9)',  '/T/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(10)',  '/SJ/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(11)',  '/J/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(12)',  '/T/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(13)',  '/SJ/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(14)',  '/J/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(15)',  '/T/')->
        
      end()
    ;
  }

  public function testAttendanceByYearFormIsWellDrawn()
  {
    $this->goToAttendanceByDay('10/09/2012');
  
    $browser = $this->getBrowser();
    $browser->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#sf_admin_container h1:contains("Cargar asistencias para")')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(7)',  '/SJ/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(8)',  '/J/')->
        checkElement('#student_attendance thead tr:nth-child(3) td:nth-child(9)',  '/T/')->
      end()
    ;
  }

  public function testTotalsHaveTheCorrectCssClass()
  {
    //fixture has: the first student is free in first trimester, almost free in second, and regular in the third.
    //             the second student, all regular.

    $notFreeCssClass     = "attendance_regular";
    $almostFreeCssClass  = "almost_free";
    $freeCssClass        = "free";

    $this->goToAttendanceByDay('10/09/2012');
    
    $this->getBrowser()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(7)[class*="'.$freeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(8)[class*="'.$freeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(9)[class*="'.$freeCssClass.'"]')->

        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(10)[class*="'.$almostFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(11)[class*="'.$almostFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(12)[class*="'.$almostFreeCssClass.'"]')->

        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(13)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(14)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(1) td:nth-child(15)[class*="'.$notFreeCssClass.'"]')->
        
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(7)[class*="'.$freeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(8)[class*="'.$freeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(9)[class*="'.$freeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(10)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(11)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(12)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(13)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(14)[class*="'.$notFreeCssClass.'"]')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(15)[class*="'.$notFreeCssClass.'"]')->
      end()
    ;
  
  }

  public function testAttendanceBySubjectIsSuccessfullySaved()
  {
    $browser = $this->getBrowser();
    $course  = $this->getCourse(self::COURSE_4);
    $day     = '10/09/2012';
    $form    = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();

    $this->goToAttendanceBySubject($day, $course);

    $student = $course->getStudents();
    $student = array_shift($student);

    $present  = $this->getSubjectAbsencesType("Presente");
    $third    = $this->getSubjectAbsencesType('1/3 falta');



    foreach(range(1,5) as $i)
    {
      $post["student_attendance_".$student->getId()."_$i"] = $third->getId();
    }

    $browser->
      deselect('multiple_student_attendance_day_disabled_1')->
      deselect('multiple_student_attendance_day_disabled_2')->
      deselect('multiple_student_attendance_day_disabled_3')->
      deselect('multiple_student_attendance_day_disabled_4')->
      deselect('multiple_student_attendance_day_disabled_5')->

      click('Guardar', array("multiple_student_attendance" => $post))->
 
      with('form')->begin()->
        hasErrors(false)->
        hasGlobalError(false)->
      end()->
        
      with('response')->begin()->
          checkElement('#flash_notice', '/El elemento fue actualizado satisfactoriamente./')->
      end()
    ;
  }

  public function testAttendanceByDayIsSuccessfullySaved()
  {
    $browser = $this->getBrowser();
    $course  = $this->getCourse(self::COURSE_1);
    $day     = '10/09/2012';
    $form    = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();

    $this->goToAttendanceByDay($day);
  
    $student  = $course->getStudents();
    $student  = array_shift($student);

    $present  = $this->getDayAbsencesType("Presente");
    $third    = $this->getDayAbsencesType('1/3 falta');

    foreach(range(1,5) as $i)
    {
      $absences["student_attendance_".$student->getId()."_$i"] = $present->getId();
    }
    
    $browser->
      click('Guardar', array("multiple_student_attendance" => $absences))->
 
      with('form')->begin()->
        hasErrors(false)->
        hasGlobalError(false)->
      end()->
        
      with('response')->begin()->
          checkElement('#flash_notice', '/El elemento fue actualizado satisfactoriamente./')->
      end()
    ;
  }

  public function testThirdAbsenceIsRoundedTo1WhenReached3Absences()
  {
    $this->checkTotalAbsencesRoundsTo1($this->getDayAbsencesType('1/3 falta'), 3);
  }

  public function testSixthAbsenceIsRoundedTo1WhenReached6Absences()
  {
    $this->checkTotalAbsencesRoundsTo1($this->getDayAbsencesType('1/6 falta'), 6);
  }

  public function testSeventhAbsenceIsRoundedTo1WhenReached7Absences()
  {
    $this->checkTotalAbsencesRoundsTo1($this->getDayAbsencesType('1/7 falta'), 7);
  }

  public function testEighthAbsenceIsRoundedTo1WhenReached8Absences()
  {
    $this->checkTotalAbsencesRoundsTo1($this->getDayAbsencesType('1/8 falta'), 8);
  }

  public function testNinthAbsenceIsRoundedTo1WhenReached9Absences()
  {
    $this->checkTotalAbsencesRoundsTo1($this->getDayAbsencesType('1/9 falta'), 9);
  }

  public function checkTotalAbsencesRoundsTo1($absenceType, $count)
  {
    $course  = $this->getCourse(self::COURSE_2);
    $student = $course->getStudents();
    $student = array_pop($student);
    $day = '02/07/2012';

    foreach (range(1, $count) as $i)
    {
      $absence = $this->createStudentAttendance($student, $day, $course, $absenceType);

      $this->goToAttendanceByDay($day, $course);

      $this->getBrowser()->
        with('response')->begin()->
          checkElement('#student_attendance tbody tr:first-child td:nth-child(7)', '/\d+(\.\d{1,2})?/')->
          checkElement('#student_attendance tbody tr:first-child td:nth-child(8)', '/\d+(\.\d{1,2})?/')->
          checkElement('#student_attendance tbody tr:first-child td:nth-child(9)', '/\d+(\.\d{1,2})?/')->
        end()
      ;

      $day = date('d/m/Y', strtotime($absence->getDay('Y-m-d') . ' + 1 day'));
    }

    $this->getBrowser()->
      with('response')->checkElement('#student_attendance tbody tr:first-child td:nth-child(9)', '1')
    ;
  }

  public function testJustificatedTotalsAreCorrect()
  {
    if(sfConfig::get('app_school_beahviour') == 'cnba')
    {
      $this->markTestIncomplete("specific beahviour testing is not available yet");
    }

    $course = $this->getCourse(self::COURSE_1);

    $this->goToAttendanceByDay('10/09/2012', $course);

    $this->getBrowser()->
      with('response')->begin()->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(7)', '0')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(8)', '2')->
        checkElement('#student_attendance tbody tr:nth-child(2) td:nth-child(9)', '2')->
      end()
    ;
  }

  public function testFormErrorWhenAttendanceDateIsOutOfPeriod()
  {
    $course  = $this->getCourse(self::COURSE_4);
    $day     = '05/02/2013';
    $student = $course->getStudents();
    $student = array_shift($student);
    $third   = $this->getSubjectAbsencesType('1/3 falta');

    $post  = array();
    foreach(range(1,5) as $i)
    {
      $post["student_attendance_".$student->getId()."_$i"] = $third->getId();
    }

    $this->goToAttendanceBySubject($day, $course);

    $this->getBrowser()->
      deselect('multiple_student_attendance_day_disabled_1')->
      deselect('multiple_student_attendance_day_disabled_2')->
      deselect('multiple_student_attendance_day_disabled_3')->
      deselect('multiple_student_attendance_day_disabled_4')->
      deselect('multiple_student_attendance_day_disabled_5')->

      click('Guardar', array("multiple_student_attendance" => $post))->
      with('form')->begin()->
        hasGlobalError()->
      end()->
      with('response')->begin()->
        checkElement('.error_list li', '/Curso: La falta ingresada no pertenece a un periodo valido para el curso/')->
      end()
    ;
  }

  public function testFormErrorWhenCourseSubjectHasNoPeriodsConfigurated()
  {
    //course 3 has no period configuration
    $course = $this->getCourse(self::COURSE_3);
    $this->goToAttendanceBySubject('10/09/2012', $course);
    $this->getBrowser()-> 
      with('response')->
        checkElement('#student_attendance tr:nth-child(3) td:nth-child(7)', '#El curso/Division no posee una configuracion de asistencias#')
    ;
  }

  public function testDisabledDayIsNotSubmittedNorSaved()
  {
    $course  = $this->getCourse(self::COURSE_2);
    $student = $course->getStudents();
    $student = array_shift($student);
    $CSY     = $course->getCareerSchoolYear();
    $day     = '10/09/2012';
   
    $this->goToAttendanceByDay($day, $course);

    $this->getBrowser()->
      select('multiple_student_attendance_day_disabled_1')->
      select('multiple_student_attendance_day_disabled_2')->
      select('multiple_student_attendance_day_disabled_3')->
      select('multiple_student_attendance_day_disabled_4')->
      select('multiple_student_attendance_day_disabled_5')->

      click('Guardar')->
      
      with('form')->begin()->
        hasErrors(false)->
        hasGlobalError(false)->
      end()->
        
      with('response')->begin()->
        checkElement('#flash_notice', '/El elemento fue actualizado satisfactoriamente./')->
      end()->

      with('propel')->begin()->
        check(
          'StudentAttendance', 
          array(
            'career_school_year_id' => $CSY->getId(),
            'student_id'            => $student->getId(),
            'day'                   => $day),
          false)->
      end()
    ;
  }

  public function testFormErrorWhenInexistentAbsenceTypeIsSubmitted()
  {
    $course  = $this->getCourse(self::COURSE_2);
    $CSY     = $course->getCareerSchoolYear();
    $day     = '10/09/2012';
   
    $student = $course->getStudents();
    $student = array_shift($student);

    $absenceTypeId = $this->getNonExistentAbsenceTypeId();
    $this->goToAttendanceByDay($day, $course);

    $post = array("student_attendance_".$student->getId()."_1" => $absenceTypeId);
    $this->addCSRF($post, SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm());

    $this->getBrowser()->
      deselect('multiple_student_attendance_day_disabled_1')->
      select('multiple_student_attendance_day_disabled_2')->
      select('multiple_student_attendance_day_disabled_3')->
      select('multiple_student_attendance_day_disabled_4')->
      select('multiple_student_attendance_day_disabled_5')->

      click('Guardar', array('multiple_student_attendance' => array("student_attendance_".$student->getId()."_1" => $absenceTypeId)))->
      
      with('form')->begin()->
        hasErrors(true)->
        isError("student_attendance_".$student->getId()."_1")->
      end()
    ;
  }

  public function testFreeStudentRedirectsToStudentFreeWithProperParameters()
  {
    $course = $this->getCourse(self::COURSE_1);
    $student = $course->getStudents();
    $student = array_shift($student);

    $this->goToAttendanceByDay('10/09/2012', $course);

    $this->getBrowser()->
      click('Dejar libre', array(), array('position'=>1))->

      with('request')->begin()->
        isParameter('module', 'student_attendance')->
        isParameter('action', 'free')->
      end()->

      isRedirected()->
      followRedirect()->

      with('request')->begin()->
        isParameter('module', 'student_free')->
        isParameter('action', 'index')->
      end()->
      with('user')->begin()->
        isAttribute('student_id', $student->getId())->
      end()
    ;
  }

  public function testReincorporateStudentRedirectsToStudentReincorporationWithProperParameters()
  {
    $course = $this->getCourse(self::COURSE_1);
    $student = $course->getStudents();
    $student = array_shift($student);

    $this->goToAttendanceByDay('10/09/2012', $course);

    $this->getBrowser()->
      click('Reincorporación', array(), array('position'=>1))->
      with('request')->begin()->
        isParameter('module', 'student_attendance')->
        isParameter('action', 'reincorporate')->
      end()->

      isRedirected()->
      followRedirect()->

      with('request')->begin()->
        isParameter('module', 'student_reincorporation')->
        isParameter('action', 'index')->
      end()->
      with('user')->begin()->
        isAttribute('student_id', $student->getId())->
      end()
    ;
  }

  protected function goToAttendanceByDay($date, $course = self::COURSE_1)
  {
    if(!$course instanceof Course)
    {
      $course = $this->getCourse($course);
    }

    $careerSchoolYear = $course->getCareerSchoolYear();
    $year             = $course->getYear();
    $division         = $course->getDivision();

    $post = array("multiple_student_attendance" => array(
        'career_school_year_id' => $careerSchoolYear->getId(),
        'year'                  => $year,
        'division_id'           => $division->getId(),
        'day'                   => $date,
     ));

    $this->addCSRF($post['multiple_student_attendance'], 'SelectValuesForAttendanceDayForm');

    $this->getBrowser()
        ->post('/inasistencias-por-dia', $post)
        ->with('request')->begin()
            ->isParameter('module', 'student_attendance')
            ->isParameter('action', 'SelectValuesForAttendanceDay')
        ->end()
        ->with('response')->begin()
          ->isStatusCode(200)
          ->checkElement('#sf_admin_container h1', '/Cargar asistencias para/')
        ->end()        
    ;

    return $this;
  }

  protected function goToAttendanceBySubject($date, $course = self::COURSE_3)
  {
    $careerSchoolYear = $course->getCareerSchoolYear();
    $year             = $course->getYear();

    $post = array("multiple_student_attendance" => array(
        'career_school_year_id' => $careerSchoolYear->getId(),
        'year'                  => $year,
        'course_subject_id'     => $course->getId(),
        'day'                   => $date,
    ));

    $this->addCSRF($post['multiple_student_attendance'], 'SelectValuesForAttendanceSubjectForm');

    $this->getBrowser()
        ->post('/inasistencias-por-materia', $post)
        ->with('request')->begin()
            ->isParameter('module', 'student_attendance')
            ->isParameter('action', 'SelectValuesForAttendanceSubject')
        ->end()
        ->with('response')->begin()
          ->isStatusCode(200)
          ->checkElement('#sf_admin_container h1', '/Cargar asistencias para/')
        ->end()
    ;

    return $this;
  }

  protected function createStudentAttendance($student, $day, $course, $absenceType)
  {
    $absence = new StudentAttendance();
    $absence->setCareerSchoolYearId($course->getCareerSchoolYear()->getId());
    $absence->setStudentId($student->getId());
    $absence->setDay($day);
    $absence->setAbsenceTypeId($absenceType->getId());
    $absence->setValue($absenceType->getValue());

    $absence->save();
    return $absence;
  }

  protected function getNonExistentAbsenceTypeId()
  {
    $c = new Criteria();
    $c->addSelectColumn(AbsenceTypePeer::ID);
    $stmt = AbsenceTypePeer::doSelectStmt($c);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    sort($ids);

    return array_pop($ids) + 1;
  }
/* 
   //
   // utility methods...
   //
  protected function configureAttendanceByYear()
  {
    $course = $this->getCourse(self::COURSE_1);
    $careerSchoolYear = $course->getCareerSchoolYear();

    $yearConfig = $careerSchoolYear->getSubjectConfiguration()->getCareerYearConfiguration($course->getYear());
    $yearConfig->setHasMaxAbsenceByPeriod(0);
    $yearConfig->save();

    return $this;
  }

  protected function setFree($student, $course, $firstTrimester)
  {
    $free = new StudentFree();
    $free->setStudentId($student->getId());
    $free->setCareerSchoolYearPeriodId($firstTrimester->getId());
    $free->setCareerSchoolYearId($course->getCareerSchoolYear()->getId());

    $free->save();

    return $this;
  }

  protected function setAlmostFree($student, $course, $period)
  {
    $absent  = $this->getAbsencesType("Ausente");
    
    $day = $period->getStartAt('Y-m-d');
    $this->createStudentAttendance($student, $day, $course, $absent);

    $day = date('d/m/Y', strtotime($period->getStartAt('Y-m-d') . "+ 1 day"));
    $this->createStudentAttendance($student, $day, $course, $absent);

    return $this;
  }
 */
}
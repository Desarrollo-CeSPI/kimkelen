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

require_once dirname(__FILE__) . '/../lib/equivalenceGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/equivalenceGeneratorHelper.class.php';

/**
 * equivalence actions.
 *
 * @package    sistema de alumnos
 * @subpackage equivalence
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class equivalenceActions extends autoEquivalenceActions {

  public function executeMakeUpEquivalence(sfWebRequest $request) {
    $career_school_year = $this->getRoute()->getObject();
    $this->career_school_year = $career_school_year;
    $this->student = $this->getUser()->getAttribute('student');
    $this->career = $career_school_year->getCareer();
    $this->school_year = null;
    $this->forms = array();
    $this->years = array();
    $this->career_subject_school_years = array();
    for ($y = 1; $y <= $this->career->getQuantityYears(); $y++):
      $this->years[] = $y;
      $this->career_subject_school_years[$y] = $this->career->getCareerSubjectsForYear($y, true);
      foreach ($this->career_subject_school_years[$y] as $career_subject):

        $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($career_subject->getId(), $this->student->getId());

        $this->forms[$career_subject->getId()] = new EquivalenceForm($student_approved_career_subject);
        $this->forms[$career_subject->getId()]->setCareerSubjectAndStudent($career_subject, $this->student);

      endforeach;
    endfor;

    $this->module = $this->getModuleName();
  }

  public function executeUpdateEquivalence(sfWebRequest $request) {
    $parametrs = $request->getPostParameters();
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($parametrs['career_school_year_id']);
    $this->career = $this->career_school_year->getCareer();
    $this->career_subject_school_years = array();
    $this->years = array();
    $this->forms = array();
    for ($y = 1; $y <= $this->career->getQuantityYears(); $y++):
      $this->years[] = $y;
      $this->career_subject_school_years[$y] = $this->career->getCareerSubjectsForYear($y, true);
    endfor;
    unset($parametrs['_save']);
    unset($parametrs['career_school_year_id']);
    $valid = true;

    foreach ($parametrs as $parameter) {

      $career_subject_id = $parameter['career_subject_id'];
      $student_id = $parameter['student_id'];
      $school_year_id = $parameter['school_year'];

      $career_subject = CareerSubjectPeer::retrieveByPK($career_subject_id);
      $student = StudentPeer::retrieveByPK($student_id);

      $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($career_subject->getId(), $student->getId());
      $student_approved_career_subject->setSchoolYearId($school_year_id);

      $parameter['career_subject_id'] = $student_approved_career_subject->getCareerSubjectId();

      $this->form = new EquivalenceForm($student_approved_career_subject);
      $this->form->setCareerSubjectAndStudent($career_subject, $student);

      if (isset($parameter['mark']) && ($parameter['mark'] != "")) {
        $this->form->bind($parameter);
        if ($this->form->isValid()) {
          $this->form->save();
        }
        else {
          $valid = false;
        }
      }
      $this->form = new EquivalenceForm($student_approved_career_subject);
      $this->form->setCareerSubjectAndStudent($career_subject, $student);
      $this->forms[$career_subject->getId()] = $this->form;
      $parameter['career_subject_id'] = $career_subject_id;
    }
    if ($valid) {
      $this->getUser()->setFlash('notice', 'subjects are updated correctly');
    }
    else {
      $this->setProcessFormErrorFlash();
    }
    $this->module = $this->getModuleName();
    $this->setTemplate('makeUpEquivalence');
  }

}
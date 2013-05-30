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

/**
 * Description of EquivalenceForm
 *
 * @author Ivan Muller
 */
class EquivalenceForm extends BaseStudentApprovedCareerSubjectForm {

  public function configure() {
    unset($this['created_at'], $this['updated_at'], $this['student_id'], $this['is_equivalence'], $this['school_year_id'], $this['career_subject_id']);

    $options = array(
      'min' => $this->getMinimumMark(),
      'max' => $this->getMaximumMark(),
      'required' => false
    );

    $messages = array(
      'min' => 'La calificación debe ser al menos %min%.',
      'max' => 'La calificación debe ser a lo sumo %max%.',
      'invalid' => 'El valor ingresado es inválido.'
    );

    $this->getWidget('mark')->setAttribute('class', 'mark');
    $this->setValidator('mark', new sfValidatorNumber($options, $messages));

    $this->setWidget('school_year', new sfWIdgetFormPropelChoice(array('model' => 'SchoolYear', 'add_empty' => true)));
    $this->setValidator('school_year', new sfValidatorPropelChoice(array('model' => 'SchoolYear', 'required' => true)));

    $this->setWidget('student_id', new sfWidgetFormInputHidden());
    $this->setValidator('student_id', new sfValidatorPass());

    $this->setWidget('career_subject_id', new sfWidgetFormInputHidden());
    $this->setValidator('career_subject_id', new sfValidatorPass());
  }

  public function getCareerStudentCriteria() {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getObject()->getStudentId());
    $c->addJoin(CareerStudentPeer::CAREER_ID, CareerPeer::ID, Criteria::INNER_JOIN);
    return $c;
  }

  public function updateOptionalCareerSubject($widget, $value) {
    $school_year_id = array_shift($value);
    $criteria = new Criteria();
    if ($school_year_id != "") {
      $career_subject = $this->getObject()->getCareerSubject();
      $school_year = SchoolYearPeer::retrieveByPK($school_year_id);
      $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($career_subject, $school_year);
      $opcions = array();

      foreach ($career_subject_school_year->getChoices() as $optional_career_subject_school_year) {
        $cs = CareerSubjectPeer::retrieveByCareerSubjectSchoolYearId($optional_career_subject_school_year->getChoiceCareerSubjectSchoolYearId());
        $opcions[] = $cs->getId();
      }
      //$opcions es un arreglo con todas las posibles optativas
      $criteria->add(CareerSubjectPeer::ID, $opcions, Criteria::IN);
      $widget->getOption('widget')->setOption('criteria', $criteria);
    }
  }

  public function setCareerSubjectAndStudent($career_subject, $student) {
    $this->widgetSchema->setNameFormat('equivalence_' . $career_subject->getId() . '[%s]');
    $student_approved_career_subject = $this->getObject();

    $this->setDefault('student_id', $student->getId());
    $this->setDefault('career_subject_id', $career_subject->getId());
    //si ya existe el objeto se cargan los valores
    if ($student_approved_career_subject) {
      $this->setDefault('school_year', $student_approved_career_subject->getSchoolYearId());
      $this->setDefault('mark', $student_approved_career_subject->getMark());
    }

    //nombre de la materia
    $this->getWidget('mark')->setLabel($career_subject->getSubject());
    //Si la materia tiene opciones
    if ($career_subject->getHasOptions()) {
      //Ya esta cargada la materia optativa ponemos todos los datos en mtWidgetFormPlain para read only
      if ($career_subject->getId() != $student_approved_career_subject->getCareerSubjectId()) {
        $this->setWidget('school_year', new mtWidgetFormPlain(array('object' => $student_approved_career_subject->getSchoolYear(), 'add_hidden_input' => true, 'use_retrieved_value' => false)));
        $this->setDefault('school_year', $student_approved_career_subject->getSchoolYearId());
        $this->setWidget('mark', new mtWidgetFormPlain());
        $this->setWidget('optional', new mtWidgetFormPlain(array('object' => $student_approved_career_subject->getCareerSubject())));
        $this->setDefault('optional', $student_approved_career_subject->getCareerSubjectId());
      }//si la optativa NO esta cargada,usamos Ajax para selecciona la optativa con respecto al año
      else {
        $widget = new sfWIdgetFormPropelChoice(array('model' => 'CareerSubject'));
        $this->setWidget('optional', new dcWidgetFormJQueryDependence(array
            ('widget' => $widget,
            'observed_id' => array("equivalence_" . $career_subject->getId() . "_school_year"),
            'on_change' => array($this, 'updateOptionalCareerSubject')))
        );
        $this->setValidator('optional', new sfValidatorPass());
      }
    }
    //NO tiene materia optativa y Ya esta cargada la nota ponemos la nota y el año en mtWidgetFormPlain para read only
    elseif ($student_approved_career_subject->getMark() != "") {
      $this->setWidget('school_year', new mtWidgetFormPlain(array('object' => $student_approved_career_subject->getSchoolYear(), 'add_hidden_input' => true, 'use_retrieved_value' => false)));
      $this->setDefault('school_year', $student_approved_career_subject->getSchoolYearId());
      $this->setWidget('mark', new mtWidgetFormPlain());
    }
  }

  protected function doSave($con = null) {
    $parames = $this->getValues();
    if (isset($parames["optional"])) {
      $this->getObject()->setCareerSubjectId($parames["optional"]);
      $this->getObject()->setIsEquivalence(true);
      $this->getObject()->setMark($parames["mark"]);
      $this->getObject()->save();
    }
    else {
      parent::doSave($con);
    }
  }

  protected function getMinimumMark() {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark();
  }

  protected function getMaximumMark() {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMaximumMark();
  }

}
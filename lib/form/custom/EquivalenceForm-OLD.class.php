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
 * @author gramirez
 */
class EquivalenceFormOLD extends BaseStudentApprovedCareerSubjectForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at'], $this['student_id'], $this['is_equivalence'], $this['school_year_id']);

    if ($this->getObject()->isNew())
    {
      $criteria = $this->getCareerStudentCriteria();
      $this->setWidget('career_id', new sfWidgetFormPropelChoice(array('model' => 'Career', 'criteria' => $criteria, 'add_empty' => true)));
      $this->setValidator('career_id', new sfValidatorPropelChoice(array('model' => 'Career', 'criteria' => $criteria)));

      $widget = new sfWIdgetFormPropelChoice(array('model' => 'CareerSubject'));
      $this->setWidget('career_subject_id', new dcWidgetAjaxDependencePropel(array(
        'dependant_widget' => $widget,
        'observe_widget_id' => 'equivalence_career_id',
        'related_column' => 'career_id',
        'get_observed_value_callback' => array($this, 'updateCareerSubject')
      )));
      $this->getWidgetSchema()->moveField('career_id','before', 'career_subject_id');
      $this->getWidget('career_subject_id')->setLabel('Subject');
    }
    else
    {
      //Solo se puede editar la nota.
      unset($this['career_subject_id']);

      $this->setWidget('career_id', new mtWidgetFormWrapper(array('content' => $this->getObject()->getCareerSubject()->getCareer())));
      $this->setValidator('career_id', new sfValidatorPass());
      $this->setWidget('career_subject', new mtWidgetFormWrapper(array('content' => $this->getObject()->getCareerSubject()->getSubject())));
      $this->setValidator('career_subject', new sfValidatorPass());

      $this->getWidgetSchema()->moveField('career_id','before', 'career_subject');
      $this->getWidgetSchema()->moveField('mark','after', 'career_subject');
    }

    $options = array(
      'min'      => $this->getMinimumMark(),
      'max'      => $this->getMaximumMark(),
      'required' => false
    );

    $messages = array(
      'min'     => 'La calificación debe ser al menos %min%.',
      'max'     => 'La calificación debe ser a lo sumo %max%.',
      'invalid' => 'El valor ingresado es inválido.'
    );
    $this->getWidget('mark')->setAttribute('class','mark');
    $this->setValidator('mark', new sfValidatorNumber($options, $messages));


    
    $this->widgetSchema->setNameFormat('equivalence[%s]');
  }

  public function getCareerStudentCriteria()
  {    
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getObject()->getStudentId());
    $c->addJoin(CareerStudentPeer::CAREER_ID, CareerPeer::ID, Criteria::INNER_JOIN);
    return $c;
  }

  public function updateCareerSubject($widget , $value)
  {
    $criteria = SchoolBehaviourFactory::getInstance()->getCareerSubjectsForEquivalenceCriteria($value, $this->getObject()->getStudent());

    $widget->setOption('criteria', $criteria);
  }

  protected function getMinimumMark()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark();
  }

  protected function getMaximumMark()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getMaximumMark();
  }
}
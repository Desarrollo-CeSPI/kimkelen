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
 * Correlative form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */

class CorrelativeForm extends BaseCorrelativeForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    unset($this['created_at']);

    /* Career subject to which we are assigning correlatives */
    $this->setWidget('career_subject_id', new sfWidgetFormReadOnly(array(
      'plain'          => false,
      'value_callback' => array('CareerSubjectPeer', 'retrieveByPk')
    )));
    $this->widgetSchema->setLabel('career_subject_id', 'Materia');


    /* Career subjects available to select as correlatives */
    $this->setWidget('correlative_career_subject_id', new sfWidgetFormPropelChoice(array(
      'model'            => 'CareerSubject',
      'multiple'         => true,
      'criteria'         => CareerSubjectPeer::getAvailableCarrerSubjectsAsCorrelativesCriteriaFor($this->getObject()->getCareerSubjectRelatedByCareerSubjectId()),
      'renderer_class'   => 'csWidgetFormSelectDoubleList',
    )));
    $this->setValidator('correlative_career_subject_id', new sfValidatorPropelChoice(array(
      'model'    => 'CareerSubject',
      'multiple' => true,
      'criteria' => CareerSubjectPeer::getAvailableCarrerSubjectsAsCorrelativesCriteriaFor($this->getObject()->getCareerSubjectRelatedByCareerSubjectId()),
      'required' => false
    )));

    $this->widgetSchema->setLabel('correlative_career_subject_id', 'Correlativas');

  }
  
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['correlative_career_subject_id']))
    {
      $values = array();

      foreach ($this->object->getCareerSubjectRelatedByCareerSubjectId()->getCorrelativesRelatedByCareerSubjectId() as $correlative)
      {
        $values[] = $correlative->getCorrelativeCareerSubjectId();
      }

      $this->setDefault('correlative_career_subject_id', $values);
    }
  }

  protected function doSave($con = null)
  {
    // Do NOT call parent::doSave($con) as this form has been
    // hacked to work as a container of many Correlative objects,
    // instead of just one
    
    $this->saveCorrelativeCareerSubjectId($con);
  }

  public function saveCorrelativeCareerSubjectId($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['correlative_career_subject_id']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    /* First try to delete old correlatives */
    $criteria = new Criteria();
    $criteria->add(CorrelativePeer::CAREER_SUBJECT_ID, $this->getObject()->getCareerSubjectId());
    CorrelativePeer::doDelete($criteria, $con);

    /* Now set new values */
    $values = $this->getValue('correlative_career_subject_id');

    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $correlative = new Correlative();
        $correlative->setCareerSubjectId($this->getObject()->getCareerSubjectId());
        $correlative->setCorrelativeCareerSubjectId($value);
        $correlative->save($con);
      }
    }
  }
  
}
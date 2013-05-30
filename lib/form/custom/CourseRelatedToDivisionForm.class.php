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
 * Description of CourseRelateToDivisionForm
 *
 * @author gramirez
 */
class CourseRelatedToDivisionForm extends BaseFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $criteria = new Criteria();
    $criteria->add(DivisionPeer::YEAR, $this->getObject()->getYear());
    $criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $this->getObject()->getCareerSchoolYear()->getId());

    $this->setWidget('related_division_id', new sfWidgetFormPropelChoice(array('model' => 'Division', 'criteria' => $criteria, 'add_empty' => true)));
    $this->setValidator("related_division_id" , new sfValidatorPropelChoice(array("model" => "Division",'required' => true )));

    $this->setDefault("related_division_id", $this->getObject()->getRelatedDivisionId());
    $this->getWidgetSchema()->setNameFormat('course_related_to_division[%s]');
  }

  public function getModelName()
  {
    return 'Course';
  }

  protected function doSave($con = null)
  {
    $course = $this->getObject();

    $course->setRelatedDivisionId($this->values['related_division_id']);

    $course->save($con);
  }
}
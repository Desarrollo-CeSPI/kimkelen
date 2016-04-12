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
 * CareerStudent form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class CareerStudentStatusForm extends CareerStudentForm
{
  public function configure()
  {
    $this->configureUnset();
    $this->configureWidgets();
    $this->configureJavascripts();
  }

  public function configureUnset()
  {
    unset ($this['student_id']);
    unset ($this['created_at']);
    unset ($this['student_id']);
  }

  public function configureWidgets()
  {
    $cs = new CareerStudentStatus();
    $this->setWidget('status',new sfWidgetFormChoice(array('choices' => $cs->getOptions(true,$no_graduate = true))));
    $this->getWidget('status')->setDefault(null);
    
    //filtro solo las carreras en que esta inscripto
    $c = new Criteria();
    $c->add(StudentPeer::ID, $this->getObject()->getStudentId());
    $c->add(CareerStudentPeer::STATUS, CareerStudentStatus::CS_GRADUATE, Criteria::NOT_EQUAL);
    $c->addJoin(CareerStudentPeer::STUDENT_ID,StudentPeer::ID);
    $c->addJoin(CareerStudentPeer::CAREER_ID,CareerPeer::ID);

    $this->setWidget('career_id', new sfWidgetFormPropelChoice(array('model' => 'Career','add_empty' => false, 'criteria' => $c)));
    $this->getWidget('career_id')->setLabel('Carrera');

  }

  public function configureJavascripts()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("Url","Javascript"));
    $student_id =  $this->getObject()->getStudentId();
    $this->getWidget('career_id')->setAttribute('onChange',remote_function(array(
      'url'     => 'student/updateCareerStudentStatus',
      'with'    => "'career_id=' + jQuery('#career_student_career_id').val() + '&student_id= $student_id '",
      'script'  => true,
      'update'  => 'javascript_div',
    )));
  }
}

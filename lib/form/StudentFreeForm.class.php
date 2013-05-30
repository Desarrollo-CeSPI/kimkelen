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
 * StudentFree form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class StudentFreeForm extends BaseStudentFreeForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $this->setWidget('student_id', new sfWidgetFormInputHidden());
    $this->setWidget('is_free', new sfWidgetFormInputHidden());
        
    $this->getWidget('career_school_year_id')->setOption('criteria', CareerSchoolYearPeer::retrieveCurrentForStudentCriteria($this->getObject()->getStudent()));

    $this->getWidget('career_school_year_period_id')->setLabel('Period');
    $this->getWidget('career_school_year_period_id')->setOption('add_empty',true);    
    $this->getWidget('career_school_year_period_id')->setOption('criteria', CareerSchoolYearPeriodPeer::retrieveCurrentsCriteria());
    $this->getWidget('career_school_year_period_id')->setOption('peer_method','retrieveOrdered');

    $this->getWidget('course_subject_id')->setOption('criteria', $this->getCourseSubjectCriteria());
    $this->getWidget('course_subject_id')->setLabel('Course subject');
    $this->getWidgetSchema()->setHelp('course_subject_id', 'Elegir curso en caso de que quede libre para un curso en especial.');

    if ($this->getObject()->isNew())
    {
      $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this , 'validateUnique'))));  
    }    
  }

  public function getCourseSubjectCriteria()
  {
    $criteria = new Criteria();
    $criteria->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getObject()->getStudentId());
    $criteria->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
    CourseSubjectPeer::retrieveCriteriaForCurrentYear($criteria);

    return $criteria;
  }

  public function validateUnique($validator, $values)
  {
    $c = new Criteria();
    $c->add(StudentFreePeer::STUDENT_ID, $values['student_id']);
    $c->add(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $values['career_school_year_period_id']);

    if (!is_null($values['course_subject_id']))
    {
      $c->add(StudentFreePeer::COURSE_SUBJECT_ID, $values['course_subject_id']);
    }
      
    $student_free = StudentFreePeer::doSelectOne($c);

    if(!is_null($student_free))
    {
      throw new sfValidatorError($validator, 'Ya existe esta tupla.');
    }

    return $values;
  }
}
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
 * CnbaStudentDisciplinarySanctionForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class CnbaStudentDisciplinarySanctionForm extends StudentDisciplinarySanctionForm
{
  public function configure()
  {
    $this->configureWidgets();
    $this->configureValidators(); 

    $this->getWidgetSchema()->setHelp('document', 'The file must be of the following types: jpeg, jpg, gif, png, pdf.');
    $this->getWidgetSchema()->moveField('observation', sfWidgetFormSchema::LAST);
    $this->getWidgetSchema()->moveField('document', sfWidgetFormSchema::AFTER, 'responsible_id');
  }

  public function configureValidators()
  {
    $this->setValidator('name', new sfValidatorString(array('required'=> false)));
    $this->setValidator('number', new sfValidatorPass(array('required'=> true)));
    $this->setValidator('value', new sfValidatorNumber(array('required'=> true)));
    $this->setValidator('disciplinary_sanction_type_id', new sfValidatorPropelChoice(array('model' => 'DisciplinarySanctionType', 'column' => 'id', 'required' => true)));
    $this->setValidator('applicant_id', new sfValidatorPropelChoice(array('model' => 'Person', 'column' => 'id', 'required' => false)));
    $this->setValidator('responsible_id', new sfValidatorPropelChoice(array('model' => 'Person', 'column' => 'id', 'required' => false)));
    $this->setValidator('sanction_type_id', new sfValidatorPropelChoice(array('model' => 'SanctionType', 'column' => 'id', 'required' => true)));
    $this->setValidator('request_date', new mtValidatorDateString(array('required'=> true)));
    $this->setValidator('resolution_date', new mtValidatorDateString(array('required'=> true)));
    $this->setValidator('document', new sfValidatorFile(array(
        'path' => StudentDisciplinarySanction::getDocumentDirectory(),
        'max_size' => '2097152',
        'required' => false)));

    $this->getValidator('document')->setOption('mime_categories', array(
      'web_images' => array(
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/gif',
      ),
      'documents' => array(
        'application/pdf'
      )
    ));


    if ($this->getObject()->getDocument())
    {
      $this->setValidator('current_document', new sfValidatorPass(array('required' => false)));
      $this->setValidator('delete_document', new sfValidatorBoolean(array('required' => false)));
    }
  }

  public function configureWidgets()
  {
    $this->setWidget('student_id', new sfWidgetFormInputHidden());
    $this->setWidget('school_year_id', new sfWidgetFormInputHidden());
    $this->setWidget('request_date', new csWidgetFormDateInput());
    $this->setWidget('resolution_date', new csWidgetFormDateInput());

    $this->getWidget('disciplinary_sanction_type_id')->setOption('add_empty', true);
    $this->getWidget('sanction_type_id')->setOption('add_empty', true);

    $applicants_criteria = new Criteria();
    PersonPeer::doSelectOrderedCriteria($applicants_criteria);
    $results = array();
    foreach (PersonalPeer::doSelect(new Criteria()) as $personal)
    {
      $results[$personal->getPersonId()] = $personal->getPersonId();
    }
    foreach (TeacherPeer::doSelect(new Criteria()) as $teacher)
    {
      $results[$teacher->getPersonId()] = $teacher->getPersonId();
    }
    $applicants_criteria->add(PersonPeer::ID, $results, Criteria::IN);
    $this->getWidget('applicant_id')->setOption('criteria', $applicants_criteria);

    $preceptors_criteria = new Criteria();
    PersonPeer::doSelectOrderedCriteria($preceptors_criteria);
    $preceptors_criteria->addJoin(PersonPeer::ID, PersonalPeer::PERSON_ID);
    $this->getWidget('responsible_id')->setOption('criteria', $preceptors_criteria);

    $this->setWidget('document', new sfWidgetFormInputFile());

    if ($this->getObject()->getDocument())
    {
      $this->setWidget('current_document', new mtWidgetFormPartial(array('module' => 'student_disciplinary_sanction', 'partial' => 'downloable_document', 'form' => $this)));
      $this->setWidget('delete_document', new sfWidgetFormInputCheckbox());

      $this->getWidgetSchema()->moveField('delete_document', sfWidgetFormSchema::BEFORE, 'document');
      $this->getWidgetSchema()->moveField('current_document', sfWidgetFormSchema::BEFORE, 'delete_document');
    }
  }

}
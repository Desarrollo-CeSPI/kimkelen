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
 * TutorCustom form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class TutorCustomForm extends TutorForm
{
  public function configure()
  {
    parent::configure();
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    
    unset ($this['person-lastname'],
           $this['person-firstname'],
           $this['person-identification_type'],
           $this['person-identification_number'],
           $this['person-sex'],
           $this['person-cuil'],
           $this['person-birthdate'],
           $this['person-birth_country'],
           $this['person-birth_state'],
           $this['person-birth_department'],
           $this['person-birth_city'],
           $this['person-photo'],
           $this['person-observations'],
           $this['person-email'],
           $this['person-phone'],
           $this['person-address'],
           $this['person-nationality_id'],
           $this['examination_repproved_subject_teacher_list'],
           $this['examination_subject_teacher_list'],
           $this['salary'],
           $this['person-username'],
           $this['person-password'],
           $this['person-password_again'],
           $this['is_alive']     
    );
    
    $this->getWidgetSchema()->moveField('tutor_type_id', sfWidgetFormSchema::BEFORE,'occupation_id' );
    
    $this->setValidator('tutor_type_id', new sfValidatorPropelChoice(array('model' => 'TutorType', 'column' => 'id', 'required' => true)));
    $this->setValidator('student_list', new sfValidatorPass(array('required'=> true)));
  }
  
  public function getFormFieldsDisplay()
  {
    return array(
          'Personal data'   =>  array( 'person-lastname', 'person-firstname', 'person-identification_type', 'is_alive', 'person-identification_number', 'person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state','person-birth_department' ,'person-birth_city','person-nationality_id', 'tutor_type_id', 'person-observations' ),
          'Statistics'      => array('occupation_id', 'occupation_category_id', 'study_id'),
          'Contact data'   => array('person-email', 'person-phone', 'person-address'),
   //       'System access'  => array('person-username', 'person-password', 'person-password_again'),
          'In charge of'  => array('student_list')
    );
  }
 
}

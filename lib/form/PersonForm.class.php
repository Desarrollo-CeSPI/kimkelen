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
 * Person form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class PersonForm extends BasePersonForm
{
  public function configure()
  {
    //Fields remove
    unset($this['user_id']);
    unset($this['address_id']);
    unset($this['is_active']);

    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Url'));

    //identification type
    $this->setWidget('identification_type',  new sfWidgetFormSelect(array(
          'choices'  => BaseCustomOptionsHolder::getInstance('IdentificationType')->getOptions()
           )));
    $this->setValidator('identification_type', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('IdentificationType')->getKeys(),
        'required'=>false)
    ));


    //birthday
    $this->setWidget('birthdate', new csWidgetFormDateInput(array('change_year' => true, 'change_month' => true)));
    $this->getWidget('birthdate')->setOption('year_range', (date('Y')-80).':'.(date('Y')));

    $this->setValidator('birthdate', new mtValidatorDateString(array('required' => false)));

    //email
    $this->setValidator('email', new sfValidatorEmail(array('required' => false)));

   //identification number
    //$this->setValidator('identification_number', new sfValidatorNumber(array('required'=>false)));

    //Birth country, state and city widgets
    $c = new Criteria();
    $c->addAscendingOrderByColumn('name');
    
    $this->setWidget('birth_country', new sfWidgetFormPropelchoice(array(
        'model'     => 'Country',
        'add_empty' => true,
        'criteria' => $c
    )));

    $this->setDefault('birth_country',  SchoolBehaviourFactory::getInstance()->getDefaultCountryId());

    $widget_birth_state = new sfWidgetFormPropelChoice(array(
      'model' => 'State',
      'add_empty' => true
      ));

    #This string is necesary to assemble the id needed for the html object
    $related_class= $this->getOption('related_class');

    if ( empty($related_class) ) throw new LogicException (get_class($this).": Can't be used without related_class option setted. SEE README of this classs!");

    $embed_str = $this->getOption('embed_as','');
    $embed_str = empty($embed_str)?'':"$embed_str-";
    $related_class.='_'.$embed_str;

    $this->setWidget('birth_state', new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'country_id',
        'dependant_widget'   => $widget_birth_state,
        'observe_widget_id'  => $related_class.'birth_country',
        'message_with_no_value' => __('Select a country first'),
    )));

    $this->setDefault('birth_state',  SchoolBehaviourFactory::getInstance()->getDefaultStateId());

	$c= new Criteria();
	$c->addAscendingOrderByColumn('name');
	
	$widget_birth_department = new sfWidgetFormPropelChoice(array(
      'model'      => 'Department',
      'add_empty'  => true,
      'criteria' => $c
    ));
    
    $this->setWidget('birth_department', new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'state_id',
        'dependant_widget'   => $widget_birth_department,
        'observe_widget_id'  => $related_class.'birth_state',
        'message_with_no_value' => __('Select a state first'),
        )));
	
    $c = new Criteria();
    $c->addAscendingOrderByColumn('name');

    $widget_birth_city = new sfWidgetFormPropelChoice(array(
      'model'      => 'City',
      'add_empty'  => true,
      'criteria' => $c
    ));

    $this->setWidget('birth_city', new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'department_id',
        'dependant_widget'   => $widget_birth_city,
        'observe_widget_id'  => $related_class.'birth_department',
        'message_with_no_value' => __('Select a department first'),
        )));
    $this->setDefault('birth_city',  SchoolBehaviourFactory::getInstance()->getDefaultCityId());
    
    $this->setWidget('nationality_id', new sfWidgetFormChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getOptions(true))));
    $this->setValidator('nationality_id', new sfValidatorChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getKeys(), 'required' => false)));

    $c_criteria = new Criteria(CountryPeer::DATABASE_NAME);
    $c_criteria->addAscendingOrderByColumn(CountryPeer::NATIONALITY);
    $this->setWidget('nationality_other_id', new sfWidgetFormPropelChoice(array('model' => 'Country', 'criteria' => $c_criteria, 'add_empty' => true,'method' => 'getNationality')));
    $this->setValidator('nationality_other_id', new sfValidatorPropelChoice(array('model' => 'Country', 'criteria' => $c_criteria, 'required' => false)));
   

    //field sex widget and validator
    $this->setWidget('sex', new sfWidgetFormSelect(array(
      'choices'  => BaseCustomOptionsHolder::getInstance('SexType')->getOptions()
    )));

    $this->setValidator('sex', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('SexType')->getKeys())
    ));

    //widgets and validators for username and password
    if ($this->getObject()->getsfGuardUser())
    {
      $this->setWidget('username', new sfWidgetFormReadOnly(array(
        'plain'          => false,
        'value_callback' => array($this->getObject()->getsfGuardUser(), 'getUsername')
      )));
      $this->setValidator('username',new sfValidatorPass());
    }
    else
    {
      $this->setWidget('username', new sfWidgetFormInput());
      $this->setValidator('username', new sfValidatorString(array('min_length' => 4, 'max_length' => 128, 'required' => false),array(
          'min_length' => __('Username must be at least 4 characters long'),
          'max_length' => __('Username must be at most 128 characters long')
      )));
      $this->getWidgetSchema()->setHelp('username',__('if blank no username will be assigned'));
    }

    $this->setWidget('password', new sfWidgetFormInputPassword());
    $this->setWidget('password_again',new sfWidgetFormInputPassword());

    $this->setValidator('password', new sfGuardSecurePasswordValidator(array('required' => false)));
    $this->setValidator('password_again', new sfGuardSecurePasswordValidator(array('required' => false)));

    $this->setWidget('photo', new sfWidgetFormInputFile());
    $this->setValidator('photo', new sfValidatorFile(array(
                                                        'path' => Person::getPhotoDirectory(),
                                                        'max_size' => '2097152',
                                                        'mime_types' => 'web_images',
                                                        'required' => false,
                                                        'validated_file_class' => 'sfCustomValidatedFile')));
    if($this->getObject()->getPhoto())
    {
      $this->setWidget('current_photo', new mtWidgetFormPartial(array('module' => 'personal', 'partial' => 'downloable_photo', 'form' => $this)));
      $this->setValidator('current_photo', new sfValidatorPass(array('required' => false)));
      $this->setWidget('delete_photo', new sfWidgetFormInputCheckbox());
      $this->setValidator('delete_photo', new sfValidatorBoolean(array('required' => false)));
    }

    $this->getWidgetSchema()->setHelp('photo', 'The file must be of the following types: jpeg, jpg, gif, png.');

    $this->getValidatorSchema()->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorPropelUnique(array('model' => 'sfGuardUser', 'field' => array($embed_str.'username'), 'column'=>array('username')),
          array('invalid' => __('There is another user with the same username'))),
      new sfValidatorCallback(array(
          'callback'=>  array($this,'checkUsername'),
          'arguments'=> array(
              'username'=>$embed_str.'username',
              'password'=>$embed_str.'password')), array('invalid'=>'If username is set, then password must be setted')),
      new sfValidatorSchemaCompare($embed_str.'password', sfValidatorSchemaCompare::EQUAL, $embed_str.'password_again',
          array(), array('invalid' => __('Password missmatch'))),

      new sfValidatorPropelUnique(array('model'=>'Person','primary_key'=>'person-id','field' => array($embed_str.'identification_type',$embed_str.'identification_number'), 'column'=>array('identification_type','identification_number')),
          array('invalid' => __('There is another user with the same identification number')))
    )));


    //ADDRESS FORM
    $address = $this->getObject()->getAddress();
    if (is_null($address)) {
        $address = new Address();
        $this->getObject()->setAddress($address);
    }

    $addressForm = new AddressForm($address, array('related_class'=>$related_class));
    $this->embedForm('address', $addressForm);

  }

  /**
   * Checks if username is setted but password is not.
   *
   * @param sfValidatorCallback $validator
   * @param <type> $values
   * @param <type> $args
   * @return <type>
   */
  public function checkUsername(sfValidatorCallback $validator, $values, $args)
  {
    $guard_user = $this->getObject()->getSfGuardUser();
    if (is_null($guard_user) &&
        !empty($values[$args['username']]) && empty($values[$args['password']]))
    {
            throw new sfValidatorError($validator, 'invalid');
    }
    
    $person = PersonPeer::retrieveByDocumentTypeAndNumber($values['person-identification_type'], $values['person-identification_number']);
    
    if(!is_null($person) && is_null($this->getObject()->getId()))
    {//Person exists
        $tutor = TutorPeer::findByDocumentTypeAndNumber($values['person-identification_type'], $values['person-identification_number']);
        if(!is_null($tutor))
        {
            $error = new sfValidatorError($validator, 'Ya existe una persona con perfil tutor con ese tipo y nro de documento.');
            throw new sfValidatorErrorSchema($validator, array('person-identification_number' => $error));
        }
        
        $guard_user = $person->getSfGuardUser();
        if (!is_null($guard_user))
        {
          $personal_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::PERSONAL);
          if (array_key_exists($personal_group, $guard_user->getGroups()))
          {
            $error = new sfValidatorError($validator, 'Ya existe una persona con perfil preceptor con ese tipo y nro de documento.');
            throw new sfValidatorErrorSchema($validator, array('person-identification_number' => $error));
          }

          $teacher_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::TEACHER);
          if (array_key_exists($teacher_group, $guard_user->getGroups()))
          {
            $error = new sfValidatorError($validator, 'Ya existe una persona con perfil profesor con ese tipo y nro de documento.');
            throw new sfValidatorErrorSchema($validator, array('person-identification_number' => $error));
          }
        }
            
        
    }
    return $values;
  }

  /**
   * Updates the values of the object with the cleaned up values.
   *
   * @param  array $values An array of values
   *
   * @return BaseObject The current updated object
   */
  public function updateObject($values = null)
  {
    parent::updateObject($values);
    $guard_user = $this->getObject()->getSfGuardUser();
    if ( !is_null($guard_user) )
    {
     /* changed password? */
      if (isset($values['password'])&&!empty($values['password']) )
      {
        $guard_user->setPassword($values['password']);
      }
    }
    else
    {
      if (isset($values['password'])&&!empty($values['password'])&&
          isset($values['username'])&&!empty($values['username']) )
      {
        $guard_user= new sfGuardUser();
        $guard_user->setUsername($values['username']);
        $guard_user->setPassword($values['password']);
        $this->getObject()->setSfGuardUser($guard_user);
      }

      if (isset($values['delete_photo']) && $values['delete_photo'] )
      {
        $this->getObject()->deleteImage();
      }
    }
    return $this->getObject();
  }
}

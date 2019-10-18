<?php

/**
 * AuthorizedPerson form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class AuthorizedPersonForm extends BaseAuthorizedPersonForm
{
  public function configure()
  {  
    $this->unsetFields();
    $person = $this->getObject()->getPerson();

    if (is_null($person)) {
            $person = new Person();
            $this->getObject()->setPerson($person);
    }

    $personForm = new PersonForm($person, array('related_class' => 'authorizedPerson', 'embed_as' => 'person'));
    $this->embedMergeForm('person', $personForm);

    $c = new Criteria();
    $c = self::getCriteriaForAvailableStudents();
   
    $this->setValidator('person-phone',new sfValidatorString(array( 'required' => true)));

    $this->getWidget('family_relationship_id')->setLabel('Family relationship');
    $this->setWidget('student_list',
      new csWidgetFormStudentMany(array('criteria'=> $c)));
    $this->setValidator('family_relationship_id', new sfValidatorPropelChoice(array('model' => 'FamilyRelationship', 'column' => 'id', 'required' => false)));
    
    $this->getWidget('student_list')->setLabel('Students');
    $this->setValidator('student_list', new sfValidatorPass());

    $this->setDefault('student_list',
    array_map(create_function('$st', 'return $st->getStudentId();'),
    $this->getObject()->getStudentAuthorizedPersons()));
    
    $this->setValidator('person-identification_type', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('IdentificationType')->getKeys(),
        'required'=>true)
    ));
    $this->setValidator('person-identification_number',new sfValidatorString(array('max_length' => 20, 'required' => true)));

    $this->setValidator('person-sex',new sfValidatorString(array( 'required' => false)));
    $this->setValidator('person-cuil',new sfValidatorString(array('required' => false)));
    $this->setValidator('person-is_active',new sfValidatorString(array( 'required' => false)));    

  }

  public function unsetFields()
  { unset($this['person_id']);
    unset($this['person-birthdate']);
    unset($this['person-birth_country']);
    unset($this['person-birth_state']);
    unset($this['person-birth_department']);
    unset($this['person-birth_city']);
    unset($this['person-observations']);
    unset($this['person-address_id']);
    unset($this['person-user_id']);
    unset($this['person-nationality_id']);
    
  }
  
   public static function getCriteriaForAvailableStudents()
  {
    $ret = array();
    if(sfContext::getInstance()->getUser()->isPreceptor())
    {
        $preceptor = PersonalPeer::retrievePreceptorBySfGuardUserId(sfContext::getInstance()->getUser()->getGuardUser()->getId());
        $c = new Criteria();         
        $c->addJoin(StudentPeer::ID, DivisionStudentPeer::STUDENT_ID);
        $c->addJoin(DivisionStudentPeer::DIVISION_ID,DivisionPreceptorPeer::DIVISION_ID);
        $c->addJoin(DivisionPreceptorPeer::DIVISION_ID, DivisionPeer::ID);
        $c->addJoin(DivisionPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
        $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
        $c->add(DivisionPreceptorPeer::PRECEPTOR_ID, $preceptor->getId());

        $students = StudentPeer::doSelect($c);           
    }
    else
    {
        $sy = SchoolYearPeer::retrieveCurrent();
        $c = new Criteria();         
        $c->addJoin(StudentPeer::ID, SchoolYearStudentPeer::STUDENT_ID);
        $c->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID,$sy->getId());
        $c->add(SchoolYearStudentPeer::IS_DELETED,false);
        $students = StudentPeer::doSelect($c);           

    }
    
                
    foreach ($students as $st)
    {
      $ret[]=$st->getId();
    }

    $criteria = new Criteria();
    $criteria->add(StudentPeer::ID,$ret,Criteria::IN);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::IS_ACTIVE, true);

    return $criteria;
  }

  
  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array('person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'person-phone', 'person-alternative_phone','family_relationship_id');

    return array(
          'Personal data'   =>  $personal_data_fields,
          'Authorized to withdraw'  => array('student_list')
    );
  }
  
  protected function doSave($con = null)
  { parent::doSave($con);
    $this->saveStudentList($con);
  }

  public function saveStudentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }
    $st_list_w = $this->getWidget('student_list');
    if (!isset( $st_list_w ))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $con->beginTransaction();
    try
    {
      $this->getObject()->deleteStudents($con);
      $values = $this->getValue('student_list');
      if (is_array($values))
      {
          foreach ($values as $value)
          {
            $student_ap = new StudentAuthorizedPerson();
            $student_ap->setAuthorizedPerson($this->getObject());
            $student_ap->setStudentId($value);
            $student_ap->save($con);
          }
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }

  }
  



}

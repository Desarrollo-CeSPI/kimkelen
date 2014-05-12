<?php

/**
 * BbaStudentForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class BbaStudentForm extends StudentForm
{
  public function configure ()
  {
    parent::configure();
    $this->getValidator('global_file_number')->setOption('required', false);
  }

  public function unsetFields()
  {
    unset($this['person_id']);
  }

  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array('person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'global_file_number','person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state', 'person-birth_city', 'person-photo', 'person-observations' );
    if($this->getObject()->getPerson()->getPhoto())
    {
      $personal_data_fields = array_merge($personal_data_fields, array('person-current_photo', 'person-delete_photo'));
    }
    return array(
          'Personal data'   =>  $personal_data_fields,
          'Contact data'    =>  array('person-email', 'person-phone', 'person-address'),
          'Health data'   =>  array('blood_group', 'blood_factor', 'health_coverage_id', 'emergency_information'),
          'Tags' => array('student_tag_list')
    );
  }
}
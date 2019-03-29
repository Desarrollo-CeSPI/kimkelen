<?php
/**
 * MedicalCertificate form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class MedicalCertificateForm extends BaseMedicalCertificateForm
{
  public function configure()
  {
    $this->setWidget('student_id', new sfWidgetFormInputHidden());
    $this->setWidget('school_year_id', new sfWidgetFormInputHidden());
  
    $this->setWidget('certificate', new sfWidgetFormInputFile());
    $this->setValidator('certificate', new sfValidatorFile(array(
                                                        'path' => MedicalCertificate::getDocumentDirectory(),
                                                        'max_size' => '2097152',
                                                        'mime_types' => array(
                                                                'application/pdf',
                                                                'image/jpeg'
                                                                            ),
                                                        'required' => false)));
    $this->getWidgetSchema()->setHelp('certificate', 'El archivo debe ser de los siguientes tipos: jpeg, jpg, pdf.');
    
    $this->setWidget('certificate_status_id',  new sfWidgetFormSelect(array(
          'choices'  => BaseCustomOptionsHolder::getInstance('MedicalCertificateStatus')->getOptionsForStatus($this->getObject()->getCertificateStatusId())
           )));
    
    $this->getWidgetSchema()->setLabel('certificate_status_id', 'Status');
    $this->setValidator('certificate_status_id', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('MedicalCertificateStatus'
                . '')->getKeys(),
        'required'=>true)
    ));
    
    $this->setWidget('date', new csWidgetFormDateInput());
    $this->setValidator('date', new mtValidatorDateString());
    
    $this->setWidget('theoric_class_from', new csWidgetFormDateInput());
    $this->setValidator('theoric_class_from', new mtValidatorDateString(array("required" => false)));
    
    $this->setWidget('theoric_class_to', new csWidgetFormDateInput());
    $this->setValidator('theoric_class_to', new mtValidatorDateString(array("required" => false)));
    
    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDate'))));
    if($this->getObject()->getCertificate())
    {
      $this->setWidget('current_certificate', new mtWidgetFormPartial(array('module' => 'medical_certificate', 'partial' => 'downloable_certificate', 'form' => $this)));
      $this->setValidator('current_certificate', new sfValidatorPass(array('required' => false)));
      $this->setWidget('delete_certificate', new sfWidgetFormInputCheckbox());
      $this->setValidator('delete_certificate', new sfValidatorBoolean(array('required' => false)));
      $this->getWidgetSchema()->moveField('delete_certificate', sfWidgetFormSchema::BEFORE, 'certificate');
      $this->getWidgetSchema()->moveField('current_certificate', sfWidgetFormSchema::BEFORE, 'delete_certificate');
    }
    
    if(!is_null($this->getObject()->getCertificateStatusId()))
    {
        $this->setWidget('current_certificate_status', new mtWidgetFormPartial(array('module' => 'medical_certificate', 'partial' => 'current_status', 'form' => $this)));
        $this->setValidator('current_certificate_status', new sfValidatorPass(array('required' => false)));
        $this->getWidgetSchema()->moveField('current_certificate_status', sfWidgetFormSchema::BEFORE, 'certificate_status_id');
        
        $this->setValidator('certificate_status_id', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('MedicalCertificateStatus'
                . '')->getKeys(),
        'required'=>false)               
        ));
        $this->getWidgetSchema()->setLabel('certificate_status_id', 'New status');
    }
  }
 
  public function checkDate($validator, $values)
  {
      $career = $this->getObject()->getStudent()->getCareerStudent()->getCareer();
      $career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career, $this->getObject()->getSchoolYear());
      $csyp = CareerSchoolYearPeriodPeer::retrieveLastDay($career_school_year);
      
      $lastday = $csyp->getEndAt();
      $csyp = CareerSchoolYearPeriodPeer::retrieveFirstDay($career_school_year);
      $firstday = $csyp->getStartAt();
 
      if (isset($values['date']) && $values['date'] >= $lastday  )
      {
        $error = new sfValidatorError($validator, 'La fecha de actualización del trámite no puede ser mayor a la fecha fin del periodo lectivo');
        throw new sfValidatorErrorSchema($validator, array('date' => $error));
      }
      
      if ( $values['theoric_class'] &&  ! isset($values['theoric_class_from']) )
      {
        $error = new sfValidatorError($validator, 'Este campo es requerido.');
        throw new sfValidatorErrorSchema($validator, array('theoric_class_from' => $error));
      }
      
      if ($values['theoric_class'] && isset($values['theoric_class_from']) &&  ($values['theoric_class_from'] > $lastday || $firstday > $values['theoric_class_from']) )
      {
        $error = new sfValidatorError($validator, 'La fecha de inicio de clases teóricas debe estar dentro de las fechas definidas para el periodo lectivo');
        throw new sfValidatorErrorSchema($validator, array('theoric_class_from' => $error));
      }
      
      if ( $values['theoric_class'] &&  ! isset($values['theoric_class_to']) )
      {
        $error = new sfValidatorError($validator, 'Este campo es requerido.');
        throw new sfValidatorErrorSchema($validator, array('theoric_class_to' => $error));
      }
      
      if ($values['theoric_class'] && isset($values['theoric_class_to']) && ($lastday < $values['theoric_class_to'] || $values['theoric_class_to'] < $firstday ) )
      {
        $error = new sfValidatorError($validator, 'La fecha fin de clases teóricas debe estar dentro de las fechas definidas para el periodo lectivo');
        throw new sfValidatorErrorSchema($validator, array('theoric_class_to' => $error));
      }
      
      if($values['theoric_class'] && $values['theoric_class_to'] < $values['theoric_class_from'])
      {
        $error = new sfValidatorError($validator, 'La fecha fin de clases teóricas no puede ser menor a la fecha de inicio.');
        throw new sfValidatorErrorSchema($validator, array('theoric_class_to' => $error));
      }
      
      return $values;
  }
  protected function doSave($con = null)
  { $values = $this->getValues();
     
    parent::doSave($con);
    if(is_null($values['certificate']))
    {
      if(isset($values['delete_certificate']) && $values['delete_certificate'])
      {
        $this->getObject()->deleteCertificate();
      }
    }
    
    $log = new LogMedicalCertificate();
    $log->setUsername(sfContext::getInstance()->getUser());
    $log->setMedicalCertificate($this->getObject());
    $log->setDescription($this->getObject()->getDescription());
    $log->setCertificate($this->getObject()->getCertificate());
    $log->setSchoolYear($this->getObject()->getSchoolYear());
    $log->setStudent($this->getObject()->getStudent());
    $log->setCertificateStatusId($this->getObject()->getCertificateStatusId());
    $log->setDate($this->getObject()->getDate());
    $log->setTheoricClass($this->getObject()->getTheoricClass());
    $log->setTheoricClassFrom($this->getObject()->getTheoricClassFrom());
    $log->setTheoricClassTo($this->getObject()->getTheoricClassTo());
    
    $log->save();
           
  }
}
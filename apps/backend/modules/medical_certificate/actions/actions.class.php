<?php
require_once dirname(__FILE__).'/../lib/medical_certificateGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/medical_certificateGeneratorHelper.class.php';
/**
 * medical_certificate actions.
 *
 * @package    symfony
 * @subpackage medical_certificate
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class medical_certificateActions extends autoMedical_certificateActions
{
    public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('student'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un estudiante  para poder administrar los certificados.');
      $this->redirect('@student');
    }
    $this->student = StudentPeer::retrieveByPK($this->getUser()->getReferenceFor('student'));
    if (is_null($this->student))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un estudiante  para poder administrar los certificados.');
      $this->redirect('@student');
    }
    parent::preExecute();
  }
  public function executeNew(sfWebRequest $request)
  {
    $this->prepareMedicalCertificate();
    $this->form = $this->configuration->getForm($this->medical_certificate);
    $this->form->setDefaults(array(
      "student_id" => $this->medical_certificate->getStudentId(),
      "school_year_id" => $this->medical_certificate->getSchoolYearId(),
      "value"=> 1,
    ));
  }
  public function executeCreate(sfWebRequest $request)
  {
    $this->prepareMedicalCertificate();
    $this->form = $this->configuration->getForm($this->medical_certificate);
    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }
  public function prepareMedicalCertificate()
  {
    $this->medical_certificate = new MedicalCertificate();
    $this->medical_certificate->setStudent($this->student);
    $this->medical_certificate->setSchoolYear(SchoolYearPeer::retrieveCurrent());
  }
  public function executeBack()
  {
    $this->redirect('student');
  }
  public function buildCriteria()
  {
    $criteria = parent::buildCriteria();
    $criteria->add(MedicalCertificatePeer::STUDENT_ID, $this->student->getId());
    $criteria->add(MedicalCertificatePeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    return $criteria;
  }
  
  public function executeDownloadCertificate($request)
  {
    $medical_certificate = MedicalCertificatePeer::retrieveByPK($request->getParameter('id'));
    if ($medical_certificate && $medical_certificate->getCertificate())
    {
      $filePath = $medical_certificate->getDocumentFullPath();
      $response = $this->getResponse();
      $response->setHttpHeader('Pragma', '');
      $response->setHttpHeader('Cache-Control', '');
      $data = file_get_contents($filePath);
      $file_exploded = explode('.', $medical_certificate->getCertificate());
      $file_extension = end($file_exploded);
      if ($file_extension == 'pdf')
      {
        $response->setHttpHeader('Content-Type', 'application/pdf');
      }
      else
      {
        if ($file_extension == 'jpg')
        {
          $content_type = 'jpeg';
        }
        else
        {
          $content_type = $file_extension;
        }
        $response->setHttpHeader('Content-Type', 'image/' . $content_type);
      }
      $response->setHttpHeader('Content-Disposition', "attachment; filename=\"" . $medical_certificate->getCertificate() . "\"");
      $response->setContent($data);
    }
    return sfView::NONE;
  }
  
  public function executeShowHistory($request)
  {
    $this->medical_certificate = MedicalCertificatePeer::retrieveByPK($request->getParameter('id'));
    $this->back_url= '@medical_certificate';
    $this->logs = LogMedicalCertificatePeer::retrieveByMedicalCertificate($this->medical_certificate);
  }
}


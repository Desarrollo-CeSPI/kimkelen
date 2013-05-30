<?php

require_once dirname(__FILE__).'/../lib/licenseGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/licenseGeneratorHelper.class.php';

/**
 * license actions.
 *
 * @package    sistema de alumnos
 * @subpackage license
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class licenseActions extends autoLicenseActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('teacher') && !$this->getUser()->getReferenceFor('personal'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una persona para poder administrar sus licencias.');
      $this->redirect('@homepage');
    }

    $this->person = $this->getObject();

    if (is_null($this->person))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una persona para poder administrar sus licencias.');
      $this->redirect('@homepage');
    }

    parent::preExecute();
  }

  public function getObject()
  {
    if (!is_null(sfContext::getInstance()->getUser()->getReferenceFor("teacher")))
    {      
      $this->back_to = 'teacher';
      return TeacherPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("teacher"))->getPerson();
    }
    $this->back_to = 'personal';
    return PersonalPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("personal"))->getPerson();

  }

  public function executeNew(sfWebRequest $request)
  {
    $this->prepareLicense();

    $this->form = new LicenseForm($this->license);

    $this->form->setDefaults(array(
      "person_id" => $this->license->getPersonId()
    ));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->prepareLicense();

    $this->form = new LicenseForm($this->license);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function prepareLicense()
  {
    $this->license = new License();
    $this->license->setPerson($this->person);
  }

  public function getPager()
  {
    $pager = parent::getPager();
    $pager->setParameter('person',$this->person);
    return $pager;
  }

  public function executeLicenseActivation(sfWebRequest $request)
  {
    $this->license = $this->getRoute()->getObject();
    $this->license->setIsActive(!$this->license->getIsActive());
    $this->license->save();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@license');
  }

  public function executeBack (sfWebRequest $request)
  {
    $this->redirect("@$this->back_to");
  }
}

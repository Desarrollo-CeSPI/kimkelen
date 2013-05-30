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
 * mainBackend actions.
 *
 * @package    conservatorio
 * @subpackage mainBackend
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class mainBackendActions extends sfActions
{
  public function preExecute()
  {
    if (($this->getUser()->getLoginRole()) === null)
    {
      $this->getUser()->loginRole();
    }
  }

  public function executeGenerateBackup(sfWebRequest $request)
  {
    if (!$this->getUser()->hasCredential('backup'))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    $yml = sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'databases.yml';
    $yml_array = sfYaml::load($yml);
    $usuario = $yml_array['all']['propel']['param']['username'];
    $password = $yml_array['all']['propel']['param']['password'];
    $dsn = $yml_array['all']['propel']['param']['dsn'];
    $dbname = substr($dsn, strpos($dsn, 'dbname=') + strlen('dbname='));
    $dbname = substr($dbname, 0, strpos($dbname, ';'));

    $fileName = 'sistema-alumnos';
    $filePath = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR.$fileName.'.sql';

    if(is_null($password))
      $command = "mysqldump -u$usuario $dbname > $filePath";
    else
      $command = "mysqldump -u$usuario -p$password $dbname > $filePath";

    exec($command);
  }

  public function executeDownloadBackup(sfWebRequest $request)
  {
    if (!$this->getUser()->hasCredential('backup'))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    $fileName = 'sistema-alumnos';
    $filePath = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR.$fileName.'.sql';

    $response = $this->getResponse();
    $response->setHttpHeader('Pragma', '');
    $response->setHttpHeader('Cache-Control', '');
    $data = file_get_contents($filePath);
    $response->setHttpHeader('Content-Type', 'text/plain');
    $response->setHttpHeader('Content-Disposition', "attachment; filename=\"$fileName.sql\"");
    $response->setContent($data);
    unlink($filePath);

    return sfView::NONE;
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->current_school_year = SchoolYearPeer::retrieveCurrent();
    
  }

  /**
   * Executes an action to show that a 404 error has occurred.
   *
   * @param sfWebRequest $request
   */
  public function executeError404(sfWebRequest $request)
  {
    return '';
  }

  public function executeDownloadManual(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Url'));
    $this->redirect(public_path('/manual.pdf'));
  }

  public function executeOnlineManual(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Url'));
    $this->redirect(public_path('/manual.htm'));
  }

  private function getPersonPhoto(sfWebRequest $request, $attachment = false)
  {
    $person = PersonPeer::retrieveByPK($request->getParameter('id'));

    if($person && $person->getPhoto())
    {
      $filePath = $person->getPhotoFullPath();
      $response = $this->getResponse();
      $response->setHttpHeader('Pragma', '');
      $response->setHttpHeader('Cache-Control', '');
      $data = file_get_contents($filePath);
      $file_exploded = explode('.', $person->getPhoto());
      $file_extension = end($file_exploded);
      if($file_extension == 'jpg'){
        $content_type = 'jpeg';
      }
      else
      {
        $content_type = $file_extension;
      }
      $response->setHttpHeader('Content-Type', 'image/'.$content_type);
      if($attachment)
      {
        $response->setHttpHeader('Content-Disposition', "attachment; filename=\"".$person->getPhoto()."\"");
      }
      $response->setContent($data);
    }
  }

  public function executePersonPhoto(sfWebRequest $request)
  {
    $this->getPersonPhoto($request);
    return sfView::NONE;
  }

  public function executeDownloablePersonPhoto(sfWebRequest $request)
  {
    $this->getPersonPhoto($request, true);
    return sfView::NONE;
  }

  public function executeDownloableDocument(sfWebRequest $request)
  {
    $student_attendance_justification  = StudentAttendanceJustificationPeer::retrieveByPK($request->getParameter('id'));

    
    if($student_attendance_justification && $student_attendance_justification->getDocument())
    {
      $filePath = $student_attendance_justification->getDocumentFullPath();
      
      $response = $this->getResponse();
      $response->setHttpHeader('Pragma', '');
      $response->setHttpHeader('Cache-Control', '');
      $data = file_get_contents($filePath);
      $file_exploded = explode('.', $student_attendance_justification->getDocument());
      $file_extension = end($file_exploded);

      if($file_extension == 'jpg'){
        $content_type = 'jpeg';
      }
      else
      {
        $content_type = $file_extension;
      }      
      $response->setHttpHeader('Content-Type', 'image/'.$content_type);
      if($attachment)
      {
        $response->setHttpHeader('Content-Disposition', "attachment; filename=\"".$student_attendance_justification->getDocument()."\"");
      }
      $response->setContent($data);
    }

    return sfView::NONE;
  }

  public function executeChangeRole($request)
  {
    $this->form = new ChangeRoleForm(array(), array('actual_user' => $this->getUser()));
    $values = $request->getParameter($this->form->getName());
   
    if (isset($values['roles']) && !empty($values['roles']))
    {
      $this->getUser()->clearCredentials();
      $new_login_role= sfGuardGroupPeer::retrieveByPK($values['roles']);
      $this->getUser()->setLoginRole($new_login_role->getName());
      //die(var_dump($this->getUser()->getAttribute('login_role')));
      $this->getUser()->addCredentials(sfGuardPermissionPeer::retrieveAllCredentialsForARole(sfGuardGroupPeer::retrieveByName($this->getUser()->getLoginRole())));
    }

    return $this->redirect("mainBackend/index");
  }

}
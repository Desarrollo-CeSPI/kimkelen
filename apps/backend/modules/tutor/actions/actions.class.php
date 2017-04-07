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

require_once dirname(__FILE__) . '/../lib/tutorGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/tutorGeneratorHelper.class.php';

/**
 * tutor actions.
 *
 * @package    sistema de alumnos
 * @subpackage tutor
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class tutorActions extends autoTutorActions
{

  public function executeIndexByStudent(sfWebRequest $request)
  {
    parent::executeIndex($request);

    $student_id = $request->getParameter('id');
    $this->pager = $this->getPager();
    $c = new Criteria();
    $c->addjoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
    $c->add(StudentTutorPeer::STUDENT_ID, $student_id);

    $this->pager->SetCriteria($c);

    $this->pager->init();

    $this->setTemplate('index');

  }

  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);

    if ($this->getUser()->hasAttribute('previous_module_was_student') && $this->getUser()->getReferenceFor('student'))
    {
      $this->form->setDefault('student_list', array($this->getUser()->getReferenceFor('student')));
    }

  }
 
  public function executeGenerateUser($request)
  {
    $this->form = new GenerateUserForm();
    
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      $this->tutor = TutorPeer::retrieveByPK($request->getParameter('tutor_id'));
      $username = $request->getParameter('generate_user[username]');
      if ($this->form->isValid())
      {
           try
            {
              //Chequeo que no exista un usuario con ese nombre
              $user = sfGuardUserPeer::retrieveByUsername($username);

              if(is_null($user))
              {
                  //si el tutor tiene email registrado en el sistema.
                  
                  if(is_null($this->tutor->getPerson()->getEmail()) || trim($this->tutor->getPerson()->getEmail() === ''))
                  {
                      $this->getUser()->setFlash('error', 'El tutor no tiene cuenta de email registrada.');
                  }
                  else {
                      //creo el usuario y envio el email.
                      $user = new sfGuardUser();
                      $user->setUsername($username);
                      
                      /*Generar password aleatoria*/
                      $password = randomPassword::generate();


                      $user->setPassword($password);
                  
                      $user->setIsActive(true);
                      $user->setMustChangePassword(true);
                      $user->save(Propel::getConnection());
                      
                      //le seteo el usuario al tutor
                      
                      $this->tutor->getPerson()->setUserId($user->getId());
                      $this->tutor->save(Propel::getConnection());
                      

                      $to_name = $this->tutor->getPerson()->getFirstname();
                      $body = "Hola,". $to_name. ".\n Se ha generado una nuevo usuario para el sistema de alumnos Kimkelen.\nUsuario: " .$username . "\nContraseña:" . $password ;
                      $from = sfConfig::get('app_mailer_from');
                      $from_name = sfConfig::get('app_mailer_name_from');
                      $to = $this->tutor->getPerson()->getEmail();
                      $subject = sfConfig::get('app_mailer_new_user_subject');


                      $mailer = sfContext::getInstance()->getMailer();
                      $message = Swift_Message::newInstance()
                        ->setFrom($from, $from_name)
                        ->setTo($to, $to_name)
                        ->setSubject($subject)
                        ->setBody($body);

                      $message->setContentType("text/html");
                      $mailer->send($message);

                      $this->getUser()->setFlash("notice", "The item was updated successfully.");
                  }
              }
              else
              {
                   $this->getUser()->setFlash('error', 'Ya existe un usuario con ese nombre.');

              }
            }
            catch (Exception $e)
            {
              $this->getUser()->setFlash('error', 'Ocurrio un error durante la generación de usuario.');
            }     
        
      }
    }
    else{
        
        $this->tutor = $this->getRoute()->getObject();
    }
       
  }
}
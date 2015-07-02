<?php

require_once dirname(__FILE__).'/../lib/student_examination_repproved_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/student_examination_repproved_subjectGeneratorHelper.class.php';

/**
 * student_examination_repproved_subject actions.
 *
 * @package    symfony
 * @subpackage student_examination_repproved_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class student_examination_repproved_subjectActions extends autoStudent_examination_repproved_subjectActions
{
  public function executeEdit(sfWebRequest $request)
  {
    $this->redirect('@student_examination_repproved_subject');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->redirect('@student_examination_repproved_subject');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->redirect('@student_examination_repproved_subject');
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->redirect('@student_examination_repproved_subject');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->redirect('@student_examination_repproved_subject');
  }

  public function executeBack(sfWebRequest $request)
  {
   $this->redirect("@examination_repproved_subject");
  }
}

<?php

require_once dirname(__FILE__).'/../lib/authorized_personGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/authorized_personGeneratorHelper.class.php';

/**
 * authorized_person actions.
 *
 * @package    symfony
 * @subpackage authorized_person
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class authorized_personActions extends autoAuthorized_personActions
{
    
  public function executeIndexByStudent(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('authorized_person_student_id',$request->getParameter('id'));
    $this->redirect('authorized_person/index');

  }
}

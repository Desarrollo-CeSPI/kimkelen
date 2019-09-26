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
    parent::executeIndex($request);

    $student_id = $request->getParameter('id');
    $this->pager = $this->getPager();
    $c = new Criteria();
    $c->addjoin(StudentAuthorizedPersonPeer::AUTHORIZED_PERSON_ID, AuthorizedPersonPeer::ID);
    $c->add(StudentAuthorizedPersonPeer::STUDENT_ID, $student_id);

    $this->pager->SetCriteria($c);

    $this->pager->init();

    $this->setTemplate('index');

  }
}

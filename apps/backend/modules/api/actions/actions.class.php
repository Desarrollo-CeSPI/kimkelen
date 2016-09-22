<?php

/**
 * api actions.
 *
 * @package    symfony
 * @subpackage api
 * @author     Corrons M. Emilia
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class apiActions extends sfActions
{

  public function executeIsStudent(sfWebRequest $request)
  {
    $student = $this->getRoute()->getObject();
    $this->student = $student->asArray();
  }
}
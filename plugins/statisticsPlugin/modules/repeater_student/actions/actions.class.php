<?php

require_once dirname(__FILE__).'/../lib/repeater_studentGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/repeater_studentGeneratorHelper.class.php';

/**
 * repeater_student actions.
 *
 * @package    bba
 * @subpackage repeater_student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class repeater_studentActions extends autoRepeater_studentActions
{
  public function executeIndex(sfWebRequest $request)
  {
    // Clean student refererences if they exists
    $this->getUser()->cleanRepeaterStudentFilters();
    return parent::executeIndex($request);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->getUser()->setRepeaterStudentReferer('repeater_student', 'Volver al listado de alumnos repitentes');
    $this->redirect(array(
          'sf_route'  => 'student_show',
          'action'    => 'show',
          'id'        => $request->getParameter('id'),
          'sf_method' => 'post'
      ));
  }
}

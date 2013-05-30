<?php

require_once dirname(__FILE__).'/../lib/deserter_studentGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/deserter_studentGeneratorHelper.class.php';

/**
 * deserter_student actions.
 *
 * @package    bba
 * @subpackage deserter_student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class deserter_studentActions extends autoDeserter_studentActions
{
  protected function buildCriteria()
  {
    if (is_null($this->filters))
    {
      $this->filters = $this->configuration->getFilterForm($this->getFilters());
    }

    $criteria = $this->filters->buildCriteria($this->getFilters());

    $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_criteria'), $criteria);
    $criteria = $event->getReturnValue();

    $deserter_students = StudentPeer::getDeserterStudents();
    
    $ids = array();
    foreach ($deserter_students as $student)
      $ids [] = $student->getId();

    $criteria->add(StudentPeer::ID,$ids,Criteria::IN);

    $total_criteria_desertores = count(StudentPeer::doSelect($criteria));
    $total_alumnos = count(StudentPeer::doSelect(new Criteria()));
    $this->getUser()->setAttribute('students_count', $total_criteria_desertores);
    $this->getUser()->setAttribute('students_percentaje', ($total_criteria_desertores * 100) /$total_alumnos);

    return $criteria;
  }

  public function executeIndex(sfWebRequest $request)
  {
    // Clean student refererences if they exists
    $this->getUser()->cleanDeserterStudentFilters();
    return parent::executeIndex($request);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->getUser()->setDeserterStudentReferer('deserter_student', 'Volver al listado de alumnos desertores');
    $this->redirect(array(
          'sf_route'  => 'student_show',
          'action'    => 'show',
          'id'        => $request->getParameter('id'),
          'sf_method' => 'post'
      ));
  }
}

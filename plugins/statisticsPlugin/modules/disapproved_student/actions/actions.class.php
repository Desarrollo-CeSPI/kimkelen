<?php

require_once dirname(__FILE__).'/../lib/disapproved_studentGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/disapproved_studentGeneratorHelper.class.php';

/**
 * disapproved_student actions.
 *
 * @package    conservatorio
 * @subpackage disapproved_student
 * @author     cborre
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class disapproved_studentActions extends autoDisapproved_studentActions
{
  //This function filter the students that have the course disapproved
  //also refresh the statistics for the filters action
  protected function buildCriteria()
  {
    if (is_null($this->filters))
    {
      $this->filters = $this->configuration->getFilterForm($this->getFilters());
    }

    $criteria = $this->filters->buildCriteria($this->getFilters());

    $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_criteria'), $criteria);
    $criteria = $event->getReturnValue();

    $disapproved_students = StudentPeer::getDisapprovedStudents();

    $ids = array();
    foreach ($disapproved_students as $ds)
      $ids [] = $ds->getId();
    
    $criteria->add(StudentPeer::ID,$ids,Criteria::IN);

    $total_criteria_desaprobados = count(StudentPeer::doSelect($criteria));
    $total_alumnos = count(StudentPeer::doSelect(new Criteria()));
    $this->getUser()->setAttribute('students_count', $total_criteria_desaprobados);
    $this->getUser()->setAttribute('students_percentaje', ($total_criteria_desaprobados * 100) /$total_alumnos);

    return $criteria;
  }

  public function executeIndex(sfWebRequest $request)
  {
    // Clean student refererences if they exists
    $this->getUser()->cleanDissaprovedStudentFilters();
    return parent::executeIndex($request);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->getUser()->setDisapprovedStudentReferer('disapproved_student', 'Volver al listado de alumnos desaprobados');
    $this->redirect(array(
          'sf_route'  => 'student_show',
          'action'    => 'show',
          'id'        => $request->getParameter('id'),
          'sf_method' => 'post'
      ));
  }
}

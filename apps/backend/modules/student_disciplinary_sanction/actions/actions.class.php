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

require_once dirname(__FILE__) . '/../lib/student_disciplinary_sanctionGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/student_disciplinary_sanctionGeneratorHelper.class.php';

/**
 * student_disciplinary_sanction actions.
 *
 * @package    sistema de alumnos
 * @subpackage student_disciplinary_sanction
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class student_disciplinary_sanctionActions extends autoStudent_disciplinary_sanctionActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('student'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un estudiante  para poder administrar las sanciones.');
      $this->redirect('@student');
    }

    $this->student = StudentPeer::retrieveByPK($this->getUser()->getReferenceFor('student'));

    if (is_null($this->student))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un estudiante  para poder administrar las sanciones.');
      $this->redirect('@student');
    }

    parent::preExecute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->prepareStudentDisciplinarySanction();
    $this->form = $this->configuration->getForm($this->student_disciplinary_sanction);

    $this->form->setDefaults(array(
      "student_id" => $this->student_disciplinary_sanction->getStudentId(),
      "school_year_id" => $this->student_disciplinary_sanction->getSchoolYearId(),
      "value"=> 1,
    ));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->prepareStudentDisciplinarySanction();

    $this->form = $this->configuration->getForm($this->student_disciplinary_sanction);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function prepareStudentDisciplinarySanction()
  {
    $this->student_disciplinary_sanction = new StudentDisciplinarySanction();
    $this->student_disciplinary_sanction->setStudent($this->student);
    $this->student_disciplinary_sanction->setSchoolYearId($this->student->getSchoolYearStudentForSchoolYear()->getSchoolYearId());
  }

  public function executeBack()
  {
    $this->redirect('student');
  }

  public function executeDownloadDocument($request)
  {
    $student_disciplinary_sanction = StudentDisciplinarySanctionPeer::retrieveByPK($request->getParameter('id'));

    if ($student_disciplinary_sanction && $student_disciplinary_sanction->getDocument())
    {
      $filePath = $student_disciplinary_sanction->getDocumentFullPath();
      $response = $this->getResponse();
      $response->setHttpHeader('Pragma', '');
      $response->setHttpHeader('Cache-Control', '');
      $data = file_get_contents($filePath);

      $file_exploded = explode('.', $student_disciplinary_sanction->getDocument());
      $file_extension = end($file_exploded);
      if ($file_extension == 'pdf')
      {
        $response->setHttpHeader('Content-Type', 'application/pdf');
      }
      else
      {
        if ($file_extension == 'jpg')
        {
          $content_type = 'jpeg';
        }
        else
        {
          $content_type = $file_extension;
        }
        $response->setHttpHeader('Content-Type', 'image/' . $content_type);
      }
      $response->setHttpHeader('Content-Disposition', "attachment; filename=\"" . $student_disciplinary_sanction->getDocument() . "\"");
      $response->setContent($data);
    }

    return sfView::NONE;
  }

  public function buildCriteria()
  {
    $criteria = parent::buildCriteria();

    $criteria->add(StudentDisciplinarySanctionPeer::STUDENT_ID, $this->student->getId());

    return $criteria;
  }

}

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

class StudentDisciplinarySanction extends BaseStudentDisciplinarySanction
{
  /**
   * Returns the directory for the disciplinary sanctions documents
   * @return String
   */
  public static function getDocumentDirectory()
  {
     return sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'disciplinary-sanction-documents';
  }

  /**
   * Returns the full path of the document
   * @return String
   */
  public function getDocumentFullPath()
  {
    return self::getDocumentDirectory().DIRECTORY_SEPARATOR.$this->getDocument();
  }

  public function getApplicant()
  {
    return $this->getPersonRelatedByApplicantId();
  }

  public function getApplicantStr()
  {
    if($this->getPersonRelatedByApplicantId()==NULL){
        return($this->getApplicantOther());
    }
    else
        return($this->getPersonRelatedByApplicantId());
  }

  public function  getResponsible() {
    return $this->getPersonRelatedByResponsibleId();
  }

  public function delete(PropelPDO $con = null)
	{
    $document_path = $this->getDocumentFullPath();
    parent::delete($con);
    $this->deletePhysicalDocument($document_path);
  }

  public function deletePhysicalDocument($document_path)
  {
    if(file_exists($document_path))
      unlink($document_path);
  }

  public function deleteDocument()
  {
    $this->deletePhysicalDocument($this->getDocumentFullPath());
    $this->setDocument('');
    $this->save();
  }

  public function getValueString()
  {
    return $this->getValue() . ' amonestaciones';
  }

  public function getFormattedRequestDate()
  {
    return date('d-m-Y',strtotime($this->getRequestDate()));
  }

  public function getFormattedResolutionDate()
  {
    return date('d-m-Y',strtotime($this->getResolutionDate()));
  }

   public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }
}

try { sfPropelBehavior::add('StudentDisciplinarySanction', array('changelog'));} catch(sfConfigurationException $e) {}

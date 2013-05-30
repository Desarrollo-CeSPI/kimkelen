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

class StudentReincorporation extends BaseStudentReincorporation
{



  /**
   * this metho check if this student has reincorporation
   *
   * @return boolean
   */
  public function hasSubject()
  {
    return null != $this->getCourseSubject();
  }

  /**
   * Can be edited if is the last StudentReincorporation and the student dont have used any of the reincorporation abscenses.
   *
   * * @return boolean
   */
  public function canEdit()
  {
    if (!$this->isLastStudentReincorporation())
      return false;

    return true;
  }

  public function getMessageCantEdit()
  {
    if (!$this->isLastStudentReincorporation())
      return 'Cant be edited because, is not the last student reincorporation.';

    return 'Cant be edited because some absence of the remainging has been used.';
  }
  /**
   * This methoe check if this is the last student reincorporation checking at created_at
   *
   * @return boolean
   */
  public function isLastStudentReincorporation()
  {
    $c = new Criteria();
    $c->add(StudentReincorporationPeer::STUDENT_ID, $this->getStudentId());
    $c->add(StudentReincorporationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $this->getCareerSchoolYearPeriodId());
    $c->add(StudentReincorporationPeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
    $c->addDescendingOrderByColumn(StudentReincorporationPeer::CREATED_AT);

    $last = StudentReincorporationPeer::doSelectOne($c);

    return ($last->getCreatedAt() == $this->getCreatedAt());

  }


  public function updateFree(PropelPDO $con = null, $is_free)
  {
    if (is_null($con))
    {
      $con = Propel::getConnection(StudentReincorporationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    try
    {
      $con->beginTransaction();

      $c = new Criteria();
      $c->add(StudentFreePeer::STUDENT_ID, $this->getStudentId());
      $c->add(StudentFreePeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
      $c->add(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $this->getCareerSchoolYearPeriodId());
      $student_free = StudentFreePeer::doSelectOne($c);

      if ($student_free)
      {
        $student_free->setIsFree($is_free);
        $student_free->save($con);
      }

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }
  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }
}

sfPropelBehavior::add('StudentReincorporation', array('changelog'));
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

class CourseSubjectStudent extends BaseCourseSubjectStudent
{

  public function getSortedCourseSubjectStudentMarks(PropelPDO $con = null)
  {
    $criteria = new Criteria();

    $criteria->addAscendingOrderByColumn(CourseSubjectStudentMarkPeer::MARK_NUMBER);

    return $this->getCourseSubjectStudentMarks($criteria, $con);

  }

  public function setMarksFromArray(array $marks, PropelPDO $con = null)
  {
    $criteria = new Criteria();

    foreach ($marks as $position => $mark)
    {
      $criteria->add(CourseSubjectStudentMarkPeer::MARK_NUMBER, $position);
      $criteria->setLimit(1);

      $cssm = $this->getCourseSubjectStudentMarks($criteria, $con);

      if (count($cssm) == 0)
      {
        throw new PropelException('Imposible calificar el alumno: la nota con orden ' . $position . ' no existe para esta materia.');
      }

      $cssm = array_shift($cssm);

      $cssm->setMark($mark);
      $cssm->save($con);

      $criteria->clear();
    }

  }

  public function countValidCourseSubjectStudentMarks()
  {
    $c = new Criteria();
    $criterion = $c->getNewCriterion(CourseSubjectStudentMarkPeer::IS_FREE, true, Criteria::EQUAL);
    $criterion->addOr($c->getNewCriterion(CourseSubjectStudentMarkPeer::IS_CLOSED, true, Criteria::EQUAL));
    $criterion->addOr($c->getNewCriterion(CourseSubjectStudentMarkPeer::MARK, null, Criteria::ISNOTNULL));

    $c->addOr($criterion);
    return $this->countCourseSubjectStudentMarks($c);

  }

  protected function doSave(PropelPDO $con)
  {
    try
    {
      $con->beginTransaction();
      
      if ($this->countCourseSubjectStudentMarks() == 0)
      {
        for ($i = 1; $i <= $this->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks(); $i++)
        {
          $course_subject_student_mark = new CourseSubjectStudentMark();
          $course_subject_student_mark->setCourseSubjectStudent($this);
          $course_subject_student_mark->setMarkNumber($i);
          
          $last_period_close = $this->getCourseSubject()->getCourse()->getCurrentPeriod()-1;
          if ($i <= $last_period_close)
          {
            $course_subject_student_mark->setIsClosed(true); // se pone la nota como cerrada
          }
          
          $course_subject_student_mark->save($con);
        }
      }
      parent::doSave($con);
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
    }

  }

  /**
   *
   * This method returns the mark passed by param.
   *
   * @param <type> $mark_number
   * @return  CourseSubjectStudentMark
   */
  public function getMarkForIsClose($mark_number, PropelPDO $con = null)
  {
    $mark = $this->getMarkFor($mark_number);
  
    if ($mark)
    {
      return ($mark->getIsClosed()) ? $mark->getMarkByConfig($this->getConfiguration()) : null;
    }
    else
    {
      return null;
    }

  }

  public function getLastMarkForIsClose(PropelPDO $con = null)
  {
    $mark_number = 0;
    /* @var $mark CourseSubjectStudentMark */
    foreach ($this->getCourseSubjectStudentMarks() as $mark)
    {
      $mark_number = $mark->getMarkNumber() > $mark_number ? $mark->getMarkNumber() : $mark_number;
    }
    $mark = $this->getMarkFor($mark_number, $con);

    return ($mark->getIsClosed()) ? $mark : null;


  }

  public function getMarkFor($mark_number, PropelPDO $con = null)
  {
    $criteria = new Criteria();

    $criteria->add(CourseSubjectStudentMarkPeer::MARK_NUMBER, $mark_number);

    $values = $this->getCourseSubjectStudentMarks($criteria, $con);

    $values = array_shift($values);

    return $values;

  }

  /**
   * This method returns true if all the marks are closed.
   *
   * @return boolean
   */
  public function areAllMarksClosed(PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentMarkPeer::IS_CLOSED, false);
    return $this->countCourseSubjectStudentMarks($c, false, $con) == 0;
  }

  public function getMarksAverage(PropelPDO $con = null)
  {
    if (!$this->areAllMarksClosed())
    {
      return '';
    }

    return SchoolBehaviourFactory::getEvaluatorInstance()->getMarksAverage($this, $con);
  }

  public function getCourseResult(PropelPDO $con = null)
  {

    /* Si tengo alguna materia sin cerrar devuelvo null */
    if (!$this->areAllMarksClosed())
    {
      return null;
    }

    /* Si tiene aprobada la cursada, entonces retornamos la cursada aprobada */
   
    if (!is_null($this->getStudentApprovedCourseSubject($con)))
    {
      return $this->getStudentApprovedCourseSubject($con);
    }
//    Esto seria por el caso en que  exista la aprobacion y por un error de versiones  no este asociada la aprobacion de la cursada con la cursada en realida!
    else
    {
      $c = new Criteria();
      $c->add(StudentApprovedCourseSubjectPeer::STUDENT_ID, $this->getStudentId());
      $c->add(StudentApprovedCourseSubjectPeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
      $c->add(StudentApprovedCourseSubjectPeer::SCHOOL_YEAR_ID, $this->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear()->getId());

      if (!is_null($result = StudentApprovedCourseSubjectPeer::doSelectOne($c)))
      {
        //aca encontramos la cursada aprobada y se la asociamos al course_subject_student
        $this->setStudentApprovedCourseSubject($result);
        $this->save($con);
        return $result;
      }
    }

    /* Si desaprobò la cursada entonces retornamos la cursada desaprobada */
    if ($this->countStudentDisapprovedCourseSubjects(null, false, $con))
    {
      $disapproveds = $this->getStudentDisapprovedCourseSubjects(null, $con);

      return array_shift($disapproveds);
    }
    
    /* Si no aprobo o desaprobò, es porque tenemos que calcular què pasò y crear el resultado: aprobado o desaprobado..
     * Eso lo sabe el behavior
     */
    return SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentResult($this, $con);

  }

  /**
   * Returns the final mark for this course subject student.
   * If the course subject student is approved, returns the student approved
   * career subject (final mark). If not, returns the marks average.
   *
   * @return integer
   */
  public function getFinalMark()
  {
    if (!$this->areAllMarksClosed() || $this->getIsNotAverageable())
      return '';

    $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this);

    return $student_approved_career_subject ? $student_approved_career_subject->getMark() : $this->getMarksAverage();
  }

  public function getFinalAvg()
  {
    if (!$this->areAllMarksClosed())
      return '';

    $course_subject_student_examination = $this->getLastCourseSubjectStudentExamination();
    $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();

    if (is_null($course_subject_student_examination))
    {
      return $this->getFinalMark();
    }
    elseif ($course_subject_student_examination->getMark() < $evaluator_instance->getExaminationNote())
    {
      return '';
    }

    $average = (string) (($this->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);

    if ($average < 4)
    {
      $average = 4;
    }

    return $average = sprintf('%.4s', $average);

  }

  /**
   * Returns the status for $this (CourseSubjectStudent).
   *
   * @return string
   */
  public function getStatus($with_mark = false)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));

    if ($this->getCourseSubject()->getCourse()->getIsClosed())
    {
      $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this);

      if ($this->getStudentApprovedCourseSubject() || $student_approved_career_subject)
      {
        return $with_mark ? __("Approved %mark%", array("%mark%" => $this->getFinalMark())) : __("Approved");
      }
      else
      {
        return $with_mark ? __("Dissaproved %mark%", array("%mark%" => $this->getFinalMark())) : __("Dissaproved");
      }
    }
    else
    {
      return __("In course");
    }

  }

  /**
   * Returns the status for $this (CourseSubjectStudent) when it's disapproved.
   *
   * @return string
   */
  public function getDisapprovedStatus()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));

    $repproved = StudentRepprovedCourseSubjectPeer::retrieveByCareerSubjectIdAndStudentId($this->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubjectId(), $this->getStudentId());

    if ($repproved)
    {
      return __("Previous");
    }
    else
    {
      $c = new Criteria();
      $c->addDescendingOrderByColumn(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER);
      $examinations = $this->getCourseSubjectStudentExaminations($c);
      return SchoolBehaviourFactory::getEvaluatorInstance()->getStringFor($examinations[0]->getExaminationNumber());
    }

  }

  public function getCompleteStatus()
  {
    $str = $this->getStatus();

    if ($this->getCourseSubject()->getCourse()->getIsClosed() && !$this->getStudentApprovedCourseSubject())
    {
      $str .= " (" . $this->getDisapprovedStatus() . ")";
    }

    return $str;

  }

  /**
   * This method is for course without division (Conservatorio like).
   *
   * @return Boolean
   */
  public function canClose()
  {
    //If the course have a division, then it is closed by all the course. Not by student.
    if ($this->getCourseSubject()->getCourse()->getDivision())
      return false;

    $c = new Criteria();
    $c->add(CourseSubjectStudentMarkPeer::MARK, null, Criteria::ISNOTNULL);
    return ($this->countCourseSubjectStudentMarks($c) > 0);

  }

  public function isClosed()
  {
    return $this->countStudentDisapprovedCourseSubjects() > 0 || !is_null($this->getStudentApprovedCourseSubjectId());

  }

  public function getAvailableCourseSubjectStudentMarks(Criteria $criteria = null)
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getAvailableCourseSubjectStudentMarks($this, $criteria);

  }

	public function getCourseSubjectStudentPathwayMark() {
		$c = new Criteria();
		$c->add(CourseSubjectStudentPathwayPeer::STUDENT_ID, $this->getStudentId());
		$c->add(CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
		$c->addJoin(CourseSubjectStudentPathwayPeer::PATHWAY_STUDENT_ID, PathwayStudentPeer::ID, Criteria::INNER_JOIN);
		$c->add(PathwayStudentPeer::PATHWAY_ID, PathwayPeer::retrieveCurrent()->getId());

		return CourseSubjectStudentPathwayPeer::doSelect($c);
  }

  public function getTotalAbsences()
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::STUDENT_ID, $this->getStudentId());
    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
    $c->add(StudentAttendancePeer::STUDENT_ATTENDANCE_JUSTIFICATION_ID, null, Criteria::ISNULL);

    $student_attendances = StudentAttendancePeer::doSelect($c);
    $total = 0;

    foreach ($student_attendances as $student_attendance)
    {
      $total += ($student_attendance->getAbsenceType()) ? $student_attendance->getAbsenceType()->getValue() : $student_attendance->getValue();
    }
    //$total = $this->roundAbsences($total);

    return $total;

  }

  public function getConfiguration()
  {
    return $this->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration();

  }

  public function countCourseSubjectStudentPeriods()
  {
    return count($this->getCourseSubject()->getCourseSubjectConfigurations());

  }

  /**
   * This method returns all the free studens of the student in this year.
   * Depending of the configuration of the course , if has day attendance or course_subject attendance
   */
  public function getStudentFrees()
  {
    $c = new Criteria();
    $c->add(StudentFreePeer::STUDENT_ID, $this->getStudentId());
    $c->add(StudentFreePeer::IS_FREE, true);

    if ($this->getCourseSubject()->hasAttendanceForSubject())
    {
      $c->add(StudentFreePeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
    }
    //else
    //{
      //$c->add(StudentFreePeer::COURSE_SUBJECT_ID, null, Criteria::ISNULL);
    //}

    return StudentFreePeer::doSelect($c);

  }

  public function updateCourseMarks($cant_marks, $con = null)
  {
    CourseSubjectStudentMarkPeer::deleteByCourseSubjectStudent($this->getId(), $con);

    for ($i = 1; $i <= $cant_marks; $i++)
    {
      $course_subject_student_mark = new CourseSubjectStudentMark();
      $course_subject_student_mark->setCourseSubjectStudent($this);
      $course_subject_student_mark->setMarkNumber($i);

      $course_subject_student_mark->save($con);
    }

  }

  public function getCourseSubjectStudentExaminationsForExaminationNumber($examination_number)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $this->getId());
    $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $examination_number);

    return CourseSubjectStudentExaminationPeer::doSelectOne($c);

  }

  public function getLastStudentDisapprovedCourseSubject()
  {
    $c = new Criteria();
    $c->add(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $this->getId());
    $c->addDescendingOrderByColumn(StudentDisapprovedCourseSubjectPeer::EXAMINATION_NUMBER);

    return StudentDisapprovedCourseSubjectPeer::doSelectOne($c);

  }

  public function canEdit()
  {
    $user = sfContext::getInstance()->getUser();

    return $user->isSuperAdmin();

  }

  public function getStudentRepprovedCourseSubject()
  {
    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $this->getId());
    $c->addDescendingOrderByColumn(StudentRepprovedCourseSubjectPeer::ID);

    return StudentRepprovedCourseSubjectPeer::doSelectOne($c);

  }

  /**
   * This method back the prev status, ONE of this:
   *
   * IF the student has approved the career subject. This method then is going to delete the StudentApprovedCareerSubject    * For this student and course_subject.
   *
   * IF the student has repproved the course_subject, this method is going to delete the last                                * student_examination_repproved_subject
   *
   * If the student has course_subject_student_examination, this method is going to delete the last one
   *
   * If the student has the course_result (Approved or disapproved), this method is going to delete this
   */
  public function backToPreviousCourseSubjectStatus(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    try
    {
      $con->beginTransaction();

      $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this);

      if (!is_null($student_approved_career_subject))
      {
	      $srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($this);

	      if (!is_null($srcs)) {
	        $srcs->setStudentApprovedCareerSubject(null);
	        $srcs->save($con);
	      }

        $student_approved_career_subject->delete($con);

      }
      
      $student_repproved_course_subject = $this->getStudentRepprovedCourseSubject();

      if (is_null($student_repproved_course_subject))
      {
        //si es examination
        $course_subject_student_examination = $this->getLastCourseSubjectStudentExamination();   
        //si existe alguna mesa de examination
        if (!is_null($course_subject_student_examination))
        {

          $course_subject_student_examination->delete($con);
        }
        else
        {
          // habilita la edicion para notas del año!
          $course_result = $this->getCourseResult();

          if (!is_null($course_result))
          {
            $course_result->delete($con);
          }
          
        }
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function getLastCourseSubjectStudentExamination()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(CourseSubjectStudentExaminationPeer::ID);
    $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $this->getId());

    return CourseSubjectStudentExaminationPeer::doSelectOne($c);

  }

  public function getStudentApprovedCareerSubject()
  {
    $career_subject_id = $this->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubjectId();
    $c = new Criteria();
    $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $this->getStudentId());
    $c->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $career_subject_id);

    return StudentApprovedCareerSubjectPeer::doSelectOne($c);

  }

  public function hasSomeMarkFree()
  {
    foreach ($this->getCourseSubjectStudentMarks() as $course_subject_student_mark)
    {
      if ($course_subject_student_mark->getIsFree()){
        return true;
      }
    }

    return false;
  }

  public function getCourseMinimunMarkForCurrentSchoolYear($con)
  {
    return $this->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();

  }

  public function repprovedCourseSubjectHasBeenApproved()
  {
    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $this->getId());
    $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNOTNULL);
    $c->addJoin(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, StudentApprovedCareerSubjectPeer::ID);

    return StudentRepprovedCourseSubjectPeer::doSelectOne($c);

  }

  public static function compare($a, $b)
  {
//    var_dump($a->getId(), $b->getId(),'--------');
    # return $a->getId() == $b->getId()? 0: $a->getId() > $b->getId()? 1: -1;


    if ($a->getId() === $b->getId())
      return 0;
    return ($a->getId() > $b->getId()) ? 1 : -1;

  }

  public function getStudentRepprovedCourseSubjectStrings()
  {
    $marks = '';
    $student_repproved_course_subject = $this->getStudentRepprovedCourseSubject();
    if (is_null($student_repproved_course_subject))
    {
      return $marks;
    }

    $crit = new Criteria();
    $crit->addJoin(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, ExaminationRepprovedSubjectPeer::ID);
    $crit->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID);
    $crit->addAscendingOrderByColumn(ExaminationRepprovedPeer::EXAMINATION_NUMBER);

    foreach ($student_repproved_course_subject->getStudentExaminationRepprovedSubjects($crit) as $srcs)
    {
      if (!is_null($srcs->getDate())) {
        $marks[] = $srcs->getShortValueString() . ' (' . $srcs->getDate('d/m/Y') . ') ';
      }
      else {
        $marks[] = $srcs->getShortValueString();
      }
    }

    return $marks = $marks != "" ? implode($marks, ', '): $marks;
  }

  public function hasNotAbsense()
  {
    $result = false;
    for ($index = 1; $index < $this->countCourseSubjectStudentMarks(null, false, null); $index++)
    {
      $result = $result || $this->getMarkFor($index)->getIsClosed();
    }
    return $result;

  }

  public function getAvgColor()
  {
    $course_result = $this->getCourseResult();

    return is_null($course_result) ? '' : $course_result->getColor();
  }

  public function getAverageByConfig($config = null)
  {
    if (!$this->areAllMarksClosed())
    {
      return '';
    }
    
    if ($config != null && !$config->isNumericalMark())
    {
      $letter_average = LetterMarkAveragePeer::getLetterMarkAverageByCourseSubjectStudent($this);
      $letter_mark = LetterMarkPeer::getLetterMarkByPk($letter_average->getLetterMarkAverage());
      
      return $letter_mark->getLetter();
    }
    else
    {
      return $this->getMarksAverage();
    }
  }

}
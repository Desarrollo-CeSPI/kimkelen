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

class ExaminationSubject extends BaseExaminationSubject
{

    public function canBeClosed()
    {
        $c = new Criteria();
        $c->addJoin(ExaminationSubjectPeer::ID, CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID);
        $c->add(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNULL);
        $c->add(CourseSubjectStudentExaminationPeer::IS_ABSENT, false);
        $c->add(CourseSubjectStudentExaminationPeer::CAN_TAKE_EXAMINATION, TRUE);
        $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID , CourseSubjectStudentPeer::ID);
          
        //quito los retirados
        $withdrawn_criteria = new Criteria();
		$withdrawn_criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
		$withdrawn_criteria->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN);
		$withdrawn_criteria->clearSelectColumns();
		$withdrawn_criteria->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
		$stmt_w = StudentCareerSchoolYearPeer::doSelectStmt($withdrawn_criteria);
		$not_in_w = $stmt_w->fetchAll(PDO::FETCH_COLUMN);
		
		
		$c->add(CourseSubjectStudentPeer::STUDENT_ID, $not_in_w, Criteria::NOT_IN);

        return $this->countCourseSubjectStudentExaminations($c) == 0 && !$this->getIsClosed();

    }

    public function getMessageCantBeClosed()
    {
        if ($this->getIsClosed())
        {
            return "The examination subject can't be closed because it's already closed.";
        }
        else
        {
            return 'Examination subject cant be closed, because some students were not calificated';
        }

    }

    public function close(PropelPDO $con = null)
    {
        $con = is_null($con) ? Propel::getConnection() : $con;

        try
        {
            $con->beginTransaction();

            $c = new Criteria();
            $c->add(CourseSubjectStudentExaminationPeer::CAN_TAKE_EXAMINATION, TRUE);

            foreach ($this->getSortedCourseSubjectStudentExaminations($c) as $course_subject_student_examination)
            {
                $course_subject_student_examination->close($con);
            }

            $this->setIsClosed(true);
            $this->save($con);

            $con->commit();
        }
        catch (Exception $e)
        {
            $con->rollBack();
            throw $e;
        }

    }

    public function getCriteriaForCourseSubjectExamination()
    {
        $criteria = new Criteria();
        $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $this->getId());
        $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID, Criteria::INNER_JOIN);
        $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
        
        //quito los retirados
        $withdrawn_criteria = new Criteria();
		$withdrawn_criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
		$withdrawn_criteria->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN);
		$withdrawn_criteria->clearSelectColumns();
		$withdrawn_criteria->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
		$stmt_w = StudentCareerSchoolYearPeer::doSelectStmt($withdrawn_criteria);
		$not_in_w = $stmt_w->fetchAll(PDO::FETCH_COLUMN);
		
		
		$criteria->add(StudentPeer::ID, $not_in_w, Criteria::NOT_IN);
        return $criteria;
    }

    /**
     * This method join Students with this examination_subject
     *
     * @return array Students
     */
    public function getStudents()
    {
        $criteria = $this->getCriteriaForCourseSubjectExamination();
        return StudentPeer::doSelect($criteria);

    }

    public function canManageStudents()
    {
        return !$this->getIsClosed();

    }

    public function getMessageCantManageStudents()
    {
        if ($this->getIsClosed())
        {
            return "The examination subject cant be moddify because it's closed.";
        }

    }

    public function getSortedCourseSubjectStudentExaminations(Criteria $c = null)
    {
        if (is_null($c))
        {
            $c = new Criteria();
        }

        $c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
        $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
        $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        
        //quito los retirados
        $withdrawn_criteria = new Criteria();
        $withdrawn_criteria->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN);
		$withdrawn_criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
		$withdrawn_criteria->clearSelectColumns();
		$withdrawn_criteria->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
		$stmt_w = StudentCareerSchoolYearPeer::doSelectStmt($withdrawn_criteria);
		$not_in_w = $stmt_w->fetchAll(PDO::FETCH_COLUMN);
		
		$c->add(StudentPeer::ID, $not_in_w, Criteria::NOT_IN);

        return $this->getCourseSubjectStudentExaminations($c);

    }

    public function getSubject()
    {
        return $this->getCareerSubjectSchoolYear()->getCareerSubject()->getSubject();

    }

    public function getExaminationNoteForStudent($student)
    {
        $criteria = new Criteria();
        $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
        $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $this->getId());
        $criteria->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
        return CourseSubjectStudentExaminationPeer::doSelectOne($criteria);

    }

    public function getTeachers()
    {
        return array_map(create_function('$c', 'return $c->getTeacher();'), $this->getExaminationSubjectTeachers());

    }

    public function getTeachersToString()
    {
        return implode(' / ', $this->getTeachers());
    }

    public function canEditCalifications()
    {
        return !$this->getIsClosed();// || sfContext::getInstance()->getUser()->hasCredential('edit_closed_examination');
    }

    public function canDelete()
    {
        if ($this->countCourseSubjectStudentExaminations() > 0)
            return false;

        return !$this->getIsClosed();
    }

    public function countTotalStudents()
    {
        return count($this->getStudents());
    }

    public function countApprovedStudents()
    {
        $criteria = $this->getCriteriaForCourseSubjectExamination();
        $criteria->add(CourseSubjectStudentExaminationPeer::MARK,SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote(),Criteria::GREATER_EQUAL);
        return  CourseSubjectStudentExaminationPeer::doCount($criteria) ;
    }


    public function countDisapprovedStudents()
    {
        $criteria = $this->getCriteriaForCourseSubjectExamination();
        $criteria->add(CourseSubjectStudentExaminationPeer::MARK,SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote(),Criteria::LESS_THAN);
        return  CourseSubjectStudentExaminationPeer::doCount($criteria) ;
    }

    public function countAbsenceStudents()
    {
        $criteria = $this->getCriteriaForCourseSubjectExamination();
        $criteria->add(CourseSubjectStudentExaminationPeer::IS_ABSENT,true);
        return  CourseSubjectStudentExaminationPeer::doCount($criteria) ;
    }


    public function getCareerSchoolYear()
    {
      return $this->getCareerSubjectSchoolYear()->getCareerSchoolYear();
    }

	  public function getYear() {
	    return $this->getCareerSubjectSchoolYear()->getCareerSubject()->getYear();
    }

		public function getSchoolYear() {
			return $this->getCareerSchoolYear()->getSchoolYear();
		}

	  public function getCareerSubject()
	  {
		  return $this->getCareerSubjectSchoolYear()->getCareerSubject();
	  }
}

sfPropelBehavior::add('ExaminationSubject', array('examination_subject'));

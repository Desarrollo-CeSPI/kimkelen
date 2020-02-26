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
        $c->add(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNULL);
        $c->add(CourseSubjectStudentExaminationPeer::IS_ABSENT, false);
        $c->add(CourseSubjectStudentExaminationPeer::CAN_TAKE_EXAMINATION, TRUE);
        $c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID , CourseSubjectStudentPeer::ID);
        $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID , $this->getId());
          
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
        $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        $criteria->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);
        
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
        $c = $this->getCriteriaForCourseSubjectExamination();
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
    public function canAssignPhysicalSheet()
    {
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);
        return !is_null($record);
    }
    
    public function canGenerateRecord()
    {   
        $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_EXAMINATION);
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);
        return $this->countTotalStudents() != 0 && ! is_null($setting->getValue()) && is_null($record) ;
    }
    
    public function getMessageCantAssignPhysicalSheet()
    {
        return "Can't assign physical sheet because the examination subject does not have a record.";  

    }
    public function getSortedByNameCourseSubjectStudentExaminations()
    {
        $criteria = new Criteria();
        $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $this->getId());
        $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID, Criteria::INNER_JOIN);
        $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
        $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
        $criteria->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);
        
        //quito los retirados
        $school_year = SchoolYearPeer::retrieveCurrent();
        
        if($this->getSchoolYear()->getYear() == $school_year->getYear())
        { //si la mesa es de este año quito retirados.
       
            $withdrawn_criteria = new Criteria();
            $withdrawn_criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
            $withdrawn_criteria->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN);
            $withdrawn_criteria->clearSelectColumns();
            $withdrawn_criteria->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
            $stmt_w = StudentCareerSchoolYearPeer::doSelectStmt($withdrawn_criteria);
            $not_in_w = $stmt_w->fetchAll(PDO::FETCH_COLUMN);
            $criteria->add(StudentPeer::ID, $not_in_w, Criteria::NOT_IN);
        }	
		
	
        
        
        return $this->getCourseSubjectStudentExaminations($criteria);
    }
    
    public function generateRecord(PropelPDO $con = null)
    {
        $con = is_null($con) ? Propel::getConnection() : $con;

        try
        {
            $con->beginTransaction();
            $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_EXAMINATION);

            $r = new Record();
            $r->setRecordType(RecordType::EXAMINATION);
            $r->setCourseOriginId($this->getId());
            $r->setLines($setting->getValue());
            $r->setStatus(RecordStatus::ACTIVE); 
            $r->setUsername(sfContext::getInstance()->getUser());
            $r->save();

            $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);

            $line =1 ;
            $sheet =1;
            $record_sheet = new RecordSheet();
            $record_sheet->setRecord($record);
            $record_sheet->setSheet($sheet);
            $record_sheet->save();

            foreach ($this->getSortedByNameCourseSubjectStudentExaminations() as $csse)
            {
               $rd = new RecordDetail();
               $rd->setRecordId($record->getId());
               $rd->setStudent($csse->getCourseSubjectStudent()->getStudent());
               $rd->setMark($csse->getMark());
               $rd->setIsAbsent($csse->getIsAbsent());
               
               if($csse->getStudent()->owsCorrelativeFor($this->getCareerSubject()))
               {
                   $rd->setOwesCorrelative(TRUE);
               }
               
               $division=DivisionPeer::retrieveStudentSchoolYearDivisions($this->getSchoolYear(), $csse->getStudent());
               if(count($division) > 0)
               {
                    $rd->setDivision($division[0]);
               }
              
               if ($csse->getIsAbsent())
               {
                   $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getAbsentResult());
               }
               elseif(!is_null($csse->getMark()))
               {
                    if ($csse->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote())
                    {
                        $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getDisapprovedResult());
                    }
                    else
                    {
                        $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getApprovedResult());
                    }
               }
               if ($line > $record->getLines())
               {
                   $line = 1;
                   $sheet ++;
                   $record_sheet = new RecordSheet();
                   $record_sheet->setRecord($record);
                   $record_sheet->setSheet($sheet);
                   $record_sheet->save();

               }
               $rd->setLine($line);
               $rd->setSheet($sheet);
               $line++;
               $rd->save();

               ####Liberando memoria###
               $rd->clearAllReferences(true);
               unset($rd);
               ##################*/
            }
            $con->commit();
        }
        catch (Exception $e)
        {
            $con->rollBack();
            throw $e;
        }   
    }
    
    public function canPrintRecord()
    {
        return $this->canAssignPhysicalSheet();
    }
    
    public function canRegenerateRecord()
    {   
        $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_EXAMINATION);
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);
        return $this->countTotalStudents() != 0 && ! is_null($setting->getValue()) && !is_null($record) ;
    }
    
    public function saveCalificationsInRecord()
    {
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);
        if(!is_null($record))
        {
            foreach ($this->getSortedByNameCourseSubjectStudentExaminations() as $csse)
            {
               $rd = RecordDetailPeer::retrieveByRecordAndStudent($record, $csse->getStudent());
               $rd->setMark($csse->getMark());
               $rd->setIsAbsent($csse->getIsAbsent());
               
               if($csse->getStudent()->owsCorrelativeFor($this->getCareerSubject()))
               {
                   $rd->setOwesCorrelative(TRUE);
               }

               $division=DivisionPeer::retrieveStudentSchoolYearDivisions($this->getSchoolYear(), $csse->getStudent());
               if(!is_null($division) && count($division) > 0)
               {
                    $rd->setDivision($division[0]);
               }

               if ($csse->getIsAbsent())
               {
                   $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getAbsentResult());
               }
               elseif(!is_null($csse->getMark()))
               {
                    if ($csse->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote())
                    {
                        $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getDisapprovedResult());
                    }
                    else
                    {
                        $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getApprovedResult());
                    }
               }else {
                   $rd->setResult(NULL);
               }

               $rd->save();
            }
        }      
    }
    
    public function canBeCalificate()
    {
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->getId(), RecordType::EXAMINATION);
        if(!is_null($record))
        {
            foreach ($this->getSortedByNameCourseSubjectStudentExaminations() as $csse)
            {
               $rd = RecordDetailPeer::retrieveByRecordAndStudent($record, $csse->getStudent());
               if (is_null($rd))
               {
                   return FALSE;
               }
            }
            if(count($this->getSortedByNameCourseSubjectStudentExaminations()) != count($record->getRecordDetails()))
            {
                return FALSE;
            }
        }
        
        return TRUE;  
    }
    
    public function getMessageCantBeCalificate()
    {
        return "Debe regenerar el acta ya que fueron modificados los alumnos en la mesa";
    }
}

sfPropelBehavior::add('ExaminationSubject', array('examination_subject'));

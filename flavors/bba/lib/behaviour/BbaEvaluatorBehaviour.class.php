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

class BbaEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

	const PROMOTION_NOTE = 7;
	const MIN_NOTE = 4;
	const POSTPONED_NOTE = 4;
	const DECEMBER = 1;
	const FEBRUARY = 2;
	const MAX_DISAPPROVED = 2;
	const EXAMINATION_NOTE = 6;
	const MINIMUN_MARK = 0; //nota minima de un examen
	const MAXIMUN_MARK = 10; //nota maxima de un examen

	const PATHWAY_PROMOTION_NOTE = 6;
        const CBFE_1       = 9;
        const CBFE_2       = 10;

	protected
		$_examination_number = array(
		self::DECEMBER => 'Diciembre',
		self::FEBRUARY => 'Febrero',
	);
        
        protected $cbfe = array(
            self::CBFE_1,
            self::CBFE_2,
        );

	/**
	 * This method returns the marks average of a student.
	 *
	 * @param CourseSubjectStudent $course_subject_student
	 * @return <type>
	 */
	public function getMarksAverage($course_subject_student, PropelPDO $con = null)
	{
		$sum = 0;
		$subject_configuration = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration();
		$tmp_sum = 0;
		$final_mark = 0;
		foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm)
		{
			$sum += $cssm->getMark();
		}

		$avg = (string) ($sum / $course_subject_student->countCourseSubjectStudentMarks());


		$avg = sprintf('%.4s', $avg);


		return $avg;

	}

	#bba solo  se fija en el promedio no en la ultima nota!
	public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
	{
		$minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
		return $average >= $minimum_mark;
	}

	public function getStudentDisapprovedResultStringShort(StudentDisapprovedCourseSubject $student_disapproved_course_subject)
	{
		return sprintf("%01.2f", $student_disapproved_course_subject->getCourseSubjectStudent()->getMarksAverage());
	}

	public function getPathwayPromotionNote ()
	{
		return self::PATHWAY_PROMOTION_NOTE;
	}
        
    public function canPrintGraduateCertificate($student)
    {
        if(!is_null($student->getCareerStudent()) && !in_array($student->getCareerStudent()->getCareer()->getId(),$this->cbfe))
        {
            if ($student->getCareerStudent()->getStatus() == CareerStudentStatus::GRADUATE)
            {
                return true;
            }
            else
            {
               //chequeo que esté en 7mo y tenga todas las materias aprobadas.
                $this->student_career_school_years = $student->getStudentCareerSchoolYears();
                $scsy_cursed = $student->getLastStudentCareerSchoolYearCoursed();
                
                if(is_null($scsy_cursed))
                {
                    return false;
                }

                $max_year = $scsy_cursed->getCareerSchoolYear()->getCareer()->getMaxYear();

                if($scsy_cursed->getYear() != $max_year)
                    return false;

                foreach ($this->student_career_school_years as $scsy)
                {
                    if($scsy->getStatus() == StudentCareerSchoolYearStatus::APPROVED || $scsy->getStatus() == StudentCareerSchoolYearStatus::IN_COURSE || $scsy->getStatus() == StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED
                            || $scsy->getStatus() == StudentCareerSchoolYearStatus::FREE || ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN  && 
                             $scsy->getId() == $scsy_cursed->getId())){

                        $career_school_year = $scsy->getCareerSchoolYear();
                        $school_year = $career_school_year->getSchoolYear();

                        $csss = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($career_school_year, $student);
                        foreach ($csss as $css)
                        { 
                            if (is_null($css->getStudentApprovedCareerSubject()) && is_null($css->getStudentApprovedCourseSubject()))
                            {
                                return false;                                                
                            }
                        }
                    }
                }
                return true;
            }
        }

    return false;

    }

    public function closeCourseSubjectStudent($result, PropelPDO $con = null)
  {
    if ($result instanceof StudentApprovedCourseSubject)
    {
      if (is_null($student_approved_career_subject = $result->getStudentApprovedCareerSubject($con)))
      {
        $student_approved_career_subject = new StudentApprovedCareerSubject();
        $student_approved_career_subject->setCareerSubject($result->getCourseSubject($con)->getCareerSubject($con));
        $student_approved_career_subject->setStudent($result->getStudent($con));
        $student_approved_career_subject->setSchoolYear($result->getSchoolYear($con));
        
        if ($result->getIsNotAverageable() && $result->getNotAverageableCalification() >= 7)
        {
        	$student_approved_career_subject->setMark($result->getNotAverageableCalification());
        }
        else
        {
             $student_approved_career_subject->setMark($result->getMark());
        }
        
        $result->setStudentApprovedCareerSubject($student_approved_career_subject);

        $student_approved_career_subject->save($con);
        $result->save($con);

        $student_approved_career_subject->clearAllReferences(true);

        $result->clearAllReferences(true);
      }

      unset($result);
      unset($student_approved_career_subject);
    }
    else
    {
		
      $c = new Criteria();
      $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $result->getExaminationNumber());
      $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $result->getCourseSubjectStudent()->getId());
      if (CourseSubjectStudentExaminationPeer::doCount($c) == 0)
      {
        $this->createCourseSubjectStudentExamination($result->getCourseSubjectStudent(null, $con), $con);
      }
    }
  }

  public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
	{
		if ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote())
		{
			$student_approved_career_subject = new StudentApprovedCareerSubject();
                        $car_sub = $student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getCourseSubject()->getCareerSubject();
                        $student_approved_career_subject->setCareerSubject($car_sub);
			$student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
			$student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

			if($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getIsNotAverageable())
                        {
                            $average = $student_examination_repproved_subject->getMark();
                        }
                        else
                        {

                           //Final average is the average of the course_subject_student and the mark of student_examination_repproved_subject
			   $average = (string) (($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getMarksAverage() + $student_examination_repproved_subject->getMark()) / 2);

			   $average = sprintf('%.4s', $average);
			   if ($average < self::MIN_NOTE)
		   	   {
				$average = self::MIN_NOTE;
			   }
                        }
			$student_approved_career_subject->setMark($average);

			$student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
			$student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
			$student_repproved_course_subject->save($con);

			$career = $student_repproved_course_subject->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer();
			##se corrobora si la previa es la última y está libre, hay que egresarlo
			$previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student_repproved_course_subject->getStudent(), $career);
			if ($student_repproved_course_subject->getStudent()->getCurrentOrLastStudentCareerSchoolYear()->getStatus() == StudentCareerSchoolYearStatus::FREE && $previous == 0)
			{
				$career_student = CareerStudentPeer::retrieveByCareerAndStudent($career->getId(), $student_repproved_course_subject->getStudent()->getId());;
				$career_student->setStatus(CareerStudentStatus::GRADUATE);
				//se guarda el school_year en que termino esta carrera
				$career_student->setGraduationSchoolYearId(SchoolYearPeer::retrieveCurrent()->getId());
				$career_student->save($con);
				//se guarda el estado en el student_career_school_year
				$scsy = $student_repproved_course_subject->getCourseSubjectStudent()->getStudent()->getCurrentOrLastStudentCareerSchoolYear();
				$scsy->setStatus(StudentCareerSchoolYearStatus::APPROVED);
				$scsy->save();
			}

			##se agrega el campo en student_disapproved_course_subject a el link del resultado final
			$student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);

			$student_approved_career_subject->save($con);
		}

	}

  public function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $course_subject_student = $course_subject_student_examination->getCourseSubjectStudent();
    
    // si aprueba la mesa de examen
    if ($course_subject_student_examination->getMark() >= $this->getExaminationNote())
    {
      $result = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student, $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

      if (is_null($result))
      {
        $result = new StudentApprovedCareerSubject();
        $result->setCareerSubject($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject());
        $result->setStudent($course_subject_student->getStudent());
        $result->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

        //Se busca si había una previa creada para esta materia entonces se debe eliminar ya que ahora está aprobada
        if ($student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student))
        {
          $sers = $student_repproved_course_subject->getStudentExaminationRepprovedSubjects();
	        //$sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($student_repproved_course_subject);

	        if ($sers >= 1) 
          {
	          foreach ($sers as $student_examination_repproved_subject) 
            {
              $student_examination_repproved_subject->delete($con);
            }
          }
          $student_repproved_course_subject->delete($con);
          
        }
      }

      $examination_subject = $course_subject_student_examination->getExaminationSubject();

      // IF is null, is because the course_subject_student_examination has been created editing student history
      $school_year = is_null($examination_subject) ? $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear() : $examination_subject->getExamination()->getSchoolYear();

      $result->setSchoolYearId($school_year->getId());
      
      if($course_subject_student->getIsNotAverageable())
      {
         $average = $course_subject_student_examination->getMark();
      }
      else
      {
         $average = $this->getAverage($course_subject_student, $course_subject_student_examination);

         $average = sprintf('%.4s', $average);
 
         if ($average < 4)
         {
           $average = 4;
         }
      }

      // se guarda la NOTA FINAL de la materia
      if ($course_subject_student_examination->getExaminationNumber() == self::FEBRUARY)
      {
        $this->setFebruaryApprovedResult($result, $average, $course_subject_student_examination->getMark());
      }
      else
      {
        $result->setMark($average);
      }

      ##se agrega en la tupla student_disapproved_course_subject el link a al resultado final y el tipo de mesa en el que aprobo
      $sdcs = $course_subject_student->getCourseResult();
      $sdcs->setStudentApprovedCareerSubject($result);
      $sdcs->setExaminationNumber($course_subject_student_examination->getExaminationNumber());
      $sdcs->save($con);

      $result->save($con);

    }
    else
    {
      // TODO: arreglar esto: pedir a la configuración
      // Pasa de diciembre a febrero (se copia el course_subject_student_examination con examination_number + 1)
      if ($course_subject_student_examination->getExaminationNumber() < count($this->_examination_number))
      {
        $this->nextCourseSubjectStudentExamination($course_subject_student_examination, $con);
      }
      else
      {
        // se crea una previa
        $srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);

        if (is_null($srcs)) {
           $student_repproved_course_subject = new StudentRepprovedCourseSubject();
           $student_repproved_course_subject->setCourseSubjectStudentId($course_subject_student->getId());
           $student_repproved_course_subject->save($con);

        }
      }
    }

  }

  public function createCourseSubjectStudentExamination(CourseSubjectStudent $course_subject_student, $con)
  {
    $course_subject_student_examination = new CourseSubjectStudentExamination();
    $course_subject_student_examination->setCourseSubjectStudent($course_subject_student);
//El if creo que no deberia existir para mantener la integridad de los datos. no deberia  existir course_subject_student_examination sin un examinationNumbre
//    if (!is_null($course_subject_student->getCourseResult()))
//    {

    $course_result = $this->getCourseSubjectStudentResult($course_subject_student, $con);

    $examination_number = self::DECEMBER;
    $course_subject_student_examination->setExaminationNumber($examination_number);
//    }
    $course_subject_student_examination->save($con);
    //Libero memoria
    $course_subject_student_examination->clearAllReferences(true);
    unset($course_subject_student_examination);
    unset($examination_number);
  }

  public function getLastStudentCareerSchoolYearCoursed($student)
  {  $last_scsy =  $student->getLastStudentCareerSchoolYear();
    
    $c = new Criteria();
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
    $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->add(CourseSubjectStudentMarkPeer::MARK,NULL, Criteria::NOT_EQUAL);
 /*$criterion = $c->getNewCriterion(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);
      $c->addOr($criterion); */
 $c->addAnd(CourseSubjectStudentMarkPeer::IS_FREE,FALSE);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);
    
    $scsy_c= StudentCareerSchoolYearPeer::doSelectOne($c);
    if(!is_null($scsy_c) && $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() == $scsy_c->getCareerSchoolYear()->getSchoolYear()->getYear())
    {
        return $scsy_c;
    }
    elseif(!is_null($scsy_c) &&  $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() > $scsy_c->getCareerSchoolYear()->getSchoolYear()->getYear() )
    {//si el ultimo año registrado es mayor al cursado me fijo si el último es 2020
        
        $years  = array(2020,2021,2022);
        if( in_array($last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear(), $years))
        {
            
            $c = new Criteria();
            $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
            $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
            $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
            $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
            $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
            $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
            $c->add(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);  
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

            $scsy_2 = StudentCareerSchoolYearPeer::doSelectOne($c);

            if(!is_null($scsy_2) && in_array($scsy_2->getCareerSchoolYear()->getSchoolYear()->getYear(), $years)  )
            {
                
                return $scsy_2;
            }
            else
            {
               return  $scsy_c;
            }
            
        }
        else
        {
            return $scsy_c;
        }
        
    }else
    {
       if(!is_null($scsy_c))
        return $scsy_c;
       else
       {
          if (!is_null($last_scsy) && $last_scsy->getYear() == 1)
          {
              return $last_scsy;
          }
         else
         {   //tiene ultimo año, pero no tiene ultimo año cursado. Es por las notas no promediables.
          $c = new Criteria();
            $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
            $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
            $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
            $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
            $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
            $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
            $c->add(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);  
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

            $scsy_2 = StudentCareerSchoolYearPeer::doSelectOne($c);
            return $scsy_2;
          }
       }
    }
  }
}

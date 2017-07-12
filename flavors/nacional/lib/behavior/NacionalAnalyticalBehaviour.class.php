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
 */

class NacionalAnalyticalBehaviour extends DefaultAnalyticalBehaviour
{
    public function getApprovationDateBySubject(StudentApprovedCareerSubject $studentApprovedCareerSubject)
    {
        $approvationInstance = $studentApprovedCareerSubject->getApprovationInstance();

        switch(get_class($approvationInstance)) {
          case 'StudentApprovedCourseSubject':

              var_dump($approvationInstance->getCourseSubject()->getCourseType());
            //return November
            if($approvationInstance->getCourseSubject()->getCourseType() != CourseType::TRIMESTER)
            {
                $period = $approvationInstance->getCourseSubject()->getLastCareerSchoolYearPeriod();
                if(!is_null($period))
                {
                    $month = date('m', strtotime($period->getEndAt()));
                    if($month > 11 ){
                        return $approvationInstance->getSchoolYear()->getYear()."-11-30";
                    }
                  return $period->getEndAt();
                }
                break;
            }
            else{
                return $approvationInstance->getSchoolYear()->getYear()."-11-30";
            }
           
            break;
          case 'StudentDisapprovedCourseSubject': 
            $cssid = $approvationInstance->getCourseSubjectStudentId();
            $csse = CourseSubjectStudentExaminationPeer::retrieveLastByCourseSubjectStudentId($cssid);
            $exam = $csse->getExaminationSubject()->getExamination();

            return $exam->getDateFrom();
          case 'StudentRepprovedCourseSubject':
            $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($approvationInstance);
            $exam = $sers->getExaminationRepprovedSubject()->getExaminationRepproved();

            return $exam->getDateFrom();
        }

        //couldn't find when was approved. return null ¿error?
        return;
        
    }
	
}
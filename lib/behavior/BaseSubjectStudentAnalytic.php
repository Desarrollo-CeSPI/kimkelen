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

 class BaseSubjectStudentAnalytic
 {

   public function __construct ($css)
   {
      $this->css = $css;

      $this->approved = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->css);

      $this->school_year = $this->css->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear();

      $this->approvationInstance = null;
   }

   public function approvationInstance()
   {
     if ($this->approved && !$this->approvationInstance) $this->approvationInstance = $this->approved->getApprovationInstance();
     return $this->approvationInstance;
   }

   public function getSubjectName()
   {
     return $this->css->getCourseSubject()->getSubject()->getName();
   }

   public function getNullLabel()
   {
     return "--------";
   }

   public function getCondition()
   {
     $instance = $this->approvationInstance();
     switch(get_class($instance)) {
      case 'StudentApprovedCourseSubject':
        return 'Regular';
      case 'StudentDisapprovedCourseSubject': 
        return 'Mesa';
      case 'StudentRepprovedCourseSubject':
        return 'Previa';
     }
     return $this->getNullLabel();
   }

   public function getApprovedDate()
   {
     return $this->approved
       ? StudentApprovedCareerSubjectPeer::retrieveApprovationDate($this->approved)
       : $this->getNullLabel();
   }

   public function getSchoolYear()
   {
     return $this->approved
      ? $this->school_year
      : $this->getNullLabel();
   }

   public function getMark($as_label = true)
   {
     return ( $this->approved ? $this->approved->getMark() : ($as_label ? $this->getNullLabel():null) );
   }


   public function getMarkAsSymbol()
   {
     if (!$this->approved) return $this->getNullLabel();
     $c = new num2text();
     return $c->num2str($this->getMark());
   }

   public function getSchoolName()
   {
       //$career_student->getCareer()->getCareerName()
       //$this->css->getSchoolName();
     return "Escuela?";
   }
 }

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

class equivalenceComponents extends sfComponents {

    public function executeStudyYear() {
        $this->career = $this->getVar('career');
        $this->year = $this->getVar('year');
        $this->school_year = null;
        $this->career_school_year = $this->getVar('career_school_year');
        $this->career_subject_school_years = $this->career->getCareerSubjectsForYear($this->year, true);
         
    }

    public function executeCareerSubject() {
        
        $this->form = new EquivalenceForm();
        #$this->form->setStudent($this->getVar('student'));
        $this->form->setCareerSubjectAndStudent($this->getVar('career_subject'),$this->getVar('student'));
        
    }

    public function executeOptions() {
        #$this->school_year = $this->getVar("school_year");
        $this->options = $this->getVar("options");
        $this->student = $this->getVar("student");
    }

}
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

class StudentCareerSubjectAllowedPeer extends BaseStudentCareerSubjectAllowedPeer
{
	static public function retrieveCriteriaByStudentAndCareerSubject($student, $career_subject)
	{
		$c = new Criteria();
		$c->add(self::STUDENT_ID, $student->getId());
		$c->add(self::CAREER_SUBJECT_ID, $career_subject->getId());

		return $c;
	}

	static public function retrieveByStudentAndCareerSubject($student, $career_subject)
	{
		return self::doSelect(self::retrieveCriteriaByStudentAndCareerSubject($student, $career_subject));	
	}

	static public function doCountStudentAndCareerSubject($student, $career_subject)
	{
		return self::doCount(self::retrieveCriteriaByStudentAndCareerSubject($student, $career_subject));
	}
}
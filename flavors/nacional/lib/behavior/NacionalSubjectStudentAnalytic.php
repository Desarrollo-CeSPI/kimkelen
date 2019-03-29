<?php /*
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

class NacionalSubjectStudentAnalytic extends BaseSubjectStudentAnalytic
{

	public function getCondition()
	{
		$instance = $this->approvationInstance();
		switch (get_class($instance))
		{
			case 'StudentApprovedCourseSubject':  
                            if ($this->getIsEquivalence())
                            {
                                return "Equivalencia";
                            }
                            else
                            {
                                return 'Regular';
                            }
			case 'StudentDisapprovedCourseSubject':
				if ($instance->getExaminationNumber() == 1) 
				{
					return 'Regular';
				}
				else
				{
					return 'R. Comp.';
				}
			case 'StudentRepprovedCourseSubject':
				if (is_null($instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()) || $instance->getLastStudentExaminationRepprovedSubject()->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType() == 1)
				{
					return 'R. Prev.';
				}
				else
				{
					return 'Libre';
				}
		}
		return;
	}

}
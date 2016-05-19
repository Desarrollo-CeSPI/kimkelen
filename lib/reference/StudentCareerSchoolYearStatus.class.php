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

class StudentCareerSchoolYearStatus extends BaseCustomOptionsHolder
{
	const
		IN_COURSE       		= 0,
		APPROVED       			= 1,
		REPPROVED       		= 2,
		LAST_YEAR_REPPROVED     = 3,
		WITHDRAWN       		= 4,
		WITHDRAWN_WITH_RESERVE	= 5,
	  FREE = 6;
	protected
		$_options = array(
		self::IN_COURSE       		 => 'Cursando',
		self::APPROVED       		 => 'Aprobado',
		self::REPPROVED      		 => 'Repitió este año',
		self::LAST_YEAR_REPPROVED 	 => 'Repetidor del año pasado, pero cursando año lectivo actual',
		self::WITHDRAWN				 => 'Retirado de la institución',
		self::WITHDRAWN_WITH_RESERVE => 'Retirado de la institución con reserva de banco',
		self::FREE => 'Libre'

	);


	public function getOptionsSelect()
	{
		return array(
			self::IN_COURSE       		 => 'Cursando',
			self::APPROVED       		 => 'Aprobado',
			self::WITHDRAWN				 => 'Retirado de la institución',
			self::WITHDRAWN_WITH_RESERVE => 'Retirado de la institución con reserva de banco'
		);

	}

}
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

class TutorPeer extends BaseTutorPeer
{
	static public function findByDocumentTypeAndNumber($document_type,$document_number)
	{
		$c = new Criteria();
		$c->addJoin(TutorPeer::PERSON_ID, PersonPeer::ID);
		$c->add(PersonPeer::IDENTIFICATION_NUMBER, $document_number);
		$c->add(PersonPeer::IDENTIFICATION_TYPE,$document_type );
		$s = self::doSelectOne($c);

		return $s;
	 }
}

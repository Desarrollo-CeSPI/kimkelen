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

/**
 * IdentificationType
 *
 * @author car
 */
class IdentificationType extends BaseCustomOptionsHolder
{
  const
    DNI      = 1,
    LC       = 2,
    LE       = 3,
    PASSPORT = 4,
    CI       = 5,
    CUIL     = 6,
    CUIT     = 7,
    OTRO     = 8;

  protected
    $_options = array(
        self::DNI       => 'DNI',
        self::LC        => 'LC',
        self::LE        => 'LE',
        self::PASSPORT  => 'PASAPORTE',
        self::CI        => 'CI',
        self::CUIL      => 'CUIL',
        self::CUIT      => 'CUIT',
        self::OTRO      => 'OTRO'
      );
      
  public function getIdentificationType($identification_type)
  {
		switch($identification_type){
			
			case 0:
				return self::DNI;
			break;
				
			case 1: // no hay dni temporario en kimkelen
				return self::OTRO;
			break;
			
			case 2:
				return self::CI;
			break;
			
			case 18:
				return self::LE;
			break;
			
			case 19:
				return self::LC;
			break;
			
			case 90:
				return self::PASSPORT;
			break;
			
			default:
				return null;
			break;
		}
   
  }
}

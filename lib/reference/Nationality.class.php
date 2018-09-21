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
 * Description of Nationality
 *
 * @author gramirez
 */
class Nationality extends BaseCustomOptionsHolder
{
  const
    N_NATIVE      = 0,
    N_NATURALIZED   = 1,
    N_FOREIGN= 2,
    N_FOR_OPTION = 3;

  protected
    $_options = array(
        self::N_NATIVE       => 'argentino',
        self::N_NATURALIZED    => 'argentino naturalizado',
        self::N_FOREIGN  => 'extranjero',
        self::N_FOR_OPTION => 'argentino por opción'
      );
    
  public function getNationality($nationality)
  {
    switch($nationality){

            case 1:
                    return self::N_NATIVE;
            break;

            case 2:
                    return self::N_FOREIGN;
            break;

            case 3:
                    return self::N_NATURALIZED;
            break;

            case 4:
                    return self::N_FOR_OPTION;
            break;

            default:
                    return null;
            break;
    } 
  }
}

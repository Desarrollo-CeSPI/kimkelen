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

class CareerStudentStatus extends BaseCustomOptionsHolder
{
  const
    REGULAR      = 0,
    GRADUATE     = 1;
 
	
  protected 
    $_options = array(
        self::REGULAR        => 'Regular',
        self::GRADUATE       => 'Egresado',

      );
  
  public function getOptions($include_blank = false, $no_graduate = false)
  {
    $options = ($no_graduate)?$this->_options_no_graduate:$this->_options;
    if ($include_blank !== false && !is_null($include_blank))
    {
      return array('' => (is_string($include_blank) ? $include_blank : '')) + $options;
    }

    return $options;
  }
}

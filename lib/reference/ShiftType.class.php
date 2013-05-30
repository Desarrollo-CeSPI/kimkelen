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

class ShiftType extends BaseCustomOptionsHolder
{

  const
    SHIFT_MORNING     = 1,
    SHIFT_AFTERNOON   = 2,
    SHIFT_EVENING     = 3;

  protected
    $_options = array(
        self::SHIFT_MORNING    => 'Mañana',
        self::SHIFT_AFTERNOON  => 'Tarde',
        self::SHIFT_EVENING    => 'Vespertino'
      );

}
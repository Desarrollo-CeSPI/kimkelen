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

class WeekDays extends BaseCustomOptionsHolder
{

  const
    MONDAY     = 1,
    TUESDAY    = 2,
    WEDNESDAY  = 3,
    THURSDAY   = 4,
    FRIDAY     = 5,
    SATURDAY   = 6,
    SUNDAY     = 7;

  protected
    $_options = array(
        self::SUNDAY    => 'Domingo',
        self::MONDAY    => 'Lunes',
        self::TUESDAY   => 'Martes',
        self::WEDNESDAY => 'Miércoles',
        self::THURSDAY  => 'Jueves',
        self::FRIDAY    => 'Viernes',
        self::SATURDAY  => 'Sabado'
      );

}
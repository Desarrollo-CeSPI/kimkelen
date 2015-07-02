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
 * GuardGroups class
 *
 * @author cborre
 */
class GuardGroups extends BaseCustomOptionsHolder
{
  const
    ADMIN      = 1,
    PERSONAL   = 2,
    TEACHER    = 3,    
    HEAD_PERSONAL = 5,
    STUDENT_OFFICE = 6;


  protected
    $_options = array(
        self::ADMIN         => 'Administrador',
        self::HEAD_PERSONAL => 'Jefe de preceptores',
        self::PERSONAL      => 'Preceptor',
        self::TEACHER       => 'Profesor',        
        self::STUDENT_OFFICE => 'Oficina de alumnos'
      );
}
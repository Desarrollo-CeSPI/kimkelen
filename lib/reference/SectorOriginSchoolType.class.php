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

class SectorOriginSchoolType extends BaseCustomOptionsHolder
{

  const
    SECTOR_PUBLIC    = 0,
    SECTOR_PRIVATE   = 1,
    SECTOR_ACTIVE    = 2,
    SECTOR_SOCIAL    = 3,
    SECTOR_UNLP      = 4;
  protected
    $_options = array(
        self::SECTOR_PUBLIC   => 'Dirección General de Cultura y Educación',
        self::SECTOR_PRIVATE  => 'Dirección Provincial de Educación de Gestión Privada',
        self::SECTOR_ACTIVE   => '-',
        self::SECTOR_SOCIAL   => '-',
        self::SECTOR_UNLP     => 'Universidad Nacional de La Plata',
      );

}


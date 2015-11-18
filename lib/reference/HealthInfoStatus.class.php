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

class HealthInfoStatus extends BaseCustomOptionsHolder
{

  const
    HEALTH_INFO_NO_COMMITED = 'No entregado',
    HEALTH_INFO_COMMITED = 'Entregado',
    HEALTH_INFO_SUITABLE = 'Apta',
    HEALTH_INFO_NO_SUITABLE = 'No apta',
    HEALTH_INFO_OBSERVATIONS = 'Con observaciones';


  protected
    $_options = array(
        self::HEALTH_INFO_NO_COMMITED   => 'No entregado',
        self::HEALTH_INFO_COMMITED  	=> 'Entregado',
        self::HEALTH_INFO_SUITABLE  	=> 'Apta',
        self::HEALTH_INFO_NO_SUITABLE 	=> 'No apta',
        self::HEALTH_INFO_OBSERVATIONS	=> 'Con observaciones'
      );

}

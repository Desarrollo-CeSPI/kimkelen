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

class MedicalCertificateStatus extends BaseCustomOptionsHolder
{
    const
    VALIDATED       		= 1,
    NOT_VALIDATED       		= 2,
    CANCELLED                       = 3,
    INITIATED       		= 4,
    HEALTH_DIRECTION                = 5,
    IN_PROCESS                      = 6,
    OUT_OF_TERM                     = 7;
    
    protected
        $_options = array(
        self::VALIDATED       		 => 'Convalidado',
        self::NOT_VALIDATED       	 => 'No convalidado',
        self::CANCELLED      		 => 'Fuera de término',
        self::INITIATED 	         => 'Iniciado (Departamento de ALumnos)',
        self::HEALTH_DIRECTION		 => 'En la Dirección de Salud',
        self::IN_PROCESS                 => 'En proceso de convalidación',
        self::OUT_OF_TERM                => 'Fuera de término'
        
    );
  public function getOptionsForStatus($status)
  {
      
      switch($status){
			
        case self::NOT_VALIDATED:
                return array(
                        ""          => "",
                    self::NOT_VALIDATED       		 => 'No convalidado',
                    self::CANCELLED       		 => 'Cancelado',
                    self::VALIDATED       		 => 'Convalidado',
                    self::OUT_OF_TERM                    => 'Fuera de término'
                  );
        break;
        case self::INITIATED:
            return array(
                    ""          => "",
                    self::INITIATED       		 => 'Iniciado (Departamento de ALumnos)',
                    self::CANCELLED       		 => 'Cancelado',
                    self::HEALTH_DIRECTION       	 => 'En la Dirección de Salud',
                    self::OUT_OF_TERM                => 'Fuera de término'
                  );
        break;
        case self::HEALTH_DIRECTION:
            return array(
                    ""          => "",
                    self::HEALTH_DIRECTION       	 => 'En la Dirección de Salud',
                    self::CANCELLED       		 => 'Cancelado',
                    self::IN_PROCESS       	         => 'En proceso de convalidación',
                    self::OUT_OF_TERM                    => 'Fuera de término'
                  );
        break;
        case self::IN_PROCESS:
            return array(
                    ""          => "",
                    self::IN_PROCESS       	         => 'En proceso de convalidación',
                    self::CANCELLED       		 => 'Cancelado',
                    self::VALIDATED       		 => 'Convalidado',
                    self::NOT_VALIDATED       		 => 'No convalidado',
                    self::OUT_OF_TERM                    => 'Fuera de término'
                  );
        break;
        default:
                return array(
                    ""          => "",
                    self::INITIATED       		 => 'Iniciado (Departamento de ALumnos)', 
                    self::OUT_OF_TERM                    => 'Fuera de término'
                );
        break;
			
    }    
  }
}

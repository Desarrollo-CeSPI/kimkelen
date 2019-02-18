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
class OccupationType extends BaseCustomOptionsHolder
{
  const
    ENSEÑANZA = 5,
    SALUD = 18,
    PROFESIONAL_INDEPENDIENTE = 23,
    COMERCIO = 3,
    SERV_PUBLICOS = 24,
    SERV_DOMICILIARIOS = 25,
    CONSTRUCCION = 26,
    INDUSTRIA = 27,
    PRODUCCION_PRIMARIA =1,
    OTROS = 28;      
 
    public function getOccupation($occupation)
    {
        switch($occupation){

            case 1:
                return self::ENSEÑANZA;
                break;

            case 2:
                return self::SALUD;
            break;

            case 3:
                return self::PROFESIONAL_INDEPENDIENTE;
                break;

            case 4:
                return self::COMERCIO;
                break;

            case 5:
                return self::SERV_PUBLICOS;
                break;

            case 6:
                return self::SERV_DOMICILIARIOS;
                break;

            case 7:
                return self::CONSTRUCCION;
                break;

            case 8:
                return self::INDUSTRIA;
                break;

            case 9:
                return self::PRODUCCION_PRIMARIA;
                break;

            case 10:
                return self::OTROS;
                break;

            default:
                return null;
                break;
        } 
    }
}

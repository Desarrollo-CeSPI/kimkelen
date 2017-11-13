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
class OccupationCategoryType extends BaseCustomOptionsHolder
{
  const
    EMP_PUBLICO = 21,
    EMP_PRIVADO = 22,
    PROFESIONAL_INDEPENDIENTE = 23,
    TRABAJADOR_CP = 7,
    EMP_HASTA_5 = 8,
    EMP_MAS_5 = 9,
    SERV_DOMESTICO = 15,
    COOPERATIVISTA = 24,
    OTROS = 25;

    public function getOccupationCategory($occupation_c)
    {
        switch($occupation_c){
            
            case 1:
                return self::EMP_PUBLICO;
                break;

            case 2:
                return self::EMP_PRIVADO;
            break;

            case 3:
                return self::PROFESIONAL_INDEPENDIENTE;
                break;

            case 4:
                return self::TRABAJADOR_CP;
                break;

            case 5:
                return self::EMP_HASTA_5;
                break;

            case 6:
                return self::EMP_MAS_5;
                break;

            case 7:
                return self::SERV_DOMESTICO;
                break;

            case 8:
                return self::COOPERATIVISTA;
                break;

            case 9:
                return self::OTROS;
                break;

            default:
                return null;
                break;
        } 
    }
}

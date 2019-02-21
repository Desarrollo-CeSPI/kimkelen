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

class City extends BaseCity
{
  const LA_PLATA = 1159;
  const BUENOS_AIRES = 1;
  const CORDOBA = 10460;

  public function __toString(){
    $array = explode(chr(32),$this->getName());
    $chain = '';
    for ($i=0;$i<count($array);$i++)
    {
      $chain .= ucfirst($array[$i]).' ';
    }
    return ucfirst($chain);
  }
  
  public function getState()
  {
      return $this->getDepartment()->getState();
  }
}

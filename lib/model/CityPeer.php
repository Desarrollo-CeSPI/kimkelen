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

class CityPeer extends BaseCityPeer
{
  public static function getOptionsForSelect($c = null)
  {
    if(is_null($c)){
      $c = new Criteria();
    }
    $cities = array();
    $cities_temp = CityPeer::doSelect($c);
    foreach($cities_temp as $city){
      $cities[$city->getId()] = $city->__toString();
    }
    return $cities;
  }
  
  public static function retrieveByDepartmentId($department_id)
	{
		$c = new Criteria();
		$c->add(self::DEPARTMENT_ID, $department_id);
		$c->addAscendingOrderByColumn('name');
		
		return self::doSelect($c);
	
	}
}

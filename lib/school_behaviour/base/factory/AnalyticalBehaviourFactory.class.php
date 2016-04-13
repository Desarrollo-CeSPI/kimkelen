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
 */


class AnalyticalBehaviourFactory
{

    private static $analyticals = array();
    
    
    private function __construct()
    {
        
    }

    

    static public function getInstance(Student $a_student)
    {  
        if (isset(self::$analyticals[$a_student->getId()]))
        {
            return self::$analyticals[$a_student->getId()];
        }
        
        $behavior = ucwords(sfConfig::get("nc_flavor_flavors_current", "demo"));
        $clazz = $behavior . "AnalyticalBehaviour";
		
		return self::$analyticals[$a_student->getId()] = new $clazz($a_student);
		    
    }
}

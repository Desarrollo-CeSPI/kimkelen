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
 * SubjectType
 *
 * @author ncuesta
 */
class SubjectType extends BaseCustomOptionsHolder
{
  const
    TYPE_ANNUAL        = 12,
    TYPE_FOUR_MONTHLY  = 4,
    TYPE_THREE_MONTHLY = 3;

  protected
    $_options = array(
        self::TYPE_ANNUAL      => 'Anual',
        self::TYPE_FOUR_MONTHLY => 'Cuatrimestral',
        self::TYPE_THREE_MONTHLY => 'Trimestral'
      );

  static public function toString($type_id)
  {
    $subject_type = new SubjectType();
    
    return $subject_type->getStringFor($type_id);
  }
}
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

class ApprovalMethod extends BaseCustomOptionsHolder
{
  const
    METHOD_REGULAR      = 1,
    METHOD_PROMOTION    = 2,
    METHOD_CHALLENGE    = 3,
    METHOD_EQUIVALENCE  = 4;

  const
    MIN_MARK_TO_PASS_REGULAR   = 4,
    MIN_MARK_TO_PASS_PROMOTION = 4,
    MIN_MARK_TO_PASS_CHALLENGE = 4;

  protected
    $_options = array(
        self::METHOD_REGULAR    => 'Regular',
        self::METHOD_PROMOTION  => 'Por promoción',
        self::METHOD_CHALLENGE  => 'Libre'
      );

  public function getStringFor($key, $default_value = null)
  {
    if($key == self::METHOD_EQUIVALENCE) return 'Equivalencia';
    else return parent::getStringFor($key, $default_value);
  }

  public function itsApproved($mark, $approval_method)
  {
    switch($approval_method){
      case self::METHOD_REGULAR:
        return ($mark >= self::MIN_MARK_TO_PASS_REGULAR);
      case self::METHOD_PROMOTION:
        return ($mark >= self::MIN_MARK_TO_PASS_PROMOTION);
      case self::METHOD_CHALLENGE:
        return ($mark >= self::MIN_MARK_TO_PASS_CHALLENGE);
      case self::METHOD_EQUIVALENCE:
        return true;
    }
  }

}
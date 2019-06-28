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

class PersonPeer extends BasePersonPeer
{

  /**
   * Overloaded because of PersonForm Unique validator.
   * Returns id when translation for person-id from FIELDNAME to PHPNAME is requested
   *
   * @see PersonFrom
   * @see BaseFormPropel
   * @see parent::translateFieldName
   */
  public static function translateFieldName($name, $fromType, $toType)
  {
    if (($fromType == BasePeer::TYPE_FIELDNAME) &&
      ($toType == BasePeer::TYPE_PHPNAME) && $name == 'person-id')
    {
      return 'id';
    }
    return parent::translateFieldName($name, $fromType, $toType);

  }

  static public function doSelectOrderedCriteria(Criteria $criteria, PropelPDO $con = null)
  {
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    $criteria->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);
    return $criteria;

  }

  static public function retrieveBySfGuardUser(sfGuardUser $sf_guard_user)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $sf_guard_user->getId());

    return self::doSelectOne($c);

  }

  static public function doSelectStudent($c)
  {
    $c->add(self::IS_ACTIVE, true);
    $c->addJoin(self::ID, StudentPeer::PERSON_ID);
    return self::doSelect($c);

  }
  static public function doSelectTeacher($c)
  {
    $c->add(self::IS_ACTIVE, true);
    $c->addJoin(self::ID, TeacherPeer::PERSON_ID);
    return self::doSelect($c);

  }
  static public function doSelectPreceptor($c)
  {
    $c->add(self::IS_ACTIVE, true);
    $c->addJoin(self::ID, PersonalPeer::PERSON_ID);
    return self::doSelect($c);

  }
  
  public static function retrieveByDocumentTypeAndNumber($document_type,$document_number)
  {
    $c = new Criteria();
    $c->add(self::IDENTIFICATION_NUMBER, $document_number);
    $c->add(self::IDENTIFICATION_TYPE, $document_type);
    $p = self::doSelectOne($c);

    return $p;
  }

}
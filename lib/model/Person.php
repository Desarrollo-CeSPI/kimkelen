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

class Person extends BasePerson
{

  /**
   * This method implements __toString method to print the object
   *
   * @return string;
   */
  public function __toString()
  {
    return $this->getFullName();

  }

  /**
   * Returns lastname, firstname of the person
   *
   * @return string
   */
  public function getFullName()
  {
    return $this->getLastname() . ', ' . $this->getFirstname();

  }

  /**
   * Appends Identification type and identification numbers
   *
   * @return string
   */
  public function getFullIdentification()
  {
    return sprintf("%s %s", $this->getIdentificationTypeString(), $this->getIdentificationNumber()
    );

  }

  /**
   * Returns the Identification type string
   *
   * @return string
   */
  public function getIdentificationTypeString()
  {
    return BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($this->getIdentificationType());

  }

  /**
   * Return the string representation for the birth country
   *
   * @return string
   */
  public function getBirthCountryRepresentation()
  {
    $country = CountryPeer::retrieveByPK($this->getBirthCountry());
    if ($country)
      return $country->getName();

  }




  /**
   * Returns if this person can be set to active. This will be only when is not active
   * @return boolean
   */
  public function canBeActivated()
  {
    return $this->getIsActive() == false;

  }


  /**
   * Returns if this person can be set to inactive. This will be only when is not active
   * @return boolean
   */
  public function canBeDeactivated()
  {
    return $this->getIsActive() && ($this->getStudent())?$this->getStudent()->canBeDeactivated():true;

  }

  /**
   * Returns the directory for the persons photos
   * @return String
   */
  public static function getPhotoDirectory()
  {
    return sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'persons-photos';

  }

  /**
   * Returns the person photo full path
   * @return String
   */
  public function getPhotoFullPath()
  {
    return self::getPhotoDirectory() . DIRECTORY_SEPARATOR . $this->getPhoto();

  }

  /**
   * Delete a person
   * Also deletes the person user and photo
   * @param PropelPDO $con
   */
  public function delete(PropelPDO $con = null)
  {
    $photo_path = $this->getPhotoFullPath();
    parent::delete($con);
    if ($this->getUserId()) {
      $user = sfGuardUserPeer::retrieveByPK($this->getUserId());
      $user->delete($con);
    }
    $this->deletePhysicalImage($photo_path);

  }

  public function deletePhysicalImage($photo_path)
  {
    if (file_exists($photo_path))
      unlink($photo_path);
  }

  public function deleteImage()
  {
    $this->deletePhysicalImage($this->getPhotoFullPath());
    $this->setPhoto('');
    $this->save();

  }

  public function getIsInLicense()
  {
    $c = new Criteria();
    $c->add(LicensePeer::PERSON_ID, $this->getId());
    $c->add(LicensePeer::IS_ACTIVE, true);

    return (LicensePeer::doCount($c) != 0);

  }

  public function changeGuardUserActivation()
  {
    $guard_user = SfGuardUserPeer::retrieveByPk($this->getUserId());
    $guard_user->setIsActive($this->getIsActive())->save();

  }

  public function getStudent()
  {
    $c = new Criteria();
    $c->add(StudentPeer::PERSON_ID, $this->getId());
    return StudentPeer::doSelectOne($c);

  }

   public function getIsActiveString()
  {
    return $this->getIsActive()? 'Sí': 'No';
  }

   public function getFormattedBirthDate()
  {
    return $this->getBirthdate('d-m-Y');
  }


  public function getBirthState()
  {
    if (is_null($this->getCity())) return null;
    return $this->getCity()->getDepartment()->getState();
  }

  public function getBirthCountry()
  {
    if (is_null($this->getCity())) return null;
    return $this->getCity()->getDepartment()->getState()->getCountry();
  }
}

sfPropelBehavior::add('Person', array('changelog'));
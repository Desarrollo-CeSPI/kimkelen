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

class myUser extends sfGuardSecurityUser
{

	static $reference_key = "students.referrer.from.%s.key";

	/**
	 * This method is intended for be used inside sfActions subclasess so when
	 * user access some action from that module, and this method is called, we can
	 * get track of some ID for sfActions module to be considered in future calls
	 * in other modules. For example if we are working with career and we want to
	 * view students registered in that career, we will set carrer id as referrer
	 *
	 * @param sfActions $action Action from which we are called. Attribute key will
	 * be extracted from $action->getModuleName
	 * @param <type> $override_route_object if $action->getRoute()->getObject()->getId()
	 * is not applicable, then provide which value is to be saved as referrer
	 * @param <type> $override_key if getModuleName is not enough, you could use this parameter
	 */
	public function setReferenceFor(sfActions $action, $override_route_object=false, $override_key=false )
	{
		$key = $override_key!==false? $override_key:$action->getModuleName();
		$id = $override_route_object!==false? $override_route_object: $action->getRoute()->getObject()->getId();
		return $this->setAttribute(sprintf(self::$reference_key,$key),$id);
	}

	/**
	 * Companion method for setReferenceFor. This method will return the attribute
	 * stored with setReferenceFor, so access to module context object will be posible
	 *
	 * @param string $module_name name of the override_key / module used when set id
	 * @return int  Associated id
	 */
	public function getReferenceFor($module_name)
	{
		return $this->getAttribute(sprintf(self::$reference_key,$module_name));
	}

	public function removeReferenceFor($module_name)
	{
		$this->getAttributeHolder()->remove(sprintf(self::$reference_key,$module_name));
	}
	
	public function setFacebookId($fb_id) {
		$this->setAttribute('facebook_id', $fb_id);
        }

        public function getFacebookId() {
            return $this->getAttribute('facebook_id');
        }

        public function getFacebookName() {
            return $this->getAttribute('facebook_name', '');
        }

        public function setFacebookName($fb_name) {
            $this->setAttribute('facebook_name', $fb_name);
        }

        public function resetFacebookAttributes()
        {
            $this->getAttributeHolder()->remove('facebook_id');
            $this->getAttributeHolder()->remove('facebook_name');
            $this->getAttributeHolder()->remove('facebook_state');
        }

        public function getFacebookState() {
            return $this->getAttribute('facebook_state');
        }

        public function setFacebookState($fb_state) {
            $this->setAttribute('facebook_state', $fb_state);
        }

}

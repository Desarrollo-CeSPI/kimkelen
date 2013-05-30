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
 * Address form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class AddressForm extends BaseAddressForm
{
  public function configure()
  {
    //Widgets Configuration
    $c = new Criteria();
    $c->addAscendingOrderByColumn('name');

    $widget_birth_city = new sfWidgetFormPropelChoice(array(
      'model'      => 'City',
      'add_empty'  => true,
      'criteria'   => $c
    ));

    $related_class= $this->getOption('related_class');

    if ( empty($related_class) ) throw new LogicException (get_class($this).": Can't be used without related_class option setted. SEE README of this classs!");

    $this->widgetSchema['city_id'] = new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'state_id',
        'dependant_widget'   => $widget_birth_city,
        'observe_widget_id'  => $related_class.'address_state_id',
        'message_with_no_value' => 'Seleccione primero la provincia',
        ));

    //Configuración de widgets
    $this->setDefault('state_id',  SchoolBehaviourFactory::getInstance()->getDefaultStateId());
    $this->setDefault('city_id',  SchoolBehaviourFactory::getInstance()->getDefaultCityId());

    $this->getWidgetSchema()->setLabel('state_id', 'State');
    $this->getWidgetSchema()->setLabel('city_id', 'City');
  }
}
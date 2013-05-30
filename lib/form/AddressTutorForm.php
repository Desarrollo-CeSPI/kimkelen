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
 * AddressTutor form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     tcordoba at cespi.unlp.edu.ar
 */

class AddressTutorForm extends BaseAddressForm
{
  public function configure()
  {
    //Widgets Configuration
    $c = new Criteria();
    $c->addAscendingOrderByColumn('name');
    
    $widget = new sfWidgetFormPropelChoice(array(
      'model'      => 'City',
      'add_empty'  => true,
      'criteria' => $c,
    ));

    $this->widgetSchema['city_id'] = new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'state_id',
        'dependant_widget'   => $widget,
        'observe_widget_id'  => 'tutor_address_state_id',
        'message_with_no_value' => 'Seleccione una provincia para visualizar',
    ));

    //Configuración de widgets

    $this->widgetSchema['city_id']->setLabel('Ciudad');
    $this->widgetSchema['state_id']->setLabel('Provincia');
    $this->setDefault('state_id',"2");
    $this->setDefault('city_id',"408");

  }
}
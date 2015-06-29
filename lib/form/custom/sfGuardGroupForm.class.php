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
 * sfGuardGroup form.
 *
 * @package    form
 * @subpackage sf_guard_group
 * @version    SVN: $Id: sfGuardGroupForm.class.php 12896 2008-11-10 19:02:34Z fabien $
 */
class sfGuardGroupForm extends BasesfGuardGroupForm
{

  public function configure()
  {
    unset($this['sf_guard_user_group_list']);

    $this->setWidget('sf_guard_group_permission_list', new sfWidgetFormSelectDoubleList(array('choices' => sfGuardPermissionPeer::getChoices(),
                                                                                              'label_unassociated' => 'No seleccionados',
                                                                                              'label_associated' => 'Seleccionados',
                                                                                              'associate'=>'<img src="../../../sfFormExtraPlugin/images/next.png" alt="Seleccionar" />',
                                                                                              'unassociate'=> '<img src="../../../sfFormExtraPlugin/images/previous.png" alt="Desseleccionar" />')));
    $this->widgetSchema['sf_guard_group_permission_list']->setLabel('Permissions');

  }

}